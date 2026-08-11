@extends('layouts.app')

@section('title', 'Detail Tiket - Portal Layanan TI BPK')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8.5rem)]" x-data="tiketPage()">
    <!-- LEFT PANEL: Ticket List (Col 5) -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col h-full overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-[#e2e6ea] space-y-3 shrink-0">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-gray-800 font-display">Tiket Saya</h3>
                <span class="text-[10px] text-gray-400 font-mono" x-text="filteredTickets().length + ' tiket ditemukan'"></span>
            </div>
            
            <!-- Search Input -->
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari berdasarkan ID, kategori, deskripsi..." class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl pl-9.5 pr-4 py-2 text-xs outline-none transition-all placeholder:text-gray-400 font-medium">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            </div>

            <!-- Filters -->
            <div class="flex gap-1.5">
                <button @click="filter = 'All'" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all" :class="filter === 'All' ? 'bg-[#b26d27] text-white' : 'bg-slate-50 border border-slate-200 text-gray-500 hover:bg-slate-100'">Semua</button>
                <button @click="filter = 'Aktif'" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all" :class="filter === 'Aktif' ? 'bg-[#b26d27] text-white' : 'bg-slate-50 border border-slate-200 text-gray-500 hover:bg-slate-100'">Aktif</button>
                <button @click="filter = 'Selesai'" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all" :class="filter === 'Selesai' ? 'bg-[#b26d27] text-white' : 'bg-slate-50 border border-slate-200 text-gray-500 hover:bg-slate-100'">Selesai</button>
            </div>
        </div>

        <!-- Scrollable Ticket List -->
        <div class="flex-1 overflow-y-auto divide-y divide-[#e2e6ea]">
            <template x-for="t in filteredTickets()" :key="t.id">
                <div @click="selectTicket(t.id)" 
                     class="p-4 text-left cursor-pointer transition-all"
                     :class="selectedId === t.id ? 'bg-[#fcf4ec] border-l-4 border-l-[#b26d27]' : 'hover:bg-slate-50/50'">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="font-mono font-bold text-[#b26d27] text-xs" x-text="t.id"></span>
                        <span class="text-[10px] text-gray-400 font-mono" x-text="t.tanggal"></span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 truncate" x-text="t.layananSub"></h4>
                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-1 mb-2.5 font-medium leading-relaxed" x-text="t.detail"></p>
                    
                    <div class="flex items-center justify-between gap-2">
                        <span class="status-badge" :class="getStatusBadgeClass(t.status)" x-text="t.status === 'Kembalikan tiket ke operator' ? 'Pending' : t.status"></span>
                    </div>
                </div>
            </template>
            <div x-show="filteredTickets().length === 0" class="p-8 text-center text-gray-400 text-xs font-medium space-y-1">
                <i data-lucide="layers" class="w-8 h-8 mx-auto text-gray-300"></i>
                <p>Tidak ada tiket ditemukan.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Ticket Details (Col 7) -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col h-full overflow-hidden">
        <template x-if="getSelectedTicket()">
            <div class="flex flex-col h-full overflow-hidden">
                <!-- Details Header -->
                <div class="p-5 border-b border-[#e2e6ea] shrink-0 bg-slate-50/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-gray-800 font-display mt-0.5" x-text="getSelectedTicket().layananSub"></h2>
                            <p class="text-[11px] text-gray-400 mt-1" x-text="'ID: ' + getSelectedTicket().id + ' | Diajukan: ' + getSelectedTicket().tanggal"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-if="getSelectedTicket().status === 'Overdue'">
                                <span class="bg-rose-50 border border-rose-200 text-rose-800 text-[10px] font-bold px-2.5 py-1 rounded-xl flex items-center gap-1.5 animate-pulse">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-rose-600"></i>
                                    <span x-text="'Telat ' + getOverdueHours(getSelectedTicket()) + ' jam'"></span>
                                </span>
                            </template>
                            <template x-if="canReopen(getSelectedTicket())">
                                <button @click="reopenTicket(getSelectedTicket().id)" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Buka Kembali Tiket</span>
                                </button>
                            </template>
                            <span class="status-badge" :class="getStatusBadgeClass(getSelectedTicket().status)" x-text="getSelectedTicket().status === 'Kembalikan tiket ke operator' ? 'Pending' : getSelectedTicket().status"></span>
                        </div>
                    </div>
                </div>

                <!-- Scrollable Body -->
                <div class="flex-1 overflow-y-auto p-5 space-y-6">
                    <!-- Issue Detail -->
                    <div class="space-y-2 bg-[#fffbeb]/20 border border-[#e8ceab]/30 p-4 rounded-xl">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="info" class="w-4 h-4 text-[#b26d27]"></i>
                            <span>Detail Pelaporan</span>
                        </h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium whitespace-pre-wrap" x-text="getSelectedTicket().detail"></p>
                    </div>

                    <!-- Flow Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-medium text-gray-600">
                        <div class="border border-slate-100 p-3 rounded-xl space-y-1">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Diteruskan Ke</div>
                            <div class="text-gray-800 font-bold" x-text="getSelectedTicket().kasubbagName"></div>
                        </div>
                        <div class="border border-slate-100 p-3 rounded-xl space-y-1" x-show="getSelectedTicket().solverName">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Solver Ditugaskan</div>
                            <div class="text-gray-800 font-bold">
                                <span x-text="getSelectedTicket().solverName"></span>
                                <template x-if="getSelectedTicket().solver2Name">
                                    <span x-text="' & ' + getSelectedTicket().solver2Name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <!-- Comment Section -->
                    <div class="border-t border-slate-100 pt-5 flex flex-col space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- LEFT COLUMN: Chat/Obrolan -->
                            <div class="flex flex-col space-y-3">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="message-square" class="w-4 h-4 text-[#b26d27]"></i>
                                    <span>Obrolan & Komentar</span>
                                </h3>

                                <!-- Comments Thread -->
                                <div class="space-y-3.5 max-h-[300px] overflow-y-auto pr-1" id="comments-box">
                                    <template x-for="c in getSelectedTicket().comments.filter(c => !['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti', 'overdue'].includes(c.type))" :key="c.id">
                                        <div class="p-3.5 rounded-lg border leading-relaxed text-xs" 
                                             :class="getCommentBubbleClass(c.type)">
                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="font-bold text-gray-900 truncate" x-text="c.authorName"></span>
                                                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded uppercase"
                                                          :class="getRoleBadgeClass(c.authorRole)"
                                                          x-text="c.authorRole"></span>
                                                </div>
                                                <span class="text-[9px] text-gray-400 font-mono" x-text="c.timestamp"></span>
                                            </div>
                                            <p class="text-xs font-medium text-gray-700 whitespace-pre-wrap" x-text="c.text"></p>
                                        </div>
                                    </template>
                                    <div x-show="getSelectedTicket().comments.filter(c => !['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti', 'overdue'].includes(c.type)).length === 0" class="text-center py-6 text-gray-400 text-xs">
                                        Belum ada obrolan.
                                    </div>
                                </div>

                                <!-- Post Comment Form -->
                                <template x-if="getSelectedTicket().status !== 'Selesai'">
                                    <form @submit.prevent="submitComment()" class="flex gap-2">
                                        <input type="text" x-model="commentText" placeholder="Ketik balasan atau komentar baru..." class="flex-1 bg-white border border-[#e2e6ea] rounded-xl px-4 py-2.5 text-xs outline-none focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27] transition-all text-gray-800 placeholder-gray-400 font-medium">
                                        <button type="submit" class="bg-[#b26d27] hover:bg-[#9b5a1b] text-white w-9.5 h-9.5 rounded-xl flex items-center justify-center shrink-0 cursor-pointer transition-all shadow-sm">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </template>
                                <template x-if="getSelectedTicket().status === 'Selesai'">
                                    <div class="p-3 bg-slate-100 border border-slate-200 rounded-xl text-center text-xs font-semibold text-gray-500 flex items-center justify-center gap-1.5">
                                        <i data-lucide="lock" class="w-3.5 h-3.5 text-gray-400"></i>
                                        <span>Fitur chat dinonaktifkan karena tiket telah selesai.</span>
                                    </div>
                                </template>
                            </div>

                            <!-- RIGHT COLUMN: Log Aktivitas -->
                            <div class="flex flex-col space-y-3">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="history" class="w-4 h-4 text-[#b26d27]"></i>
                                    <span>Log Aktivitas Sistem</span>
                                </h3>

                                <!-- Logs Thread -->
                                <div class="space-y-3.5 max-h-[300px] overflow-y-auto pr-1" id="logs-box">
                                    <template x-for="c in getSelectedTicket().comments.filter(c => ['penyelesaian', 'tindaklanjuti', 'overdue', 'eskalasi'].includes(c.type))" :key="c.id">
                                        <div class="p-3.5 rounded-lg border leading-relaxed text-xs" 
                                             :class="getCommentBubbleClass(c.type)">
                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="font-bold text-gray-900 truncate" x-text="c.authorName"></span>
                                                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded uppercase"
                                                          :class="getRoleBadgeClass(c.authorRole)"
                                                          x-text="c.authorRole"></span>
                                                </div>
                                            </div>
                                            <p class="text-xs font-medium text-gray-700 whitespace-pre-wrap" x-text="c.text"></p>
                                        </div>
                                    </template>
                                    <div x-show="getSelectedTicket().comments.filter(c => ['penyelesaian', 'tindaklanjuti', 'overdue', 'eskalasi'].includes(c.type)).length === 0" class="text-center py-6 text-gray-400 text-xs">
                                        Belum ada log aktivitas.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template x-if="!getSelectedTicket()">
            <div class="m-auto text-center py-12 text-gray-400 space-y-2">
                <i data-lucide="layers" class="w-12 h-12 mx-auto text-gray-300"></i>
                <p class="text-sm font-semibold">Pilih tiket dari daftar di sebelah kiri untuk melihat detail.</p>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function tiketPage() {
        return {
            tickets: @json($tickets),
            selectedId: null,
            search: '',
            filter: 'All',
            commentText: '',

            init() {
                // Read from URL query param if exists
                const urlParams = new URLSearchParams(window.location.search);
                const queryId = urlParams.get('id');
                if (queryId) {
                    this.selectedId = queryId;
                } else if (this.tickets.length > 0) {
                    this.selectedId = this.tickets[0].id;
                }
                
                this.$nextTick(() => {
                    lucide.createIcons();
                    this.scrollComments();
                });
            },

            async fetchTickets() {
                try {
                    const res = await fetch('/api/tickets');
                    this.tickets = await res.json();
                    
                    if (this.tickets.length > 0 && !this.selectedId) {
                        this.selectedId = this.tickets[0].id;
                    }
                    
                    this.$nextTick(() => {
                        lucide.createIcons();
                        this.scrollComments();
                    });
                } catch (e) {
                    console.error('Failed to load tickets', e);
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
                               t.layananSub.toLowerCase().includes(s) ||
                               t.detail.toLowerCase().includes(s);
                    }

                    return true;
                });
            },

            getSelectedTicket() {
                return this.tickets.find(t => t.id === this.selectedId);
            },

            selectTicket(id) {
                this.selectedId = id;
                this.commentText = '';
                this.$nextTick(() => {
                    lucide.createIcons();
                    this.scrollComments();
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
            },

            getCommentBubbleClass(type) {
                switch (type) {
                    case 'sistem': return 'bg-slate-50 border-slate-200 text-slate-700';
                    case 'terima': return 'bg-emerald-50 border-l-4 border-l-emerald-500 text-emerald-800';
                    case 'penugasan': return 'bg-blue-50 border-l-4 border-l-blue-500 text-blue-800';
                    case 'mulai_kerjakan': return 'bg-purple-50 border-l-4 border-l-purple-500 text-purple-800';
                    case 'penyelesaian': return 'bg-green-50 border-l-4 border-l-green-600 text-green-800';
                    case 'eskalasi': return 'bg-amber-50 border-l-4 border-l-amber-500 text-amber-800';
                    case 'overdue': return 'bg-rose-50 border-l-4 border-l-rose-500 text-rose-800';
                    case 'tindaklanjuti': return 'bg-sky-50 border-l-4 border-l-sky-500 text-sky-800';
                    default: return 'bg-white border-[#e2e6ea] shadow-xs text-gray-800';
                }
            },

            getOverdueHours(t) {
                if (!t || !t.created_at) return 0;
                const createdTime = new Date(t.created_at).getTime();
                if (isNaN(createdTime)) return 0;
                const elapsedMs = Date.now() - createdTime;
                const hours = Math.floor(elapsedMs / (1000 * 60 * 60));
                return Math.max(0, hours - 24);
            },

            getRoleBadgeClass(role) {
                if (role === 'pengguna') return 'bg-[#fcf4ec] text-[#b26d27]';
                if (role === 'kasubbag') return 'bg-blue-100 text-blue-800';
                if (role === 'solver') return 'bg-purple-100 text-purple-800';
                return 'bg-slate-100 text-slate-700';
            },

            canReopen(t) {
                if (!t || t.status !== 'Selesai') return false;
                
                let completedTime = null;
                if (t.tanggalSelesai) {
                    completedTime = new Date(t.tanggalSelesai.replace(' ', 'T')).getTime();
                }
                if (!completedTime || isNaN(completedTime)) {
                    if (t.tanggalUpdate) {
                        completedTime = new Date(t.tanggalUpdate.replace(' ', 'T')).getTime();
                    }
                }
                if (!completedTime || isNaN(completedTime)) return false;

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
                        if (this.selectedId === id) {
                            this.selectTicket(id);
                        }
                    } else {
                        alert(res.error || 'Gagal membuka kembali tiket.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async submitComment() {
                if (!this.commentText.trim()) return;

                const text = this.commentText.trim();
                this.commentText = '';

                try {
                    const response = await fetch(`/api/tickets/${this.selectedId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            comment: {
                                text: text,
                                type: 'komentar'
                            }
                        })
                    });

                    if (response.ok) {
                        this.fetchTickets();
                    } else {
                        alert('Gagal mengirim komentar.');
                    }
                } catch (err) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            scrollComments() {
                const box = document.getElementById('comments-box');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
                const logBox = document.getElementById('logs-box');
                if (logBox) {
                    logBox.scrollTop = logBox.scrollHeight;
                }
            }
        };
    }
</script>
@endpush
