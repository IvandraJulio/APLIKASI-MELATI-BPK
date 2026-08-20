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

        // 2. Clear existing ticket, comment, and notification data for clean demo state
        DB::table('comments')->delete();
        DB::table('notifications')->delete();
        DB::table('tickets')->delete();

        // 3. Seed 8 Mock Tickets for Demo Presentation
        $tickets = [
            [
                'id' => 'TKT-2026-001',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Teknologi',
                'layananSub' => 'Layanan Intranet',
                'layanan' => 'Gangguan Koneksi Internet Ruang Kerja',
                'detail' => 'Koneksi internet di ruang kerja lantai 3 Biro TI sering terputus-putus (RTO). Lampu indikator port switch berkedip merah secara periodik saat digunakan untuk rapat Zoom.',
                'bisa_remote' => false,
                'tanggal' => '2026-08-20',
                'tanggalUpdate' => '2026-08-20 08:00',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Pending',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(1),
            ],
            [
                'id' => 'TKT-2026-002',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Identitas',
                'layananSub' => 'Layanan Akun',
                'layanan' => 'Reset Password & Akses VPN BPK',
                'detail' => 'Lupa password akun VPN BPK untuk kerja remote dari luar kantor saat dinas lapangan. Mohon bantuan reset password dan konfirmasi akses.',
                'bisa_remote' => true,
                'tanggal' => '2026-08-20',
                'tanggalUpdate' => '2026-08-20 08:15',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Diterima',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subMinutes(45),
            ],
            [
                'id' => 'TKT-2026-003',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Perangkat',
                'layananSub' => 'Pemeliharaan Perangkat',
                'layanan' => 'Pemeliharaan Laptop Dinas (Overheat & Slow)',
                'detail' => 'Laptop HP EliteBook dinas mengalami panas berlebih (overheat), suara kipas kencang, dan performa sangat lambat saat membuka aplikasi audit BPK.',
                'bisa_remote' => false,
                'tanggal' => '2026-08-19',
                'tanggalUpdate' => '2026-08-20 07:30',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => 's1_1',
                'solverName' => 'Supriyadi (Infra Solver 1)',
                'status' => 'Dikerjakan',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(18),
            ],
            [
                'id' => 'TKT-2026-004',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Server',
                'layananSub' => 'Pengelolaan Server',
                'layanan' => 'Permintaan Penambahan Resource VM Audit',
                'detail' => 'Membutuhkan penambahan vCPU (dari 4 ke 8 core) dan RAM (dari 16GB ke 32GB) pada VM server staging database audit untuk pengujian akhir.',
                'bisa_remote' => true,
                'tanggal' => '2026-08-19',
                'tanggalUpdate' => '2026-08-20 06:45',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Dieskalasi',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(20),
            ],
            [
                'id' => 'TKT-2026-005',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Perangkat',
                'layananSub' => 'Perangkat Jaringan',
                'layanan' => 'Penggantian Access Point WiFi Lantai 4',
                'detail' => 'Access Point Wi-Fi Sektor B Lantai 4 mati total. Signal Wi-Fi hilang untuk seluruh pegawai di lantai 4. Tiket pending > 24 jam belum ditindaklanjuti.',
                'bisa_remote' => false,
                'tanggal' => '2026-08-18',
                'tanggalUpdate' => '2026-08-18 09:00',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Overdue',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(36),
            ],
            [
                'id' => 'TKT-2026-006',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Identitas',
                'layananSub' => 'Layanan Akun',
                'layanan' => 'Konfigurasi Email BPK di Smartphone',
                'detail' => 'Sinkronisasi email dinas @bpk.go.id pada aplikasi Outlook Mobile Android terhenti dan muncul error authentication.',
                'bisa_remote' => true,
                'tanggal' => '2026-08-20',
                'tanggalUpdate' => '2026-08-20 06:30',
                'tanggalSelesai' => '2026-08-20 06:30',
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => 's1_1',
                'solverName' => 'Supriyadi (Infra Solver 1)',
                'status' => 'Selesai',
                'alasanTolak' => null,
                'catatanKasubbag' => 'Telah dibantu re-konfigurasi IMAP/SMTP port 993 & re-generate app password. Pengujian kirim & terima email sukses.',
                'created_at' => now()->subHours(3),
            ],
            [
                'id' => 'TKT-2026-007',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Permintaan',
                'layananKategori' => 'Layanan Aplikasi',
                'layananSub' => 'SIM-P',
                'layanan' => 'Permintaan Fitur Baru Export Excel Custom',
                'detail' => 'Permintaan penambahan tombol export khusus data audit ke Excel format XLSB pada modul SIM-P.',
                'bisa_remote' => false,
                'tanggal' => '2026-08-19',
                'tanggalUpdate' => '2026-08-19 11:30',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => null,
                'solverName' => null,
                'status' => 'Kembalikan tiket ke operator',
                'alasanTolak' => 'Salah kategori layanan. Permintaan pengembangan aplikasi SIM-P harusnya ditujukan ke Subbagian Pengembangan Sistem Informasi Pemeriksaan (k3), bukan Subbag Infrastruktur.',
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(24),
            ],
            [
                'id' => 'TKT-2026-008',
                'pengirimId' => 'u1',
                'pengirimName' => 'Budi Santoso',
                'jenis' => 'Insiden',
                'layananKategori' => 'Layanan Aplikasi',
                'layananSub' => 'Aplikasi Perkantoran',
                'layanan' => 'Troubleshooting Error Excel Add-in Audit',
                'detail' => 'Add-in audit BPK pada Excel mengalami crash "Runtime Error 1004" saat mengolah dataset audit berukuran besar (>50MB). Butuh bantuan remote troubleshooting.',
                'bisa_remote' => true,
                'tanggal' => '2026-08-20',
                'tanggalUpdate' => '2026-08-20 07:50',
                'tanggalSelesai' => null,
                'kasubbagId' => 'k1',
                'kasubbagName' => 'Ir. Hartono, M.T.',
                'solverId' => 's1_1',
                'solverName' => 'Supriyadi (Infra Solver 1)',
                'status' => 'Dikerjakan',
                'alasanTolak' => null,
                'catatanKasubbag' => null,
                'created_at' => now()->subHours(2),
            ],
        ];

        foreach ($tickets as $ticketData) {
            $created_at = $ticketData['created_at'] ?? now();
            unset($ticketData['created_at']);
            $t = Ticket::create($ticketData);
            $t->created_at = $created_at;
            $t->save();
        }

        // 4. Seed Comments for the Mock Tickets
        $comments = [
            // TKT-2026-001 (Pending)
            [
                'id' => 'c1_1',
                'ticketId' => 'TKT-2026-001',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket baru berhasil diajukan dengan kategori "Layanan Teknologi" → "Layanan Intranet" → "Gangguan Koneksi Internet Ruang Kerja". Otomatis diteruskan ke Subbagian Pengelolaan Infrastruktur dan Jaringan.',
                'timestamp' => '2026-08-20 08:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c1_2',
                'ticketId' => 'TKT-2026-001',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Mohon bantuan penanganan secepatnya Pak Kasubbag, koneksi sangat dibutuhkan untuk menyusun laporan konsolidasi.',
                'timestamp' => '2026-08-20 08:05',
                'type' => 'komentar',
            ],

            // TKT-2026-002 (Diterima)
            [
                'id' => 'c2_1',
                'ticketId' => 'TKT-2026-002',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket baru diajukan via portal pengguna.',
                'timestamp' => '2026-08-20 08:10',
                'type' => 'sistem',
            ],
            [
                'id' => 'c2_2',
                'ticketId' => 'TKT-2026-002',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket telah diterima oleh Kasubbag Infrastruktur. Silakan solver yang bertugas untuk mengambil dan memproses tiket ini.',
                'timestamp' => '2026-08-20 08:15',
                'type' => 'terima',
            ],

            // TKT-2026-003 (Dikerjakan)
            [
                'id' => 'c3_1',
                'ticketId' => 'TKT-2026-003',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-19 14:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c3_2',
                'ticketId' => 'TKT-2026-003',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket ditugaskan kepada solver: Supriyadi (Infra Solver 1).',
                'timestamp' => '2026-08-19 14:30',
                'type' => 'penugasan',
            ],
            [
                'id' => 'c3_3',
                'ticketId' => 'TKT-2026-003',
                'authorId' => 's1_1',
                'authorName' => 'Supriyadi (Infra Solver 1)',
                'authorRole' => 'solver',
                'text' => 'Tindak Lanjut Solver: Laptop telah diterima di meja kerja TI. Sedang dilakukan pembongkaran casing, pembersihan debu fan, dan penggantian thermal paste.',
                'timestamp' => '2026-08-20 07:30',
                'type' => 'tindaklanjuti',
            ],

            // TKT-2026-004 (Dieskalasi)
            [
                'id' => 'c4_1',
                'ticketId' => 'TKT-2026-004',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-19 10:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c4_2',
                'ticketId' => 'TKT-2026-004',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket ditugaskan kepada Supriyadi (Infra Solver 1).',
                'timestamp' => '2026-08-19 10:15',
                'type' => 'penugasan',
            ],
            [
                'id' => 'c4_3',
                'ticketId' => 'TKT-2026-004',
                'authorId' => 's1_1',
                'authorName' => 'Supriyadi (Infra Solver 1)',
                'authorRole' => 'solver',
                'text' => 'Tiket dieskalasi kembali ke Kasubbag oleh Solver Supriyadi (Infra Solver 1). Alasan: Penambahan quota vCPU dan RAM melebihi ambang batas reguler dan memerlukan otorisasi persetujuan Kasubbag Infrastruktur.',
                'timestamp' => '2026-08-20 06:45',
                'type' => 'eskalasi',
            ],

            // TKT-2026-005 (Overdue)
            [
                'id' => 'c5_1',
                'ticketId' => 'TKT-2026-005',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-18 09:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c5_2',
                'ticketId' => 'TKT-2026-005',
                'authorId' => 'sistem',
                'authorName' => 'Sistem Otomatis Escalation',
                'authorRole' => 'sistem',
                'text' => 'Tiket otomatis diubah statusnya menjadi Overdue karena belum direspons dalam waktu 24 jam.',
                'timestamp' => '2026-08-19 09:00',
                'type' => 'overdue',
            ],

            // TKT-2026-006 (Selesai)
            [
                'id' => 'c6_1',
                'ticketId' => 'TKT-2026-006',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-20 05:30',
                'type' => 'sistem',
            ],
            [
                'id' => 'c6_2',
                'ticketId' => 'TKT-2026-006',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket ditugaskan ke Supriyadi.',
                'timestamp' => '2026-08-20 05:45',
                'type' => 'penugasan',
            ],
            [
                'id' => 'c6_3',
                'ticketId' => 'TKT-2026-006',
                'authorId' => 's1_1',
                'authorName' => 'Supriyadi (Infra Solver 1)',
                'authorRole' => 'solver',
                'text' => 'Tiket telah selesai dikerjakan. Catatan: Telah dibantu re-konfigurasi IMAP/SMTP port 993 & re-generate app password. Pengujian kirim & terima email sukses.',
                'timestamp' => '2026-08-20 06:30',
                'type' => 'penyelesaian',
            ],

            // TKT-2026-007 (Kembalikan tiket ke operator / Ditolak)
            [
                'id' => 'c7_1',
                'ticketId' => 'TKT-2026-007',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-19 09:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c7_2',
                'ticketId' => 'TKT-2026-007',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket dikembalikan ke operator. Alasan: Salah kategori layanan. Permintaan pengembangan aplikasi SIM-P harusnya ditujukan ke Subbagian Pengembangan Sistem Informasi Pemeriksaan (k3), bukan Subbag Infrastruktur.',
                'timestamp' => '2026-08-19 11:30',
                'type' => 'tolak',
            ],

            // TKT-2026-008 (Dikerjakan + Google Meet)
            [
                'id' => 'c8_1',
                'ticketId' => 'TKT-2026-008',
                'authorId' => 'u1',
                'authorName' => 'Budi Santoso',
                'authorRole' => 'pengguna',
                'text' => 'Tiket diajukan oleh pengguna.',
                'timestamp' => '2026-08-20 07:00',
                'type' => 'sistem',
            ],
            [
                'id' => 'c8_2',
                'ticketId' => 'TKT-2026-008',
                'authorId' => 'k1',
                'authorName' => 'Ir. Hartono, M.T.',
                'authorRole' => 'kasubbag',
                'text' => 'Tiket ditugaskan ke Supriyadi.',
                'timestamp' => '2026-08-20 07:15',
                'type' => 'penugasan',
            ],
            [
                'id' => 'c8_3',
                'ticketId' => 'TKT-2026-008',
                'authorId' => 's1_1',
                'authorName' => 'Supriyadi (Infra Solver 1)',
                'authorRole' => 'solver',
                'text' => 'Halo Pak Budi, mari kita lakukan remote screen-sharing via Google Meet untuk mengecek error Add-in Excel secara langsung. Silakan bergabung melalui link berikut: https://meet.google.com/abc-defg-hij',
                'timestamp' => '2026-08-20 07:50',
                'type' => 'komentar',
            ],
        ];

        foreach ($comments as $commentData) {
            Comment::create($commentData);
        }

        $this->call(ArticleSeeder::class);
    }
}
