<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EscalateOverdueTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:escalate-overdue-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eskalasi otomatis tiket berstatus Pending yang tidak ditindaklanjuti oleh Kasubbag dalam 24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = date('Y-m-d H:i');
        $cutoffTime = now()->subDay();

        $overdueTickets = \App\Models\Ticket::where('status', 'Pending')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $this->info("Menemukan " . $overdueTickets->count() . " tiket Pending yang melebihi 24 jam.");

        foreach ($overdueTickets as $ticket) {
            $ticket->update([
                'status' => 'Overdue',
                'tanggalUpdate' => $now,
            ]);

            // Tambah log comment bertipe 'overdue'
            \App\Models\Comment::create([
                'id' => 'cmt-sys-esc-' . microtime(true),
                'ticketId' => $ticket->id,
                'authorId' => 'system',
                'authorName' => 'Sistem Otomatis',
                'authorRole' => 'operator', // system/operator
                'text' => 'Tiket otomatis diubah statusnya menjadi Overdue karena tidak ada tindak lanjut dari Kasubbag selama lebih dari 24 jam.',
                'timestamp' => $now,
                'type' => 'overdue',
            ]);

            // Kirim notifikasi ke Kasubbag terkait
            if (!empty($ticket->kasubbagId)) {
                $kasubbags = \App\Models\User::where('role', 'kasubbag')
                    ->where('subbagId', $ticket->kasubbagId)
                    ->get();
                foreach ($kasubbags as $kb) {
                    \App\Models\Notification::create([
                        'user_id' => $kb->id,
                        'ticket_id' => $ticket->id,
                        'title' => 'Tiket Overdue',
                        'message' => "Tiket {$ticket->id} ({$ticket->layanan}) telah otomatis ditandai Overdue karena melebihi batas waktu 24 jam di antrean Anda.",
                    ]);
                }
            }

            // Kirim notifikasi ke Operator
            $operators = \App\Models\User::where('role', 'operator')->get();
            foreach ($operators as $op) {
                \App\Models\Notification::create([
                    'user_id' => $op->id,
                    'ticket_id' => $ticket->id,
                    'title' => 'Tiket Overdue',
                    'message' => "Tiket {$ticket->id} ({$ticket->layanan}) otomatis ditandai Overdue karena melebihi batas waktu 24 jam di subbagian Kasubbag.",
                ]);
            }

            $this->line("Tiket {$ticket->id} berhasil diubah ke status Overdue.");
        }

        $this->info("Proses penandaan overdue selesai.");
    }
}
