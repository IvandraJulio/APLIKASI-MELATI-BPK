<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\Article;
use App\Models\User;
use App\Http\Controllers\DashboardController;

echo "=== MEMULAI DEMONSTRASI AUTO-FAQ ===\n";

// 1. Dapatkan user valid untuk menjadi pengirim & solver
$user = User::first();
if (!$user) {
    echo "Error: Tidak ada user di database untuk pengujian.\n";
    exit(1);
}
echo "Menggunakan user: {$user->name} (Role: {$user->role})\n";

// 2. Bersihkan data uji coba lama agar bersih
$category = 'Layanan Identitas';
$sub = 'Layanan Akun';
$service = 'Masalah Reset Password Test';

echo "Membersihkan data uji coba lama...\n";
Ticket::where('layanan', $service)->delete();
Article::where('service', $service)->delete();

// 3. Buat 9 tiket berstatus Selesai yang belum diproses FAQ
echo "Membuat 9 tiket selesai sebelumnya...\n";
$problems = [
    "Tidak bisa login karena lupa password portal.",
    "Muncul error password salah saat mencoba masuk.",
    "Sudah coba reset password tapi email link tidak masuk.",
    "Akun terkunci setelah 3 kali salah memasukkan password.",
    "Butuh bantuan reset kata sandi akun portal saya.",
    "Lupa password dan juga lupa email pemulihannya.",
    "Error login incorrect credentials secara terus menerus.",
    "Tombol reset password tidak merespon di browser Chrome.",
    "Ingin mengubah password lama yang sudah kedaluwarsa."
];

$solutions = [
    "Password direset manual oleh solver dan diberikan password sementara.",
    "Melakukan verifikasi NIP lalu mereset password via SISDM.",
    "Solver memeriksa antrean email keluar dan mengirim ulang link reset.",
    "Membuka blokir akun lewat admin panel dan mereset password.",
    "Mereset password sesuai request dan mengirim kredensial baru.",
    "Mencocokkan NIP dengan email aktif baru untuk mengirim password baru.",
    "Mereset ulang password dan meminta user membersihkan cache browser.",
    "Melakukan reset dari sisi server dan menyarankan ganti browser.",
    "Mengarahkan user ke menu ubah password dan memandu langkahnya."
];

for ($i = 0; $i < 9; $i++) {
    Ticket::create([
        'id' => 'TKT-TEST-' . ($i + 1),
        'pengirimId' => $user->id,
        'pengirimName' => $user->name,
        'jenis' => 'Pengaduan',
        'layananKategori' => $category,
        'layananSub' => $sub,
        'layanan' => $service,
        'detail' => $problems[$i],
        'tanggal' => date('Y-m-d H:i'),
        'tanggalUpdate' => date('Y-m-d H:i'),
        'tanggalSelesai' => date('Y-m-d H:i'),
        'status' => 'Selesai',
        'catatanKasubbag' => $solutions[$i],
        'is_faq_processed' => false
    ]);
}

// 4. Buat tiket ke-10 (yang akan menembus batas threshold >= 10)
echo "Membuat tiket ke-10...\n";
$ticket10 = Ticket::create([
    'id' => 'TKT-TEST-10',
    'pengirimId' => $user->id,
    'pengirimName' => $user->name,
    'jenis' => 'Pengaduan',
    'layananKategori' => $category,
    'layananSub' => $sub,
    'layanan' => $service,
    'detail' => "Tidak bisa masuk ke portal, butuh reset password mendesak karena ada audit.",
    'tanggal' => date('Y-m-d H:i'),
    'tanggalUpdate' => date('Y-m-d H:i'),
    'tanggalSelesai' => date('Y-m-d H:i'),
    'status' => 'Selesai',
    'catatanKasubbag' => "Verifikasi data audit, reset password dilakukan dan dikirim ke nomor kontak terdaftar.",
    'is_faq_processed' => false
]);

// 5. Panggil metode checkAndGenerateFaq melalui Reflection
echo "Memicu logika auto-FAQ via Gemini API untuk tiket ke-10...\n";
$controller = new DashboardController();
$reflector = new \ReflectionClass($controller);
$method = $reflector->getMethod('checkAndGenerateFaq');
$method->setAccessible(true);

$method->invoke($controller, $ticket10);

// 6. Verifikasi apakah FAQ baru telah dibuat
$faq = Article::where('service', $service)->first();

if ($faq) {
    echo "\n✔ SUCCESS: FAQ Baru berhasil dibuat secara otomatis oleh Gemini AI!\n";
    echo "==================================================\n";
    echo "JUDUL FAQ: " . $faq->title . "\n";
    echo "KATEGORI : " . $faq->category . " -> " . $faq->subcategory . "\n";
    echo "KONTEN   :\n" . strip_tags($faq->content) . "\n";
    echo "==================================================\n";
} else {
    echo "\n❌ FAILED: FAQ baru tidak ditemukan di database. Silakan periksa log server/Gemini API.\n";
}

// 7. Bersihkan data test kembali agar database tetap bersih
echo "\nMembersihkan data pengujian...\n";
Ticket::where('layanan', $service)->delete();
Article::where('service', $service)->delete();
echo "Selesai.\n";
