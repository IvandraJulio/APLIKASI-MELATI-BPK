<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (with hashed passwords)
        $users = [
            ['id' => 'u1', 'name' => 'Budi Santoso', 'username' => 'budi', 'password' => 'budi123', 'role' => 'pengguna', 'lokasi' => 'Kantor Pusat', 'subbagId' => null],
            ['id' => 'u2', 'name' => 'Siti Rahayu', 'username' => 'siti', 'password' => 'siti123', 'role' => 'pengguna', 'lokasi' => 'Kantor Pusat', 'subbagId' => null],
            ['id' => 'u3', 'name' => 'Ahmad Fauzi', 'username' => 'ahmad', 'password' => 'ahmad123', 'role' => 'pengguna', 'lokasi' => 'Kantor Perwakilan', 'subbagId' => null],
            ['id' => 'u4', 'name' => 'Dewi Kusuma', 'username' => 'dewi', 'password' => 'dewi123', 'role' => 'pengguna', 'lokasi' => 'Kantor Perwakilan', 'subbagId' => null],
            
            ['id' => 'k1', 'name' => 'Ir. Hartono, M.T.', 'username' => 'kasubbag.infrastruktur', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k1'],
            ['id' => 'k2', 'name' => 'Dra. Wulandari, M.Si.', 'username' => 'kasubbag.pelayanan', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k2'],
            ['id' => 'k3', 'name' => 'Rizal Pratama, S.T.', 'username' => 'kasubbag.si.pemeriksaan', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k3'],
            ['id' => 'k4', 'name' => 'Hendra Gunawan, S.Kom.', 'username' => 'kasubbag.si.kelembagaan', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k4'],
            ['id' => 'k5', 'name' => 'Dr. Nuraini, M.Sc.', 'username' => 'kasubbag.sains.data', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k5'],
            ['id' => 'k6', 'name' => 'Bambang Susilo, S.Kom.', 'username' => 'kasubbag.tata.kelola', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k6'],
            ['id' => 'k7', 'name' => 'Rina Marliani, M.M.', 'username' => 'kasubbag.keamanan', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k7'],
            ['id' => 'k8', 'name' => 'Teguh Prasetyo, S.T.', 'username' => 'kasubbag.miot', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k8'],
            ['id' => 'k_plti', 'name' => 'Andi Wijaya, S.Kom (Kasubbag PLTI)', 'username' => 'kasubbag.plti', 'password' => 'pass123', 'role' => 'kasubbag', 'lokasi' => 'Kantor Perwakilan', 'subbagId' => 'plti'],

            ['id' => 's1_1', 'name' => 'Supriyadi (Infra Solver 1)', 'username' => 'solver.infra.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k1'],
            ['id' => 's1_2', 'name' => 'Aris Nugroho (Infra Solver 2)', 'username' => 'solver.infra.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k1'],
            ['id' => 's1_3', 'name' => 'Dimas Saputra (Infra Solver 3)', 'username' => 'solver.infra.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k1'],
            ['id' => 's2_1', 'name' => 'Farah Amalia (TIK Solver 1)', 'username' => 'solver.tik.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k2'],
            ['id' => 's2_2', 'name' => 'Bayu Anggara (TIK Solver 2)', 'username' => 'solver.tik.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k2'],
            ['id' => 's2_3', 'name' => 'Sonia Fitri (TIK Solver 3)', 'username' => 'solver.tik.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k2'],
            ['id' => 's3_1', 'name' => 'Deni Ardiansyah (SIM-P Solver 1)', 'username' => 'solver.sim.p1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k3'],
            ['id' => 's3_2', 'name' => 'Eko Prasetyo (SIM-P Solver 2)', 'username' => 'solver.sim.p2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k3'],
            ['id' => 's3_3', 'name' => 'Lilis Handayani (SIM-P Solver 3)', 'username' => 'solver.sim.p3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k3'],
            ['id' => 's4_1', 'name' => 'Wawan Hermawan (SIM-K Solver 1)', 'username' => 'solver.sim.k1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k4'],
            ['id' => 's4_2', 'name' => 'Fitriani (SIM-K Solver 2)', 'username' => 'solver.sim.k2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k4'],
            ['id' => 's4_3', 'name' => 'Aditya Pratama (SIM-K Solver 3)', 'username' => 'solver.sim.k3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k4'],
            ['id' => 's5_1', 'name' => 'Rian Setiawan (Sains Solver 1)', 'username' => 'solver.sains.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k5'],
            ['id' => 's5_2', 'name' => 'Kartika Sari (Sains Solver 2)', 'username' => 'solver.sains.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k5'],
            ['id' => 's5_3', 'name' => 'Andi Wijaya (Sains Solver 3)', 'username' => 'solver.sains.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k5'],
            ['id' => 's6_1', 'name' => 'Heri Susanto (Tata Kelola Solver 1)', 'username' => 'solver.tata.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k6'],
            ['id' => 's6_2', 'name' => 'Melinda Putri (Tata Kelola Solver 2)', 'username' => 'solver.tata.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k6'],
            ['id' => 's6_3', 'name' => 'Yudi Darmawan (Tata Kelola Solver 3)', 'username' => 'solver.tata.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k6'],
            ['id' => 's7_1', 'name' => 'Angga Saputra (Sec Solver 1)', 'username' => 'solver.sec.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k7'],
            ['id' => 's7_2', 'name' => 'Diana Lestari (Sec Solver 2)', 'username' => 'solver.sec.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k7'],
            ['id' => 's7_3', 'name' => 'Rudi Hartono (Sec Solver 3)', 'username' => 'solver.sec.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k7'],
            ['id' => 's8_1', 'name' => 'Fajar Ramadan (MIOT Solver 1)', 'username' => 'solver.miot.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k8'],
            ['id' => 's8_2', 'name' => 'Indah Permata (MIOT Solver 2)', 'username' => 'solver.miot.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k8'],
            ['id' => 's8_3', 'name' => 'Agung Hidayat (MIOT Solver 3)', 'username' => 'solver.miot.3', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Pusat', 'subbagId' => 'k8'],
            ['id' => 's_plti_1', 'name' => 'Beni (PLTI Solver 1)', 'username' => 'solver.plti.1', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Perwakilan', 'subbagId' => 'plti'],
            ['id' => 's_plti_2', 'name' => 'Rini (PLTI Solver 2)', 'username' => 'solver.plti.2', 'password' => 'solver123', 'role' => 'solver', 'lokasi' => 'Kantor Perwakilan', 'subbagId' => 'plti'],

            ['id' => 'op1', 'name' => 'Operator TI Utama BPK', 'username' => 'admin', 'password' => 'admin123', 'role' => 'operator', 'lokasi' => 'Kantor Pusat', 'subbagId' => null],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['id' => $userData['id']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'lokasi' => $userData['lokasi'] ?? 'Kantor Pusat',
                    'subbagId' => $userData['subbagId'],
                ]
            );
        }

        // 2. Seed Default Tickets
        $tickets = [
            [
                'id' => 'TKT-2026-001',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Teknologi',
                'layananSub' => 'Layanan Intranet',
                'layanan' => 'Penyediaan kabel LAN',
                'detail' => 'Kabel LAN di ruang kerja lantai 3 Biro TI mengalami kerusakan (terkelupas/retak) sehingga koneksi internet sering terputus-putus secara tiba-tiba.',
                'tanggal' => '2026-06-25',
                'tanggalUpdate' => '2026-06-25 09:15',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Pending',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
            ],
            [
                'id' => 'TKT-2026-002',
                'pengirimId' => 'u2',
                'pengirimName' => 'Siti Rahayu',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Perangkat',
                'layananSub' => 'Pemeliharaan Perangkat',
                'layanan' => 'Pemeliharaan Perangkat',
                'detail' => 'Laptop operasional lambat sekali saat digunakan untuk menjalankan aplikasi audit BPK yang berukuran besar. Layar laptop juga berkedip-kedip saat digerakkan. Butuh pemeriksaan hardware menyeluruh.',
                'tanggal' => '2026-06-26',
                'tanggalUpdate' => '2026-06-26 14:30',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k2',
                'kasubbagName' => 'Dra. Wulandari, M.Si.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Diterima',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
            ],
            [
                'id' => 'TKT-2026-OVERDUE-DIRECT',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Perangkat',
                'layananSub' => 'Standarisasi Perangkat Komputer',
                'layanan' => 'Standarisasi Perangkat Komputer',
                'detail' => 'Laporan uji coba penanganan overdue langsung.',
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'tanggalUpdate' => date('Y-m-d H:i', strtotime('-2 days')),
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Overdue',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(26),
            ],
            [
                'id' => 'TKT-2026-OVERDUE-PENDING',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Perangkat',
                'layananSub' => 'Standarisasi Perangkat Komputer',
                'layanan' => 'Standarisasi Perangkat Komputer',
                'detail' => 'Laporan uji coba penandaan overdue otomatis via command. Jalankan php artisan app:escalate-overdue-tickets untuk menguji.',
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'tanggalUpdate' => date('Y-m-d H:i', strtotime('-2 days')),
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Pending',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(26),
            ],
        ];

        foreach ($tickets as $ticketData) {
            $created_at = $ticketData['created_at'] ?? now();
            unset($ticketData['created_at']);
            $t = Ticket::updateOrCreate(['id' => $ticketData['id']], $ticketData);
            $t->created_at = $created_at;
            $t->save();
        }

        // 3. Seed Default Comments
        $comments = [
            [
                'id' => 'c1_1',
                'ticketId' => 'TKT-2026-001',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Kabel LAN ini sangat penting karena kami sedang menyusun laporan konsolidasi nasional minggu ini.',
                'timestamp' => '2026-06-25 09:15',
                'type' => 'komentar',
            ],
            [
                'id' => 'c2_1',
                'ticketId' => 'TKT-2026-002',
                'authorId' => 'k2',
                'authorName' => 'Dra. Wulandari, M.Si.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket diterima.',
                'timestamp' => '2026-06-26 14:30',
                'type' => 'terima',
            ],
        ];

        foreach ($comments as $commentData) {
            Comment::updateOrCreate(['id' => $commentData['id']], $commentData);
        }

        // 4. Seed the 50 tickets from seed_50_tickets.sql
        $seedSqlPath = database_path('seeders/seed_50_tickets.sql');
        if (File::exists($seedSqlPath)) {
            $sqlContent = File::get($seedSqlPath);
            // Remove 'USE db_layanan_ti;'
            $sqlContent = preg_replace('/USE\s+db_layanan_ti\s*;/i', '', $sqlContent);
            // Replace double quotes with backticks for MySQL compatibility
            $sqlContent = str_replace('"', '`', $sqlContent);
            // Execute the raw query
            DB::unprepared($sqlContent);
        }

        // 5. Programmatically generate realistic comments for the newly seeded tickets
        $tickets = Ticket::where('id', '!=', 'TKT-2026-001')
            ->where('id', '!=', 'TKT-2026-002')
            ->get();

        $subbagMaster = [
            'k1' => 'Subbagian Pengelolaan Infrastruktur dan Jaringan',
            'k2' => 'Subbagian Pelayanan TIK',
            'k3' => 'Subbagian Pengembangan Sistem Informasi Pemeriksaan',
            'k4' => 'Subbagian Pengembangan Sistem Informasi Kelembagaan',
            'k5' => 'Subbagian Sains Data',
            'k6' => 'Subbagian Tata Kelola Data',
            'k7' => 'Subbagian Keamanan Informasi',
            'k8' => 'Subbagian MIOT',
            'plti' => 'Pusat Layanan Teknologi Informasi Perwakilan',
        ];

        foreach ($tickets as $ticket) {
            $subbagName = $subbagMaster[$ticket->kasubbagId] ?? 'Subbagian Pelayanan TIK';
            $now = $ticket->tanggalUpdate ?: date('Y-m-d H:i');

            // Time calculations to create a realistic flow of comments
            $submissionTime = $ticket->tanggal . ' 08:30';
            if (strtotime($now) < strtotime($submissionTime)) {
                $submissionTime = date('Y-m-d H:i', strtotime($now) - 3600); // 1 hour before update
            }

            // 1. Initial System Comment
            Comment::updateOrCreate(
                ['id' => 'cmt-sys-' . $ticket->id],
                [
                    'ticketId' => $ticket->id,
                    'authorId' => $ticket->pengirimId,
                    'authorName' => $ticket->pengirimName,
                    'authorRole' => 'pengguna',
                    'text' => "Tiket baru berhasil diajukan dengan kategori \"{$ticket->layananKategori}\" → \"{$ticket->layananSub}\" → \"{$ticket->layanan}\". Otomatis diteruskan ke {$subbagName}.",
                    'timestamp' => $submissionTime,
                    'type' => 'sistem',
                ]
            );

            // 2. User explanation comment
            Comment::updateOrCreate(
                ['id' => 'cmt-user-' . $ticket->id],
                [
                    'ticketId' => $ticket->id,
                    'authorId' => $ticket->pengirimId,
                    'authorName' => $ticket->pengirimName,
                    'authorRole' => 'pengguna',
                    'text' => "Mohon bantuan penanganan secepatnya, ini sangat mendesak untuk tugas kami di lapangan.",
                    'timestamp' => date('Y-m-d H:i', strtotime($submissionTime) + 300), // 5 mins after submission
                    'type' => 'komentar',
                ]
            );

            // 3. Status-based Comments
            if (in_array($ticket->status, ['Diterima', 'Ditugaskan', 'Dikerjakan', 'Selesai', 'Kembalikan tiket ke operator'])) {
                // Kasubbag received it
                $receiveTime = date('Y-m-d H:i', strtotime($submissionTime) + 1800); // 30 mins after submission
                Comment::updateOrCreate(
                    ['id' => 'cmt-rcv-' . $ticket->id],
                    [
                        'ticketId' => $ticket->id,
                        'authorId' => $ticket->kasubbagId,
                        'authorName' => $ticket->kasubbagName ?: 'Kasubbag',
                        'authorRole' => 'kasubbag',
                        'text' => "Tiket diterima, sedang kami periksa kelayakannya.",
                        'timestamp' => $receiveTime,
                        'type' => 'terima',
                    ]
                );

                if (in_array($ticket->status, ['Ditugaskan', 'Dikerjakan', 'Selesai']) && $ticket->solverId) {
                    // Kasubbag assigned solver
                    $assignTime = date('Y-m-d H:i', strtotime($receiveTime) + 1200); // 20 mins after receive
                    Comment::updateOrCreate(
                        ['id' => 'cmt-asg-' . $ticket->id],
                        [
                            'ticketId' => $ticket->id,
                            'authorId' => $ticket->kasubbagId,
                            'authorName' => $ticket->kasubbagName ?: 'Kasubbag',
                            'authorRole' => 'kasubbag',
                            'text' => "Tiket ditugaskan kepada solver: {$ticket->solverName}.",
                            'timestamp' => $assignTime,
                            'type' => 'penugasan',
                        ]
                    );

                    if (in_array($ticket->status, ['Dikerjakan', 'Selesai'])) {
                        // Solver working comment
                        $workTime = date('Y-m-d H:i', strtotime($assignTime) + 900); // 15 mins after assign
                        Comment::updateOrCreate(
                            ['id' => 'cmt-wrk-' . $ticket->id],
                            [
                                'ticketId' => $ticket->id,
                                'authorId' => $ticket->solverId,
                                'authorName' => $ticket->solverName,
                                'authorRole' => 'solver',
                                'text' => "Baik, laporan saya terima. Sedang saya lakukan pengecekan dan troubleshooting lapangan.",
                                'timestamp' => $workTime,
                                'type' => 'komentar',
                            ]
                        );

                        if ($ticket->status === 'Selesai') {
                            // Solver completion comment
                            Comment::updateOrCreate(
                                ['id' => 'cmt-fin-' . $ticket->id],
                                [
                                    'ticketId' => $ticket->id,
                                    'authorId' => $ticket->solverId,
                                    'authorName' => $ticket->solverName,
                                    'authorRole' => 'solver',
                                    'text' => "Tiket telah selesai dikerjakan. Catatan penyelesaian: " . ($ticket->catatanKasubbag ?: 'Masalah telah diselesaikan sesuai SOP.'),
                                    'timestamp' => $ticket->tanggalUpdate ?: date('Y-m-d H:i', strtotime($workTime) + 3600),
                                    'type' => 'selesai',
                                ]
                            );
                        }
                    }
                } elseif ($ticket->status === 'Kembalikan tiket ke operator') {
                    // Rejected/Returned by Kasubbag
                    Comment::updateOrCreate(
                        ['id' => 'cmt-rej-' . $ticket->id],
                        [
                            'ticketId' => $ticket->id,
                            'authorId' => $ticket->kasubbagId,
                            'authorName' => $ticket->kasubbagName ?: 'Kasubbag',
                            'authorRole' => 'kasubbag',
                            'text' => "Tiket dikembalikan ke operator. Alasan: " . ($ticket->alasanTolak ?: 'Kategori tidak sesuai.'),
                            'timestamp' => $ticket->tanggalUpdate ?: date('Y-m-d H:i', strtotime($receiveTime) + 1800),
                            'type' => 'tolak',
                        ]
                    );
                }
            }
        }

        $this->call(ArticleSeeder::class);
    }
}
