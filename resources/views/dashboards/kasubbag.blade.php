@extends('layouts.app')

@section('title', 'Dashboard Kasubbag - Portal Layanan TI BPK')

@section('content')
@php
    $defaultSolverId = '';
    foreach($solvers as $s) {
        $count = \App\Models\Ticket::where(function($q) use ($s) {
                $q->where('solverId', $s->id)
                  ->orWhere('solver2Id', $s->id);
            })
            ->whereIn('status', ['Ditugaskan', 'Dikerjakan'])
            ->count();
        if ($count < 6) {
            $defaultSolverId = $s->id;
            break;
        }
    }
    if (empty($defaultSolverId) && $solvers->isNotEmpty()) {
        $defaultSolverId = $solvers->first()->id;
    }
@endphp
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8.5rem)] animate-in fade-in duration-300" x-data="kasubbagPage()">
    <!-- LEFT PANEL: Ticket List with Tab filters -->
    <div class="lg:col-span-4 bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col h-full">
        <!-- Header with Subbag Name -->
        <div class="p-4 border-b border-gray-100 bg-white shrink-0">
            <span class="text-[9px] bg-[#fcf4ec] text-[#b26d27] font-bold px-2 py-0.5 rounded-md uppercase font-mono tracking-wider">
                Kasubbag Dispatcher
            </span>
            <h3 class="text-xs font-bold text-gray-800 font-display mt-1.5 truncate">
                Inbox Subbagian TI
            </h3>

            <!-- Tab Control -->
            <div class="flex gap-1 mt-3 bg-slate-50 p-1 rounded-xl">
                <button @click="activeTab = 'pending'; selectedId = null"
                        class="flex-1 py-2 text-center text-[10px] font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                        :class="activeTab === 'pending' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    New (<span x-text="tickets.filter(t => t.status !== 'Selesai' && t.status !== 'Kembalikan tiket ke operator' && (!t.solverId || t.solverId === '')).length"></span>)
                </button>
                <button @click="activeTab = 'aktif'; selectedId = null"
                        class="flex-1 py-2 text-center text-[10px] font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                        :class="activeTab === 'aktif' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    Aktif (<span x-text="tickets.filter(t => t.status !== 'Selesai' && t.status !== 'Kembalikan tiket ke operator' && t.solverId && t.solverId !== '').length"></span>)
                </button>
                <button @click="activeTab = 'selesai'; selectedId = null"   
                        class="flex-1 py-2 text-center text-[10px] font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                        :class="activeTab === 'selesai' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'">
                    Selesai (<span x-text="tickets.filter(t => t.status === 'Selesai' || t.status === 'Kembalikan tiket ke operator').length"></span>)
                </button>
            </div>
        </div>

        <!-- Scrolling tickets -->
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100 bg-slate-50/30">
            <template x-for="t in getDisplayedTickets()" :key="t.id">
                <button @click="selectTicket(t.id)"
                        class="w-full p-4 text-left transition-all block relative cursor-pointer"
                        :class="selectedId === t.id ? 'bg-[#fcf4ec] border-l-4 border-l-[#b26d27]' : (isOverdue(t) ? 'bg-rose-50/40 hover:bg-rose-50/70 border-l-4 border-l-rose-500' : 'bg-transparent hover:bg-slate-50')">
                    <div class="flex items-center justify-between gap-1.5 mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-bold text-xs text-gray-800" x-text="t.id"></span>
                            <template x-if="t.status === 'Dieskalasi'">
                                <span class="bg-orange-100 text-orange-800 text-[8px] font-black px-1.5 py-0.5 rounded uppercase font-mono animate-pulse">
                                    Eskalasi
                                </span>
                            </template>
                            <template x-if="isOverdue(t)">
                                <span class="bg-rose-100 text-rose-800 text-[8px] font-black px-1.5 py-0.5 rounded uppercase font-mono flex items-center gap-0.5 animate-pulse">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                                    Terlalu Lama
                                </span>
                            </template>
                        </div>
                        <span class="status-badge" :class="getStatusBadgeClass(t.status)" x-text="t.status === 'Kembalikan tiket ke operator' ? 'Ditolak' : (t.status === 'Pending' ? 'New' : t.status)"></span>
                    </div>

                    <h4 class="text-xs font-bold text-gray-900 truncate" x-text="t.layanan"></h4>
                    <p class="text-[10px] text-gray-400 mt-1 truncate leading-relaxed" x-text="'Pelapor: ' + t.pengirimName"></p>

                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[9px] text-gray-400 font-mono">
                        <span x-text="t.solverId ? 'Solver: ' + t.solverName.split(' (')[0] + (t.solver2Name ? ' & ' + t.solver2Name.split(' (')[0] : '') : 'Solver: Belum Ditunjuk'"></span>
                        <span x-text="t.tanggalUpdate"></span>
                    </div>
                </button>
            </template>
            <div x-show="getDisplayedTickets().length === 0" class="p-8 text-center text-gray-400 text-xs">
                Tidak ada tiket dalam kategori ini.
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Ticket Detail View + Action controls -->
    <div class="lg:col-span-8 bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col h-full">
        <template x-if="getSelectedTicket()">
            <div class="flex flex-col h-full overflow-hidden">
                <!-- Detail Header -->
                <div class="p-5 border-b border-gray-100 bg-slate-50/40 shrink-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-gray-800 font-display mt-0.5" x-text="getSelectedTicket().layanan"></h2>
                            <p class="text-[11px] text-gray-400 mt-1" x-text="'ID: ' + getSelectedTicket().id + ' | Pelapor: ' + getSelectedTicket().pengirimName"></p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-if="canReopen(getSelectedTicket())">
                                <button @click="reopenTicket(getSelectedTicket().id)" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Buka Kembali Tiket</span>
                                </button>
                            </template>
                            <span class="status-badge" :class="getStatusBadgeClass(getSelectedTicket().status)" x-text="getSelectedTicket().status === 'Kembalikan tiket ke operator' ? 'Ditolak' : (getSelectedTicket().status === 'Pending' ? 'New' : getSelectedTicket().status)"></span>
                        </div>
                    </div>
                </div>

                <!-- Warning Banner for Overdue Ticket -->
                <template x-if="isOverdue(getSelectedTicket())">
                    <div class="bg-rose-50 border-b border-rose-100 px-5 py-3 flex items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-rose-500 text-white rounded-full p-1 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-rose-800">PERINGATAN: Tiket Terlalu Lama Belum Diambil</h4>
                                <p class="text-[10px] text-rose-600 font-medium mt-0.5" x-text="'Tiket ini belum ditugaskan ke solver selama ' + getOverdueDuration(getSelectedTicket()) + '.'"></p>
                            </div>
                        </div>
                        <button @click="openAssignModal()" class="bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-extrabold px-3 py-1.5 rounded-xl transition-all shadow-xs cursor-pointer flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                            Tugaskan Sekarang
                        </button>
                    </div>
                </template>

                <!-- Scrollable body content -->
                <div class="flex-1 overflow-y-auto p-5 space-y-6">
                    <!-- Detail Section -->
                    <div class="bg-[#fffbeb]/20 border border-[#e8ceab]/30 p-4 rounded-xl space-y-2">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="info" class="w-4 h-4 text-[#b26d27]"></i>
                            <span>Deskripsi Masalah / Permintaan</span>
                        </h4>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium whitespace-pre-wrap" x-text="getSelectedTicket().detail"></p>
                    </div>

                    <!-- Flow Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-medium text-gray-600">
                        <div class="border border-slate-100 p-3 rounded-xl">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kategori Layanan</div>
                            <div class="text-gray-800 font-bold mt-1" x-text="getSelectedTicket().layananKategori + ' → ' + getSelectedTicket().layananSub"></div>
                        </div>
                        <div class="border border-slate-100 p-3 rounded-xl">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Status Penugasan Solver</div>
                            <div class="text-gray-800 font-bold mt-1">
                                <span x-text="getSelectedTicket().solverName ? getSelectedTicket().solverName : 'Belum Ditugaskan'"></span>
                                <template x-if="getSelectedTicket().solver2Name">
                                    <span x-text="' & ' + getSelectedTicket().solver2Name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 pt-5 space-y-3.5" x-show="getSelectedTicket().status !== 'Selesai' && getSelectedTicket().status !== 'Kembalikan tiket ke operator'">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Aksi Penanganan Tiket</h4>
                        
                        <div class="flex flex-wrap gap-2.5">
                            <!-- Accept Ticket -->
                            <button @click="acceptTicket()" 
                                    x-show="getSelectedTicket().status === 'Pending'"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Terima Tiket
                            </button>

                            <!-- Assign Solver -->
                            <button @click="openAssignModal()" 
                                    x-show="getSelectedTicket().status === 'Diterima' || getSelectedTicket().status === 'Ditugaskan' || getSelectedTicket().status === 'Dikerjakan' || getSelectedTicket().status === 'Dieskalasi'"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                Tugaskan Solver
                            </button>

                            <!-- Return to Operator -->
                            <button @click="openRejectModal()" 
                                    x-show="getSelectedTicket().status === 'Pending' || getSelectedTicket().status === 'Diterima' || getSelectedTicket().status === 'Dieskalasi'"
                                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                Kembalikan ke Operator
                            </button>

                            <!-- Complete Ticket -->
                            <button @click="openCompleteModal()" 
                                    x-show="getSelectedTicket().status === 'Dikerjakan' || getSelectedTicket().status === 'Ditugaskan'"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Selesaikan Tiket
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
                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300"></i>
                <p class="text-sm font-semibold">Pilih tiket dari inbox untuk mulai memproses.</p>
            </div>
        </template>
    </div>

    <!-- MODAL: Assign Solver -->
    <div x-show="assignModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4 relative">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                    <i data-lucide="user-check" class="text-blue-600 w-5 h-5"></i>
                    Tugaskan Solver TI
                </h3>
                <button @click="assignModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-slate-100 p-1.5 rounded-lg transition-all cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Solver 1 -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Pilih Personel Solver</label>
                    <button x-show="!showSolver2" @click="showSolver2 = true" type="button" class="text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded-lg transition-all text-xs font-bold flex items-center gap-1 cursor-pointer" title="Tambah Solver Kedua">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Solver</span>
                    </button>
                </div>
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <!-- Dropdown Trigger button -->
                    <button @click="dropdownOpen = !dropdownOpen" type="button" class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-semibold flex items-center justify-between cursor-pointer">
                        <span class="flex items-center gap-2">
                            <template x-if="solvers.find(s => s.id === selectedSolverId)">
                                <span class="flex items-center gap-2">
                                    <span class="text-gray-900 font-bold" x-text="solvers.find(s => s.id === selectedSolverId).name"></span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black border uppercase tracking-wider animate-in fade-in"
                                          :class="solvers.find(s => s.id === selectedSolverId).busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (solvers.find(s => s.id === selectedSolverId).busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                          x-text="solvers.find(s => s.id === selectedSolverId).busy_level + ' (' + solvers.find(s => s.id === selectedSolverId).assigned_today + '/6)'"></span>
                                </span>
                            </template>
                            <template x-if="!solvers.find(s => s.id === selectedSolverId)">
                                <span class="text-gray-400">Pilih Solver 1...</span>
                            </template>
                        </span>
                        <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu items -->
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition.origin.top.left class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100">
                        <template x-for="s in solvers" :key="s.id">
                            <button @click="if (s.busy_level !== 'Hi' && s.id !== selectedSolver2Id) { selectedSolverId = s.id; dropdownOpen = false; }" type="button" class="w-full text-left p-2.5 flex items-center justify-between text-xs transition-colors"
                                    :class="(s.busy_level === 'Hi' || s.id === selectedSolver2Id) ? 'opacity-45 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 cursor-pointer bg-white'">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold" :class="(s.busy_level === 'Hi' || s.id === selectedSolver2Id) ? 'text-gray-400' : 'text-gray-900'" x-text="s.name"></span>
                                    <span class="text-[10px] text-gray-400" x-text="'Tugas aktif: ' + s.assigned_today + '/6'"></span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black border uppercase tracking-wider"
                                      :class="s.busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (s.busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                      x-text="s.busy_level === 'Hi' ? 'Hi (Penuh)' : s.busy_level"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Solver 2 (Optional) -->
            <div x-show="showSolver2" x-transition class="space-y-1.5 pt-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Pilih Personel Solver 2</label>
                    <button @click="showSolver2 = false; selectedSolver2Id = null;" type="button" class="text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-all text-xs font-bold flex items-center gap-1 cursor-pointer" title="Hapus Solver 2">
                        <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                        <span>Hapus</span>
                    </button>
                </div>
                <div class="relative" x-data="{ dropdownOpen2: false }">
                    <button @click="dropdownOpen2 = !dropdownOpen2" type="button" class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-semibold flex items-center justify-between cursor-pointer">
                        <span class="flex items-center gap-2">
                            <template x-if="solvers.find(s => s.id === selectedSolver2Id)">
                                <span class="flex items-center gap-2">
                                    <span class="text-gray-900 font-bold" x-text="solvers.find(s => s.id === selectedSolver2Id).name"></span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black border uppercase tracking-wider animate-in fade-in"
                                          :class="solvers.find(s => s.id === selectedSolver2Id).busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (solvers.find(s => s.id === selectedSolver2Id).busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                          x-text="solvers.find(s => s.id === selectedSolver2Id).busy_level + ' (' + solvers.find(s => s.id === selectedSolver2Id).assigned_today + '/6)'"></span>
                                </span>
                            </template>
                            <template x-if="!solvers.find(s => s.id === selectedSolver2Id)">
                                <span class="text-gray-400">Pilih Solver 2...</span>
                            </template>
                        </span>
                        <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform" :class="dropdownOpen2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu items Solver 2 -->
                    <div x-show="dropdownOpen2" @click.away="dropdownOpen2 = false" x-transition.origin.top.left class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100">
                        <template x-for="s in solvers" :key="s.id">
                            <button @click="if (s.busy_level !== 'Hi' && s.id !== selectedSolverId) { selectedSolver2Id = s.id; dropdownOpen2 = false; }" type="button" class="w-full text-left p-2.5 flex items-center justify-between text-xs transition-colors"
                                    :class="(s.busy_level === 'Hi' || s.id === selectedSolverId) ? 'opacity-45 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 cursor-pointer bg-white'">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold" :class="(s.busy_level === 'Hi' || s.id === selectedSolverId) ? 'text-gray-400' : 'text-gray-900'" x-text="s.name"></span>
                                    <span class="text-[10px] text-gray-400" x-text="'Tugas aktif: ' + s.assigned_today + '/6'"></span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black border uppercase tracking-wider"
                                      :class="s.busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (s.busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                      x-text="s.busy_level === 'Hi' ? 'Hi (Penuh)' : s.busy_level"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button @click="assignModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button @click="confirmAssign()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Reject to Operator -->
    <div x-show="rejectModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4">
            <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                <i data-lucide="x-circle" class="text-rose-600 w-5 h-5"></i>
                Kembalikan ke Operator
            </h3>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Alasan Pengembalian</label>
                <textarea x-model="rejectReason" rows="3" placeholder="Masukkan alasan kenapa tiket dikembalikan ke operator..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-medium"></textarea>
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button @click="rejectModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold">Batal</button>
                <button @click="confirmReject()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold">Kirim</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Complete Ticket -->
    <div x-show="completeModalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-100 shadow-2xl text-gray-800 space-y-4">
            <h3 class="text-sm font-bold font-display text-gray-900 flex items-center gap-2">
                <i data-lucide="check-circle" class="text-emerald-600 w-5 h-5"></i>
                Selesaikan Tiket
            </h3>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Catatan Penyelesaian</label>
                <textarea x-model="completeNotes" rows="3" placeholder="Masukkan detail langkah perbaikan atau catatan penutupan tiket..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-medium"></textarea>
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button @click="completeModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold">Batal</button>
                <button @click="confirmComplete()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">Selesai</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function kasubbagPage() {
        return {
            tickets: [],
            searchQuery: '',
            selectedId: null,
            activeTab: 'pending',
            commentText: '',
            
            // Modals
            assignModalOpen: false,
            rejectModalOpen: false,
            completeModalOpen: false,
            
            selectedSolverId: '{{ $defaultSolverId }}',
            selectedSolver2Id: null,
            showSolver2: false,
            rejectReason: '',
            completeNotes: '',
            solvers: [
                @foreach($solvers as $solver)
                    { id: '{{ $solver->id }}', name: '{{ $solver->name }}', assigned_today: 0, busy_level: 'Low' },
                @endforeach
            ],

            init() {
                this.fetchTickets();
                window.addEventListener('search-tickets', (e) => {
                    this.searchQuery = e.detail;
                });
            },

            async fetchSolversBusyStatus() {
                try {
                    const res = await fetch('/api/solvers/busy-status');
                    const data = await res.json();
                    this.solvers = data;
                    
                    // If currently selected solver is now busy, change it to a non-busy one
                    const currentSelected = this.solvers.find(s => s.id === this.selectedSolverId);
                    if (currentSelected && currentSelected.busy_level === 'Hi') {
                        const firstNonBusy = this.solvers.find(s => s.busy_level !== 'Hi');
                        if (firstNonBusy) {
                            this.selectedSolverId = firstNonBusy.id;
                        }
                    }
                } catch (e) {
                    console.error('Failed to load solver busy status', e);
                }
            },

            async fetchTickets() {
                try {
                    const res = await fetch('/api/tickets');
                    this.tickets = await res.json();
                    
                    // Fetch solver busy status dynamically
                    await this.fetchSolversBusyStatus();
                    
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
                return this.tickets.filter(t => {
                    const isClosed = t.status === 'Selesai' || t.status === 'Kembalikan tiket ke operator';
                    
                    let tabMatches = false;
                    if (this.activeTab === 'pending') {
                        tabMatches = !isClosed && (!t.solverId || t.solverId === '');
                    } else if (this.activeTab === 'aktif') {
                        tabMatches = !isClosed && t.solverId && t.solverId !== '';
                    } else {
                        tabMatches = isClosed;
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
                    } else {
                        alert(res.error || 'Gagal membuka kembali tiket.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            isOverdue(t) {
                if (!t || !t.created_at) return false;
                
                // Tiket belum diambil jika solverId belum diisi dan status bukan Selesai/Ditolak
                const isNotTaken = !t.solverId || t.solverId === '';
                const isClosed = t.status === 'Selesai' || t.status === 'Kembalikan tiket ke operator';
                if (!isNotTaken || isClosed) return false;

                const createdTime = new Date(t.created_at).getTime();
                if (isNaN(createdTime)) return false;

                const now = Date.now();
                const elapsedMs = now - createdTime;
                const hours24Ms = 24 * 60 * 60 * 1000;

                return elapsedMs > hours24Ms;
            },

            getOverdueDuration(t) {
                if (!t || !t.created_at) return '';
                const createdTime = new Date(t.created_at).getTime();
                if (isNaN(createdTime)) return '';
                const elapsedMs = Date.now() - createdTime;
                
                const totalHours = Math.floor(elapsedMs / (1000 * 60 * 60));
                const days = Math.floor(totalHours / 24);
                const hours = totalHours % 24;
                
                if (days > 0) {
                    return `${days} hari ${hours} jam`;
                }
                return `${hours} jam`;
            },

            getStatusBadgeClass(status) {
                const s = status === 'Kembalikan tiket ke operator' ? 'Pending' : status;
                switch (s) {
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

            async acceptTicket() {
                const ticket = this.getSelectedTicket();
                if (!ticket) return;

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Diterima',
                            comment: {
                                text: 'Tiket diterima oleh Kasubbag TI.',
                                type: 'terima'
                            }
                        })
                    });

                    if (response.ok) {
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal memproses tiket.');
                }
            },

            openAssignModal() {
                const ticket = this.getSelectedTicket();
                if (ticket) {
                    this.selectedSolverId = ticket.solverId || '{{ $defaultSolverId }}';
                    this.selectedSolver2Id = ticket.solver2Id || null;
                    this.showSolver2 = !!ticket.solver2Id;
                } else {
                    this.selectedSolverId = '{{ $defaultSolverId }}';
                    this.selectedSolver2Id = null;
                    this.showSolver2 = false;
                }
                this.assignModalOpen = true;
            },

            async confirmAssign() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.selectedSolverId) {
                    alert('Harap pilih minimal 1 personel Solver!');
                    return;
                }

                const s1 = this.solvers.find(s => s.id === this.selectedSolverId);
                let s2 = null;
                if (this.showSolver2 && this.selectedSolver2Id) {
                    s2 = this.solvers.find(s => s.id === this.selectedSolver2Id);
                }

                const s1Name = s1 ? s1.name : '';
                const s2Name = s2 ? s2.name : null;

                let commentText = `Tiket ditugaskan kepada solver: ${s1Name} dan mulai dikerjakan.`;
                if (s2Name) {
                    commentText = `Tiket ditugaskan kepada 2 solver: ${s1Name} dan ${s2Name} dan mulai dikerjakan.`;
                }

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Dikerjakan',
                            solverId: this.selectedSolverId,
                            solverName: s1Name,
                            solver2Id: s2 ? s2.id : null,
                            solver2Name: s2Name,
                            comment: {
                                text: commentText,
                                type: 'penugasan'
                            }
                        })
                    });

                    if (response.ok) {
                        this.assignModalOpen = false;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal menugaskan solver.');
                }
            },

            openRejectModal() {
                this.rejectReason = '';
                this.rejectModalOpen = true;
            },

            async confirmReject() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.rejectReason.trim()) return;

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Kembalikan tiket ke operator',
                            alasanTolak: this.rejectReason.trim(),
                            comment: {
                                text: `Tiket dikembalikan ke operator. Alasan: ${this.rejectReason.trim()}`,
                                type: 'eskalasi'
                            }
                        })
                    });

                    if (response.ok) {
                        this.rejectModalOpen = false;
                        this.fetchTickets();
                    }
                } catch (e) {
                    alert('Gagal mengembalikan tiket.');
                }
            },

            openCompleteModal() {
                this.completeNotes = '';
                this.completeModalOpen = true;
            },

            async confirmComplete() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.completeNotes.trim()) return;

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
