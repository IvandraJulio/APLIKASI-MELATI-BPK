@extends('layouts.app')

@section('title', 'Status Tiket Saya - Portal Layanan TI BPK')

@section('content')
<div class="space-y-6 animate-in fade-in duration-300" x-data="tiketPage()">
    <!-- Section Heading -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-[#b26d27] font-display">Status Tiket Saya</h2>
            <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh riwayat tiket pelaporan Anda</p>
        </div>
    </div>

    <!-- Panel Card -->
    <div class="bg-white rounded-2xl shadow-xs overflow-hidden border border-slate-100/30 flex flex-col">
        <!-- Header with Search & Filter -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
            <!-- Left: Search Box -->
            <div class="relative w-full md:max-w-sm">
                <input type="text" x-model="search" placeholder="Cari berdasarkan ID, kategori, deskripsi..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl pl-9.5 pr-4 py-2.5 text-xs outline-none transition-all placeholder:text-gray-400 font-semibold">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            </div>
            
            <!-- Right: Filter Buttons -->
            <div class="flex gap-2">
                <button @click="filter = 'All'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer" :class="filter === 'All' ? 'bg-[#b26d27] text-white shadow-sm' : 'bg-white border border-slate-200 text-gray-500 hover:bg-slate-100'">Semua</button>
                <button @click="filter = 'Aktif'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer" :class="filter === 'Aktif' ? 'bg-[#b26d27] text-white shadow-sm' : 'bg-white border border-slate-200 text-gray-500 hover:bg-slate-100'">Aktif</button>
                <button @click="filter = 'Selesai'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer" :class="filter === 'Selesai' ? 'bg-[#b26d27] text-white shadow-sm' : 'bg-white border border-slate-200 text-gray-500 hover:bg-slate-100'">Selesai</button>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/30 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-4">ID Tiket</th>
                        <th class="px-6 py-4">Kategori Layanan</th>
                        <th class="px-6 py-4">Layanan / Detail</th>
                        <th class="px-6 py-4">Tanggal Update</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-gray-700">
                    <template x-for="t in filteredTickets()" :key="t.id">
                        <tr class="hover:bg-slate-50/20 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-[#b26d27]" x-text="t.id"></td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 block" x-text="t.layananKategori"></span>
                                <span class="text-gray-400 text-[10px] block mt-0.5" x-text="t.layananSub"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800 block truncate max-w-xs md:max-w-md" x-text="t.layanan" :title="t.layanan"></span>
                                <span class="text-gray-400 text-[10px] block truncate max-w-xs md:max-w-md mt-0.5" x-text="t.detail"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono" x-text="t.tanggalUpdate"></td>
                            <td class="px-6 py-4">
                                <span class="status-badge" :class="getStatusBadgeClass(t.status)" x-text="t.status === 'Kembalikan tiket ke operator' ? 'Pending' : t.status"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <template x-if="canReopen(t)">
                                        <button @click="reopenTicket(t.id)" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3 py-2 rounded-xl transition-all flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                            <span>Reopen</span>
                                        </button>
                                    </template>
                                    <a :href="'/dashboard/detail?id=' + t.id" class="inline-block bg-slate-100 hover:bg-[#fcf4ec] hover:text-[#b26d27] text-gray-700 font-bold px-4 py-2 rounded-xl transition-all cursor-pointer">
                                        Lihat Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <!-- Empty State -->
            <div x-show="filteredTickets().length === 0" class="text-center py-16 text-gray-400 space-y-2">
                <i data-lucide="layers" class="w-10 h-10 mx-auto text-gray-300"></i>
                <p class="text-xs font-semibold">Tidak ada tiket ditemukan dalam kategori ini.</p>
                <a href="{{ route('dashboard') }}" class="inline-block mt-2 text-xs bg-[#b26d27] text-white font-bold px-4 py-2 rounded-full hover:bg-[#9b5a1b] transition-colors cursor-pointer">
                    Buat Tiket Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function tiketPage() {
        return {
            tickets: @json($tickets),
            search: '',
            filter: 'All',

            init() {
                // Dimuat instan menggunakan data yang dipre-render oleh PHP
            },

            async fetchTickets() {
                try {
                    const res = await fetch('/api/tickets');
                    this.tickets = await res.json();
                } catch (e) {
                    console.error('Failed to load tickets', e);
                }
            },

            canReopen(t) {
                if (!t || t.status !== 'Selesai') return false;
                
                let timeStr = t.tanggalSelesai || t.tanggalUpdate || t.created_at;
                if (!timeStr) return false;
                
                let formattedStr = String(timeStr).trim().replace(' ', 'T');
                if (formattedStr.length <= 10) {
                    formattedStr += 'T23:59:59';
                }
                
                let completedTime = new Date(formattedStr).getTime();
                if (isNaN(completedTime)) return false;

                const now = Date.now();
                const elapsedMs = now - completedTime;
                const hours24Ms = 24 * 60 * 60 * 1000;

                return elapsedMs >= 0 && elapsedMs <= hours24Ms;
            },

            async reopenTicket(id) {
                if (!confirm('Apakah Anda yakin ingin membuka kembali tiket ini?')) return;
                
                try {
                    const response = await fetch(`/api/tickets/${id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            action: 'reopen'
                        })
                    });

                    const res = await response.json();
                    if (response.ok && res.success) {
                        alert('Tiket berhasil dibuka kembali!');
                        await this.fetchTickets();
                        this.filter = 'Aktif';
                    } else {
                        alert(res.error || 'Gagal membuka kembali tiket.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            filteredTickets() {
                return this.tickets.filter(t => {
                    const displayStatus = t.status === 'Kembalikan tiket ke operator' ? 'Pending' : t.status;
                    
                    // Filter tab
                    if (this.filter === 'Aktif') {
                        if (displayStatus === 'Selesai') return false;
                    } else if (this.filter === 'Selesai') {
                        if (displayStatus !== 'Selesai') return false;
                    }

                    // Search box
                    if (this.search) {
                        const s = this.search.toLowerCase();
                        return t.id.toLowerCase().includes(s) ||
                               t.layananKategori.toLowerCase().includes(s) ||
                               t.layananSub.toLowerCase().includes(s) ||
                               t.layanan.toLowerCase().includes(s) ||
                               t.detail.toLowerCase().includes(s);
                    }

                    return true;
                });
            },

            getStatusBadgeClass(status) {
                const s = status === 'Kembalikan tiket ke operator' ? 'Pending' : status;
                switch (s) {
                    case 'Pending': return 'status-pending';
                    case 'Diterima': return 'status-diterima';
                    case 'Ditugaskan': return 'status-ditugaskan';
                    case 'Dikerjakan': return 'status-dikerjakan';
                    case 'Dieskalasi': return 'status-dieskalasi';
                    case 'Overdue': return 'status-overdue';
                    case 'Selesai': return 'status-selesai';
                    default: return 'status-pending';
                }
            }
        };
    }
</script>
@endpush
