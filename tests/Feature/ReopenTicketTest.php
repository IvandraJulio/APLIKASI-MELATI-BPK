<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReopenTicketTest extends TestCase
{
    use RefreshDatabase;

    private $pengguna;
    private $solver1;
    private $solver2;
    private $kasubbag;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->pengguna = User::create([
            'id' => 'usr-pengguna',
            'name' => 'Pengguna Test',
            'username' => 'pengguna',
            'email' => 'pengguna@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);

        $this->solver1 = User::create([
            'id' => 'usr-solver1',
            'name' => 'Solver 1',
            'username' => 'solver1',
            'email' => 'solver1@test.com',
            'password' => bcrypt('password'),
            'role' => 'solver',
        ]);

        $this->solver2 = User::create([
            'id' => 'usr-solver2',
            'name' => 'Solver 2',
            'username' => 'solver2',
            'email' => 'solver2@test.com',
            'password' => bcrypt('password'),
            'role' => 'solver',
        ]);

        $this->kasubbag = User::create([
            'id' => 'usr-kasubbag',
            'name' => 'Kasubbag Test',
            'username' => 'kasubbag',
            'email' => 'kasubbag@test.com',
            'password' => bcrypt('password'),
            'role' => 'kasubbag',
            'subbagId' => 'subbag-1',
        ]);
    }

    public function test_reopen_ticket_notifies_both_solvers_if_both_assigned()
    {
        // Create completed ticket
        $ticket = Ticket::create([
            'id' => 'tkt-123',
            'pengirimId' => $this->pengguna->id,
            'pengirimName' => $this->pengguna->name,
            'jenis' => 'Layanan',
            'layananKategori' => 'Kategori Test',
            'layananSub' => 'Sub Test',
            'layanan' => 'Layanan Test',
            'detail' => 'Detail Test',
            'tanggal' => date('Y-m-d'),
            'tanggalUpdate' => date('Y-m-d H:i'),
            'status' => 'Selesai',
            'solverId' => $this->solver1->id,
            'solverName' => $this->solver1->name,
            'solver2Id' => $this->solver2->id,
            'solver2Name' => $this->solver2->name,
            'tanggalSelesai' => date('Y-m-d H:i:s'),
            'kasubbagId' => 'subbag-1',
            'kasubbagName' => 'Kasubbag Test',
        ]);

        // Act as user and reopen ticket
        $response = $this->actingAs($this->pengguna)
            ->postJson("/api/tickets/{$ticket->id}/actions", [
                'action' => 'reopen'
            ]);

        $response->assertStatus(200);

        // Verify status is changed to 'Dikerjakan'
        $ticket->refresh();
        $this->assertEquals('Dikerjakan', $ticket->status);

        // Verify notifications are sent to solver1 and solver2, but NOT to kasubbag
        $notifications = Notification::where('ticket_id', $ticket->id)->get();
        $this->assertCount(2, $notifications);

        $notifiedUserIds = $notifications->pluck('user_id')->toArray();
        $this->assertContains($this->solver1->id, $notifiedUserIds);
        $this->assertContains($this->solver2->id, $notifiedUserIds);
        $this->assertNotContains($this->kasubbag->id, $notifiedUserIds);
    }

    public function test_reopen_ticket_notifies_only_one_solver_if_only_one_assigned()
    {
        // Create completed ticket
        $ticket = Ticket::create([
            'id' => 'tkt-124',
            'pengirimId' => $this->pengguna->id,
            'pengirimName' => $this->pengguna->name,
            'jenis' => 'Layanan',
            'layananKategori' => 'Kategori Test',
            'layananSub' => 'Sub Test',
            'layanan' => 'Layanan Test',
            'detail' => 'Detail Test',
            'tanggal' => date('Y-m-d'),
            'tanggalUpdate' => date('Y-m-d H:i'),
            'status' => 'Selesai',
            'solverId' => $this->solver1->id,
            'solverName' => $this->solver1->name,
            'solver2Id' => null,
            'solver2Name' => null,
            'tanggalSelesai' => date('Y-m-d H:i:s'),
            'kasubbagId' => 'subbag-1',
            'kasubbagName' => 'Kasubbag Test',
        ]);

        // Act as user and reopen ticket
        $response = $this->actingAs($this->pengguna)
            ->postJson("/api/tickets/{$ticket->id}/actions", [
                'action' => 'reopen'
            ]);

        $response->assertStatus(200);

        // Verify status is changed to 'Dikerjakan'
        $ticket->refresh();
        $this->assertEquals('Dikerjakan', $ticket->status);

        // Verify notification is sent only to solver1
        $notifications = Notification::where('ticket_id', $ticket->id)->get();
        $this->assertCount(1, $notifications);

        $notifiedUserIds = $notifications->pluck('user_id')->toArray();
        $this->assertContains($this->solver1->id, $notifiedUserIds);
        $this->assertNotContains($this->solver2->id, $notifiedUserIds);
        $this->assertNotContains($this->kasubbag->id, $notifiedUserIds);
    }

    public function test_reopen_ticket_notifies_kasubbag_if_no_solvers_assigned()
    {
        // Create completed ticket
        $ticket = Ticket::create([
            'id' => 'tkt-125',
            'pengirimId' => $this->pengguna->id,
            'pengirimName' => $this->pengguna->name,
            'jenis' => 'Layanan',
            'layananKategori' => 'Kategori Test',
            'layananSub' => 'Sub Test',
            'layanan' => 'Layanan Test',
            'detail' => 'Detail Test',
            'tanggal' => date('Y-m-d'),
            'tanggalUpdate' => date('Y-m-d H:i'),
            'status' => 'Selesai',
            'solverId' => null,
            'solverName' => null,
            'solver2Id' => null,
            'solver2Name' => null,
            'tanggalSelesai' => date('Y-m-d H:i:s'),
            'kasubbagId' => 'subbag-1',
            'kasubbagName' => 'Kasubbag Test',
        ]);

        // Act as user and reopen ticket
        $response = $this->actingAs($this->pengguna)
            ->postJson("/api/tickets/{$ticket->id}/actions", [
                'action' => 'reopen'
            ]);

        $response->assertStatus(200);

        // Verify status is changed to 'Pending' since no solvers are assigned
        $ticket->refresh();
        $this->assertEquals('Pending', $ticket->status);

        // Verify notification is sent to Kasubbag
        $notifications = Notification::where('ticket_id', $ticket->id)->get();
        $this->assertCount(1, $notifications);

        $notifiedUserIds = $notifications->pluck('user_id')->toArray();
        $this->assertContains($this->kasubbag->id, $notifiedUserIds);
        $this->assertNotContains($this->solver1->id, $notifiedUserIds);
        $this->assertNotContains($this->solver2->id, $notifiedUserIds);
    }
}
