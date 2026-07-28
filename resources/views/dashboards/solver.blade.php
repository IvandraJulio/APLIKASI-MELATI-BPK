@extends('layouts.app')

@section('title', 'Dashboard Solver - Portal Layanan TI BPK')

@section('content')
@php
    $solver = Auth::user();
    $assignedToday = \App\Models\Ticket::where(function($q) use ($solver) {
            $q->where('solverId', $solver->id)
              ->orWhere('solver2Id', $solver->id);
        })
        ->whereIn('status', ['Ditugaskan', 'Dikerjakan'])
        ->count();
    
    if ($assignedToday >= 6) {
        $busyLabel = 'Hi';
        $busyBg = 'bg-rose-100 text-rose-800 border-rose-200';
        $isBusyHi = true;
    } elseif ($assignedToday >= 3) {
        $busyLabel = 'Med';
        $busyBg = 'bg-amber-100 text-amber-800 border-amber-200';
        $isBusyHi = false;
    } else {
        $busyLabel = 'Low';
        $busyBg = 'bg-emerald-100 text-emerald-800 border-emerald-200';
        $isBusyHi = false;
    }
@endphp
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8.5rem)] animate-in fade-in duration-300" x-data="solverPage()">
    <!-- LEFT PANEL: Tasks list -->
    <div class="lg:col-span-4 bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col h-full">
        <!-- Header with status toggle -->
        <div class="p-4 border-b border-gray-100 bg-white shrink-0">
            <span class="text-[9px] bg-purple-50 text-purple-700 font-bold px-2 py-0.5 rounded-md uppercase font-mono tracking-wider">
                Petugas Solver TI
            </span>
            <div class="flex items-center justify-between mt-1.5">
                <h3 class="text-xs font-bold text-gray-800 font-display">Tugas Penanganan Saya</h3>
                <span class="px-2 py-0.5 rounded text-[8px] font-black border uppercase tracking-wider transition-all"
                      :class="busyLabel === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (busyLabel === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                      x-text="'Busy: ' + busyLabel + ' (' + assignedToday + '/6)'">
                </span>
            </div>

            <div class="flex gap-1.5 mt-3 bg-slate-50 p-1 rounded-xl">
                <button @click="activeTab = 'aktif'; selectedId = null"
                        class="flex-1 py-2 text-center text-[10px] md:text-xs font-bold rounded-lg transition-all cursor-pointer"
                        :class="activeTab === 'aktif' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    Tugas Aktif (<span x-text="tickets.filter(t => (t.solverId === '{{ Auth::id() }}' || t.solver2Id === '{{ Auth::id() }}') && (t.status === 'Ditugaskan' || t.status === 'Dikerjakan')).length"></span>)
                </button>
                <button @click="activeTab = 'bisa_diambil'; selectedId = null"
                        class="flex-1 py-2 text-center text-[10px] md:text-xs font-bold rounded-lg transition-all cursor-pointer"
                        :class="activeTab === 'bisa_diambil' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    Bisa Diambil (<span x-text="tickets.filter(t => t.kasubbagId === '{{ Auth::user()->subbagId }}' && !t.solverId && t.status !== 'Selesai' && t.status !== 'Kembalikan tiket ke operator').length"></span>)
                </button>
                <button @click="activeTab = 'selesai'; selectedId = null"
                        class="flex-1 py-2 text-center text-[10px] md:text-xs font-bold rounded-lg transition-all cursor-pointer"
                        :class="activeTab === 'selesai' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    Selesai (<span x-text="tickets.filter(t => (t.comments && t.comments.some(c => c.authorId === '{{ Auth::id() }}' && c.type === 'penyelesaian')) || ((t.solverId === '{{ Auth::id() }}' || t.solver2Id === '{{ Auth::id() }}') && t.status === 'Selesai')).length"></span>)
                </button>
            </div>
        </div>

        <!-- Scrolling list -->
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100 bg-slate-50/30">
            <template x-for="t in getDisplayedTickets()" :key="t.id">
                <button @click="selectTicket(t.id)"
                        class="w-full p-4 text-left transition-all block cursor-pointer"
                        :class="selectedId === t.id ? 'bg-[#fcf4ec] border-l-4 border-l-[#b26d27]' : 'bg-transparent hover:bg-slate-50'">
                    <div class="flex items-center justify-between gap-1.5 mb-1.5">
                        <span class="font-mono font-bold text-xs text-gray-800" x-text="t.id"></span>
                        <span class="status-badge" :class="getStatusBadgeClass(t.status)" x-text="t.status === 'Pending' ? 'New' : t.status"></span>
                    </div>

                    <h4 class="text-xs font-bold text-gray-900 truncate" x-text="t.layanan"></h4>
                    <p class="text-[10px] text-gray-400 mt-1 truncate" x-text="'Pelapor: ' + t.pengirimName"></p>

                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[9px] text-gray-400 font-mono">
                        <span x-text="t.tanggalUpdate"></span>
                    </div>
                </button>
            </template>
            <div x-show="getDisplayedTickets().length === 0" class="p-8 text-center text-gray-400 text-xs">
                Tidak ada tugas dalam kategori ini.
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Tasks detail & comments -->
    <div class="lg:col-span-8 bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col h-full">
        <template x-if="getSelectedTicket()">
            <div class="flex flex-col h-full overflow-hidden">
                <!-- Header -->
                <div class="p-4 md:p-5 border-b border-[#e2e6ea] bg-slate-50/40 shrink-0 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-800 font-display mt-0.5" x-text="getSelectedTicket().layanan"></h2>
                        <p class="text-[11px] text-gray-400 mt-1" x-text="'ID: ' + getSelectedTicket().id + ' | Pelapor: ' + getSelectedTicket().pengirimName"></p>
                    </div>
                    <span class="status-badge" :class="getStatusBadgeClass(getSelectedTicket().status)" x-text="getSelectedTicket().status === 'Pending' ? 'New' : getSelectedTicket().status"></span>
                </div>

                <!-- Scrollable Body -->
                <div class="flex-1 overflow-y-auto p-5 space-y-6">
                    <!-- Issue Detail -->
                    <div class="bg-[#fffbeb]/20 border border-[#e8ceab]/30 p-4 rounded-xl space-y-2">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="info" class="w-4 h-4 text-[#b26d27]"></i>
                            <span>Detail Pelaporan</span>
                        </h4>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium whitespace-pre-wrap" x-text="getSelectedTicket().detail"></p>
                    </div>

                    <!-- Flow Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-medium text-gray-600">
                        <div class="border border-slate-100 p-3 rounded-xl">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kategori Layanan</div>
                            <div class="text-gray-800 font-bold mt-1" x-text="getSelectedTicket().layananKategori + ' → ' + getSelectedTicket().layananSub"></div>
                        </div>
                        <div class="border border-slate-100 p-3 rounded-xl" x-show="getSelectedTicket().solverName">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Ditugaskan Kepada</div>
                            <div class="text-gray-800 font-bold mt-1">
                                <span x-text="getSelectedTicket().solverName"></span>
                                <template x-if="getSelectedTicket().solver2Name">
                                    <span x-text="' & ' + getSelectedTicket().solver2Name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 pt-5 space-y-3.5" x-show="activeTab !== 'selesai'">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Aksi Penanganan Solver</h4>
                        
                        <div class="flex flex-wrap gap-2.5">
                            <!-- Claim Ticket -->
                            <button @click="if (isBusyHi) { alert('Anda sedang Busy: Hi (telah mengambil/memiliki 6 atau lebih tiket aktif). Anda tidak dapat mengambil tiket baru.'); return; } claimTicket()" 
                                    x-show="activeTab === 'bisa_diambil'"
                                    :class="isBusyHi ? 'bg-gray-300 text-gray-500 cursor-not-allowed border border-gray-200' : 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer'"
                                    class="font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-1.5">
                                <i data-lucide="play" class="w-4 h-4"></i>
                                <span>Ambil & Kerjakan Tiket</span>
                                <span x-show="isBusyHi" class="text-[9px] bg-rose-600 text-white font-extrabold px-1.5 py-0.5 rounded ml-1 uppercase font-mono">Busy: Hi</span>
                            </button>



                            <!-- Tindaklanjuti Ticket -->
                            <button @click="openTindaklanjutiModal()" 
                                    x-show="activeTab === 'aktif'"
                                    class="bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                                Tindaklanjuti
                            </button>

                            <!-- Escalate Ticket -->
                            <button @click="openEscalateModal()" 
                                    x-show="activeTab === 'aktif'"
                                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                                Eskalasi ke Kasubbag
                            </button>
                        </div>
                    </div>

                    <!-- Comments Section -->
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
                                    <template x-for="c in getSelectedTicket().comments.filter(c => !['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti'].includes(c.type))" :key="c.id">
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
                                    <div x-show="getSelectedTicket().comments.filter(c => !['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti'].includes(c.type)).length === 0" class="text-center py-6 text-gray-400 text-xs font-medium">
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
                                    <template x-for="c in getSelectedTicket().comments.filter(c => ['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti'].includes(c.type))" :key="c.id">
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
                                    <div x-show="getSelectedTicket().comments.filter(c => ['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti'].includes(c.type)).length === 0" class="text-center py-6 text-gray-400 text-xs font-medium">
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
                <i data-lucide="check-square" class="w-12 h-12 mx-auto text-gray-300"></i>
                <p class="text-sm font-semibold">Pilih tugas dari daftar di sebelah kiri untuk mulai menindaklanjuti.</p>
            </div>
        </template>
    </div>

    <!-- MODAL: Complete Ticket -->
    <div x-show="completeModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4 relative">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                    <i data-lucide="check-circle" class="text-emerald-600 w-5 h-5"></i>
                    Selesaikan Tiket
                </h3>
                <button @click="completeModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-slate-100 p-1.5 rounded-lg transition-all cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Catatan Penyelesaian</label>
                <textarea x-model="completeNotes" rows="3" placeholder="Bagaimana masalah diselesaikan?..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-medium"></textarea>
            </div>
            <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button @click="completeModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button @click="confirmComplete()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer">Selesai</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Escalate Ticket -->
    <div x-show="escalateModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4 relative">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                    <i data-lucide="alert-octagon" class="text-rose-600 w-5 h-5"></i>
                    Eskalasi ke Kasubbag
                </h3>
                <button @click="escalateModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-slate-100 p-1.5 rounded-lg transition-all cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Alasan Eskalasi</label>
                <textarea x-model="escalateReason" rows="3" placeholder="Mengapa tiket ini perlu dieskalasi kembali ke Kasubbag?..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-medium"></textarea>
            </div>
            <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button @click="escalateModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button @click="confirmEscalate()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer">Kirim</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Tindaklanjuti Ticket -->
    <div x-show="tindaklanjutiModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4 relative">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                    <i data-lucide="wrench" class="text-sky-600 w-5 h-5"></i>
                    Tindaklanjuti Tiket
                </h3>
                <button @click="tindaklanjutiModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-slate-100 p-1.5 rounded-lg transition-all cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Log / Catatan Tindak Lanjut <span class="text-rose-500">*</span></label>
                <textarea x-model="tindaklanjutiNotes" rows="3" placeholder="Detail tindak lanjut yang dilakukan..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-medium"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button @click="confirmCompleteFromTL()" type="button" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer transition-all shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Selesaikan Tiket
                </button>
                <button @click="confirmTindaklanjuti()" type="button" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold cursor-pointer transition-all shadow-sm flex items-center gap-1.5">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                    Kirim Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function solverPage() {
        return {
            tickets: [],
            searchQuery: '',
            selectedId: null,
            activeTab: 'aktif',
            commentText: '',
            
            completeModalOpen: false,
            escalateModalOpen: false,
            tindaklanjutiModalOpen: false,
            
            completeNotes: '',
            escalateReason: '',
            tindaklanjutiNotes: '',
            isBusyHi: {{ $isBusyHi ? 'true' : 'false' }},
            assignedToday: {{ $assignedToday }},
            busyLabel: '{{ $busyLabel }}',

            init() {
                this.fetchTickets();
                window.addEventListener('search-tickets', (e) => {
                    this.searchQuery = e.detail;
                });
            },

            async fetchBusyStatus() {
                try {
                    const res = await fetch('/api/solvers/busy-status');
                    const solvers = await res.json();
                    const me = solvers.find(s => s.id === '{{ Auth::id() }}');
                    if (me) {
                        this.assignedToday = me.assigned_today;
                        this.busyLabel = me.busy_level;
                        this.isBusyHi = me.busy_level === 'Hi';
                    }
                } catch (e) {
                    console.error('Failed to fetch busy status', e);
                }
            },

            async fetchTickets() {
                try {
                    const res = await fetch('/api/tickets');
                    this.tickets = await res.json();
                    
                    // Fetch busy status dynamically
                    await this.fetchBusyStatus();
                    
                    const displayed = this.getDisplayedTickets();
                    if (displayed.length > 0 && !this.selectedId) {
                        this.selectedId = displayed[0].id;
                    }
                    
                    this.$nextTick(() => {
                        lucide.createIcons();
                        this.scrollComments();
                    });
                } catch (e) {
                    console.error('Failed to load tickets', e);
                }
            },

            getDisplayedTickets() {
                const me = '{{ Auth::id() }}';
                return this.tickets.filter(t => {
                    let tabMatches = false;
                    if (this.activeTab === 'aktif') {
                        tabMatches = (t.solverId === me || t.solver2Id === me) && (t.status === 'Ditugaskan' || t.status === 'Dikerjakan');
                    } else if (this.activeTab === 'bisa_diambil') {
                        tabMatches = t.kasubbagId === '{{ Auth::user()->subbagId }}' && !t.solverId && t.status !== 'Selesai' && t.status !== 'Kembalikan tiket ke operator';
                    } else { // selesai
                        const completedByMe = t.comments && t.comments.some(c => c.authorId === me && c.type === 'penyelesaian');
                        tabMatches = completedByMe || ((t.solverId === me || t.solver2Id === me) && t.status === 'Selesai');
                    }

                    if (!tabMatches) return false;

                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        const idStr = String(t.id).toLowerCase();
                        const layananStr = String(t.layanan).toLowerCase();
                        const pengirimStr = String(t.pengirimName).toLowerCase();
                        const detailStr = t.detail ? String(t.detail).toLowerCase() : '';
                        
                        return idStr.includes(query) || 
                               layananStr.includes(query) || 
                               pengirimStr.includes(query) ||
                               detailStr.includes(query);
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
                switch (status) {
                    case 'Pending': return 'status-pending';
                    case 'Diterima': return 'status-diterima';
                    case 'Ditugaskan': return 'status-ditugaskan';
                    case 'Dikerjakan': return 'status-dikerjakan';
                    case 'Dieskalasi': return 'status-dieskalasi';
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
                    case 'tindaklanjuti': return 'bg-sky-50 border-l-4 border-l-sky-500 text-sky-800';
                    default: return 'bg-white border-[#e2e6ea] shadow-xs text-gray-800';
                }
            },

            getRoleBadgeClass(role) {
                if (role === 'pengguna') return 'bg-[#fcf4ec] text-[#b26d27]';
                if (role === 'kasubbag') return 'bg-blue-100 text-blue-800';
                if (role === 'solver') return 'bg-purple-100 text-purple-800';
                return 'bg-slate-100 text-slate-700';
            },

            async claimTicket() {
                const ticket = this.getSelectedTicket();
                if (!ticket) return;

                const meId = '{{ Auth::id() }}';
                const meName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Dikerjakan',
                            solverId: meId,
                            solverName: meName,
                            comment: {
                                text: `Tiket diambil secara mandiri dan mulai dikerjakan oleh Solver: ${meName}.`,
                                type: 'penugasan'
                            }
                        })
                    });

                    if (response.ok) {
                        this.activeTab = 'aktif';
                        this.selectedId = ticket.id;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal mengambil tiket.');
                }
            },



            openCompleteModal() {
                this.completeNotes = '';
                this.completeModalOpen = true;
            },

            async confirmComplete() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.completeNotes.trim()) return;

                const meName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Selesai',
                            catatanKasubbag: this.completeNotes.trim(),
                            tanggalSelesai: new Date().toISOString().slice(0, 10),
                            comment: {
                                text: `Tiket telah selesai dikerjakan. Catatan: ${this.completeNotes.trim()}`,
                                type: 'penyelesaian'
                            }
                        })
                    });

                    if (response.ok) {
                        this.completeModalOpen = false;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal menyelesaikan tiket.');
                }
            },

            openEscalateModal() {
                this.escalateReason = '';
                this.escalateModalOpen = true;
            },

            async confirmEscalate() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.escalateReason.trim()) return;

                const meName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Dieskalasi',
                            solverId: '',
                            solverName: '',
                            solver2Id: null,
                            solver2Name: null,
                            comment: {
                                text: `Tiket dieskalasi kembali ke Kasubbag oleh Solver ${meName}. Alasan: ${this.escalateReason.trim()}`,
                                type: 'eskalasi'
                            }
                        })
                    });

                    if (response.ok) {
                        this.escalateModalOpen = false;
                        this.selectedId = null;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal mengeksekusi eskalasi.');
                }
            },

            openTindaklanjutiModal() {
                this.tindaklanjutiNotes = '';
                this.tindaklanjutiModalOpen = true;
            },

            async confirmTindaklanjuti() {
                const ticket = this.getSelectedTicket();
                if (!ticket) return;

                if (!this.tindaklanjutiNotes.trim()) {
                    alert('Harap isi Catatan Tindak Lanjut terlebih dahulu!');
                    return;
                }

                const meName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Dikerjakan',
                            comment: {
                                text: `Tindak Lanjut Solver: ${this.tindaklanjutiNotes.trim()}`,
                                type: 'tindaklanjuti'
                            }
                        })
                    });

                    if (response.ok) {
                        this.tindaklanjutiModalOpen = false;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal mengirim tindak lanjut.');
                }
            },

            async confirmCompleteFromTL() {
                const ticket = this.getSelectedTicket();
                if (!ticket) return;

                if (!this.tindaklanjutiNotes.trim()) {
                    alert('Wajib mengisi Catatan Tindak Lanjut terlebih dahulu untuk menyelesaikan tiket!');
                    return;
                }

                const meName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Selesai',
                            catatanKasubbag: this.tindaklanjutiNotes.trim(),
                            tanggalSelesai: new Date().toISOString().slice(0, 10),
                            comment: {
                                text: `Tindak Lanjut & Penyelesaian Solver: ${this.tindaklanjutiNotes.trim()}`,
                                type: 'penyelesaian'
                            }
                        })
                    });

                    if (response.ok) {
                        this.tindaklanjutiModalOpen = false;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal menyelesaikan tiket.');
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
