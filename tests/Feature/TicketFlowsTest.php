<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed users
        User::create([
            'id' => 'u_pusat',
            'name' => 'Budi Pusat',
            'username' => 'budipusat',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
            'lokasi' => 'Kantor Pusat',
        ]);

        User::create([
            'id' => 'u_perwakilan',
            'name' => 'Ahmad Perwakilan',
            'username' => 'ahmadperwakilan',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
            'lokasi' => 'Kantor Perwakilan',
        ]);

        User::create([
            'id' => 'k_pelayanan',
            'name' => 'Wulandari Pelayanan',
            'username' => 'wulandari',
            'password' => bcrypt('password'),
            'role' => 'kasubbag',
            'subbagId' => 'k2',
            'lokasi' => 'Kantor Pusat',
        ]);

        User::create([
            'id' => 'k_plti',
            'name' => 'Andi PLTI',
            'username' => 'andiplti',
            'password' => bcrypt('password'),
            'role' => 'kasubbag',
            'subbagId' => 'plti',
            'lokasi' => 'Kantor Perwakilan',
        ]);

        User::create([
            'id' => 'solver_1',
            'name' => 'Solver 1',
            'username' => 'solver1',
            'password' => bcrypt('password'),
            'role' => 'solver',
            'subbagId' => 'k2',
            'lokasi' => 'Kantor Pusat',
        ]);
        
        User::create([
            'id' => 'solver_2',
            'name' => 'Solver 2',
            'username' => 'solver2',
            'password' => bcrypt('password'),
            'role' => 'solver',
            'subbagId' => 'k2',
            'lokasi' => 'Kantor Pusat',
        ]);
    }

    /**
     * Test routing for Head Office (Kantor Pusat) users.
     */
    public function test_routing_head_office_user()
    {
        $user = User::find('u_pusat');
        $this->actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'layananKategori' => 'Layanan Identitas',
            'layananSub' => 'Layanan Akun',
            'layanan' => 'Reset Password / Masalah Login',
            'detail' => 'Lupa password AD login portal.',
            'bisa_remote' => false, // Head office is routed normally even if remote is false
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $ticketId = $response->json('id');
        $ticket = Ticket::find($ticketId);

        $this->assertNotNull($ticket);
        $this->assertEquals('k2', $ticket->kasubbagId); // Routed to Subbag Pelayanan (k2)
        $this->assertFalse((bool)$ticket->bisa_remote);
    }

    /**
     * Test routing for Representative (Kantor Perwakilan) users with Remote = true.
     */
    public function test_routing_representative_user_with_remote_true()
    {
        $user = User::find('u_perwakilan');
        $this->actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'layananKategori' => 'Layanan Identitas',
            'layananSub' => 'Layanan Akun',
            'layanan' => 'Reset Password / Masalah Login',
            'detail' => 'Lupa password AD login portal.',
            'bisa_remote' => true, // Can be solved remotely -> routed to Head Office (k2)
        ]);

        $response->assertStatus(200);

        $ticketId = $response->json('id');
        $ticket = Ticket::find($ticketId);

        $this->assertEquals('k2', $ticket->kasubbagId); // Routed to k2
        $this->assertTrue((bool)$ticket->bisa_remote);
    }

    /**
     * Test routing for Representative (Kantor Perwakilan) users with Remote = false.
     */
    public function test_routing_representative_user_with_remote_false()
    {
        $user = User::find('u_perwakilan');
        $this->actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'layananKategori' => 'Layanan Identitas',
            'layananSub' => 'Layanan Akun',
            'layanan' => 'Reset Password / Masalah Login',
            'detail' => 'Lupa password AD login portal.',
            'bisa_remote' => false, // Cannot be solved remotely -> routed to local PLTI
        ]);

        $response->assertStatus(200);

        $ticketId = $response->json('id');
        $ticket = Ticket::find($ticketId);

        $this->assertEquals('plti', $ticket->kasubbagId); // Routed to PLTI
        $this->assertFalse((bool)$ticket->bisa_remote);
    }

    /**
     * Test automated escalation for tickets pending for > 24 hours.
     */
    public function test_escalation_for_overdue_tickets()
    {
        // Create an overdue ticket
        $ticket = Ticket::create([
            'id' => 'TKT-TEST-999',
            'pengirimId' => 'u_pusat',
            'pengirimName' => 'Budi Pusat',
            'jenis' => 'Layanan',
            'layananKategori' => 'Layanan Identitas',
            'layananSub' => 'Layanan Akun',
            'layanan' => 'Reset Password',
            'detail' => 'Testing overdue.',
            'tanggal' => date('Y-m-d'),
            'tanggalUpdate' => date('Y-m-d H:i', strtotime('-2 days')),
            'kasubbagId' => 'k2',
            'status' => 'Pending',
        ]);
        
        // Manually force created_at to be older than 24 hours
        $ticket->created_at = now()->subDays(2);
        $ticket->save();

        $this->artisan('app:escalate-overdue-tickets')
            ->expectsOutput('Menemukan 1 tiket Pending yang melebihi 24 jam.')
            ->expectsOutput('Tiket TKT-TEST-999 berhasil diubah ke status Overdue.')
            ->assertExitCode(0);

        $ticket->refresh();
        $this->assertEquals('Overdue', $ticket->status);

        // Verify log comment
        $comment = Comment::where('ticketId', 'TKT-TEST-999')
            ->where('type', 'overdue')
            ->first();
        $this->assertNotNull($comment);
        $this->assertStringContainsString('Tiket otomatis diubah statusnya menjadi Overdue', $comment->text);

        // Verify notifications sent to Kasubbag and Operator
        $notifications = Notification::where('ticket_id', 'TKT-TEST-999')->get();
        $this->assertTrue($notifications->count() > 0);
    }

    /**
     * Test reopen notifications for both solvers.
     */
    public function test_reopen_notifications_sent_to_both_solvers()
    {
        $user = User::find('u_pusat');
        $this->actingAs($user);

        // Create completed ticket with solver1 and solver2
        $ticket = Ticket::create([
            'id' => 'TKT-TEST-888',
            'pengirimId' => 'u_pusat',
            'pengirimName' => 'Budi Pusat',
            'jenis' => 'Layanan',
            'layananKategori' => 'Layanan Identitas',
            'layananSub' => 'Layanan Akun',
            'layanan' => 'Reset Password',
            'detail' => 'Testing reopen.',
            'tanggal' => date('Y-m-d'),
            'tanggalUpdate' => date('Y-m-d H:i'),
            'tanggalSelesai' => date('Y-m-d H:i'),
            'kasubbagId' => 'k2',
            'solverId' => 'solver_1',
            'solverName' => 'Solver 1',
            'solver2Id' => 'solver_2',
            'solver2Name' => 'Solver 2',
            'status' => 'Selesai',
        ]);

        $response = $this->postJson('/api/tickets/TKT-TEST-888/actions', [
            'action' => 'reopen',
        ]);

        $response->assertStatus(200);

        $ticket->refresh();
        $this->assertEquals('Dikerjakan', $ticket->status);

        // Check notifications
        $notif1 = Notification::where('user_id', 'solver_1')->where('ticket_id', 'TKT-TEST-888')->first();
        $notif2 = Notification::where('user_id', 'solver_2')->where('ticket_id', 'TKT-TEST-888')->first();

        $this->assertNotNull($notif1);
        $this->assertNotNull($notif2);
        $this->assertEquals('Tiket Dibuka Kembali', $notif1->title);
        $this->assertEquals('Tiket Dibuka Kembali', $notif2->title);
    }
}
