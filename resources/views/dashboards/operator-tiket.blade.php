    @extends('layouts.app')

@section('title', 'Semua Tiket - Operator Biro TI BPK')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8.5rem)] animate-in fade-in duration-300" x-data="operatorTicketsPage()">
    <!-- LEFT PANEL: Ticket List with Filters (Col 5) -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col h-full overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-[#e2e6ea] space-y-3 shrink-0">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-gray-800 font-display">Daftar Semua Tiket</h3>
                <span class="text-[10px] text-gray-400 font-mono" x-text="filteredTickets().length + ' tiket'"></span>
            </div>
            
            <!-- Search Input -->
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari ID, pelapor, detail..." class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl pl-9.5 pr-4 py-2 text-xs outline-none transition-all placeholder:text-gray-400 font-medium">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            </div>

            <!-- Filters -->
            <div class="mb-4" x-data="{ openStatusDropdown: false }">
                <label class="block text-[8px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider font-mono">Status</label>
                <div class="relative" @click.away="openStatusDropdown = false">
                    <!-- Dropdown Trigger button -->
                    <button @click="openStatusDropdown = !openStatusDropdown" type="button" 
                            class="w-full flex items-center justify-between bg-white border border-[#b26d27] text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-bold cursor-pointer">
                        <span class="truncate pr-2" x-text="statusFilter === 'All' ? 'Semua Status' : (statusFilter === 'Pending' ? 'Pending / New' : (statusFilter === 'Kembalikan tiket ke operator' ? 'Dikembalikan' : statusFilter))"></span>
                        <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform shrink-0" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu items -->
                    <div x-show="openStatusDropdown" 
                         x-transition.origin.top.left 
                         class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100"
                         style="display: none;">
                        <button @click="statusFilter = 'All'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'All' ? 'text-[#b26d27]' : ''">Semua Status</button>
                        <button @click="statusFilter = 'Pending'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Pending' ? 'text-[#b26d27]' : ''">Pending / New</button>
                        <button @click="statusFilter = 'Ditugaskan'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Ditugaskan' ? 'text-[#b26d27]' : ''">Ditugaskan</button>
                        <button @click="statusFilter = 'Dikerjakan'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Dikerjakan' ? 'text-[#b26d27]' : ''">Dikerjakan</button>
                        <button @click="statusFilter = 'Dieskalasi'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Dieskalasi' ? 'text-[#b26d27]' : ''">Dieskalasi</button>
                        <button @click="statusFilter = 'Overdue'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Overdue' ? 'text-[#b26d27]' : ''">Overdue</button>
                        <button @click="statusFilter = 'Selesai'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Selesai' ? 'text-[#b26d27]' : ''">Selesai</button>
                        <button @click="statusFilter = 'Kembalikan tiket ke operator'; openStatusDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="statusFilter === 'Kembalikan tiket ke operator' ? 'text-[#b26d27]' : ''">Dikembalikan</button>
                    </div>
                </div>
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
                    <h4 class="text-xs font-bold text-gray-900 truncate" x-text="t.layanan"></h4>
                    <p class="text-[10px] text-gray-500 truncate" x-text="'Pelapor: ' + t.pengirimName"></p>
                    
                    <div class="flex items-center justify-between gap-2 mt-2.5">
                        <span class="status-badge font-semibold" :class="getStatusBadgeClass(t.status)" x-text="t.status === 'Kembalikan tiket ke operator' ? 'Dikembalikan' : (t.status === 'Pending' ? 'Pending / New' : t.status)"></span>
                        <template x-if="t.bisa_remote">
                            <span class="bg-emerald-100 text-emerald-800 text-[8px] font-black px-1.5 py-0.5 rounded uppercase font-mono flex items-center gap-0.5" title="Dapat diselesaikan secara online">
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                Online
                            </span>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="filteredTickets().length === 0" class="p-8 text-center text-gray-400 text-xs">
                Tidak ada tiket ditemukan.
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
                            <h2 class="text-base font-bold text-gray-800 font-display mt-0.5" x-text="getSelectedTicket().layanan"></h2>
                            <p class="text-[11px] text-gray-400 mt-1" x-text="'ID: ' + getSelectedTicket().id + ' | Pelapor: ' + getSelectedTicket().pengirimName"></p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <template x-if="canReopen(getSelectedTicket())">
                                <button @click="reopenTicket(getSelectedTicket().id)" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Buka Kembali Tiket</span>
                                </button>
                            </template>
                            <template x-if="getSelectedTicket().status === 'Overdue'">
                                <span class="bg-rose-50 border border-rose-200 text-rose-800 text-[10px] font-bold px-2.5 py-1 rounded-xl flex items-center gap-1.5 animate-pulse">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-rose-600"></i>
                                    <span x-text="'Telat ' + getOverdueHours(getSelectedTicket()) + ' jam'"></span>
                                </span>
                            </template>
                            <span class="status-badge" :class="getStatusBadgeClass(getSelectedTicket().status)" x-text="getSelectedTicket().status === 'Kembalikan tiket ke operator' ? 'Dikembalikan' : (getSelectedTicket().status === 'Pending' ? 'Pending / New' : getSelectedTicket().status)"></span>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-medium text-gray-600">
                        <div class="border border-slate-100 p-3 rounded-xl space-y-1">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Rute Subbagian</div>
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
                        <div class="border border-slate-100 p-3 rounded-xl flex flex-col justify-center space-y-1">
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Metode Penanganan</div>
                            <div>
                                <template x-if="getSelectedTicket().bisa_remote">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold rounded-lg shadow-2xs">
                                        <i data-lucide="globe" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>Dapat Diselesaikan secara Online (Remote)</span>
                                    </span>
                                </template>
                                <template x-if="!getSelectedTicket().bisa_remote">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold rounded-lg shadow-2xs">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>Penanganan Fisik / On-Site</span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- ASSIGNMENT SECTIONS (Reassign & Direct Assign Solver) -->
                    <div class="border-t border-slate-100 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4" x-show="getSelectedTicket().status !== 'Selesai'">
                        <!-- LEFT COLUMN: Reassign Subbagian -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Penugasan Ulang Subbagian (Reassign)</h4>
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col gap-3.5 justify-between min-h-[120px]">
                                <div class="w-full" x-data="{ open: false }" @click.away="open = false">
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider font-mono">Pindahkan Subbagian TI</label>
                                    <div class="relative">
                                        <!-- Dropdown Trigger button -->
                                        <button @click="open = !open" type="button" class="w-full bg-white border border-[#b26d27] text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-bold flex items-center justify-between cursor-pointer">
                                            <span class="truncate pr-2" x-text="selectedSubbagId && subbags[selectedSubbagId] ? subbags[selectedSubbagId].replace('Subbagian ', '') : 'Pilih Subbagian...'"></span>
                                            <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Dropdown Menu items -->
                                        <div x-show="open" 
                                             x-transition.origin.top.left 
                                             class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100"
                                             style="display: none;">
                                            <template x-for="(name, id) in subbags" :key="id">
                                                <button @click="selectedSubbagId = id; open = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer truncate" :class="selectedSubbagId === id ? 'text-[#b26d27]' : ''" x-text="name">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <button @click="reassignTicket()" 
                                        :disabled="!selectedSubbagId"
                                        :class="!selectedSubbagId ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-[#b26d27] hover:bg-[#9b5a1b] text-white cursor-pointer'"
                                        class="w-full font-bold text-xs py-3 px-4 rounded-xl transition-all shadow-sm hover:shadow-md">
                                    Pindahkan Rute
                                </button>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Assign directly to solver -->
                        <div class="space-y-2" x-show="getSelectedTicket().status !== 'Selesai'">
                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Tugaskan Langsung ke Solver</h4>
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col gap-3.5 justify-between min-h-[120px]">
                                <!-- Solver 1 -->
                                <div class="w-full space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Pilih Personel Solver 1</label>
                                        <button x-show="!showSolver2" @click="showSolver2 = true" type="button" class="text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2 py-0.5 rounded-lg transition-all text-[10px] font-bold flex items-center gap-1 cursor-pointer" title="Tambah Solver Kedua">
                                            <i data-lucide="plus" class="w-3 h-3"></i>
                                            <span>Tambah Solver</span>
                                        </button>
                                    </div>
                                    <div class="relative" x-data="{ dropdownOpen: false }">
                                        <!-- Dropdown Trigger button -->
                                        <button @click="dropdownOpen = !dropdownOpen" type="button" class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-semibold flex items-center justify-between cursor-pointer">
                                            <span class="flex items-center gap-2 min-w-0">
                                                <template x-if="solvers.find(s => s.id === selectedSolverId)">
                                                    <span class="flex items-center gap-2 text-left min-w-0">
                                                        <span class="text-gray-900 truncate max-w-[150px] sm:max-w-[200px]" x-text="solvers.find(s => s.id === selectedSolverId).name"></span>
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-black border uppercase tracking-wider animate-in fade-in shrink-0"
                                                              :class="solvers.find(s => s.id === selectedSolverId).busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (solvers.find(s => s.id === selectedSolverId).busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                                              x-text="solvers.find(s => s.id === selectedSolverId).busy_level + ' (' + solvers.find(s => s.id === selectedSolverId).assigned_today + '/6)'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!solvers.find(s => s.id === selectedSolverId)">
                                                    <span class="text-gray-400">Pilih Solver 1...</span>
                                                </template>
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform shrink-0" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu items -->
                                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition.origin.top.left class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100">
                                            <template x-for="s in solvers.filter(solver => !selectedSubbagId || solver.subbagId === selectedSubbagId)" :key="s.id">
                                                <button @click="if (s.busy_level !== 'Hi' && s.id !== selectedSolver2Id) { selectedSolverId = s.id; dropdownOpen = false; }" type="button" class="w-full text-left p-3 flex items-center justify-between text-xs transition-colors bg-white hover:bg-slate-50 cursor-pointer"
                                                        :class="(s.busy_level === 'Hi' || s.id === selectedSolver2Id) ? 'opacity-45 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 cursor-pointer bg-white'">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="font-bold text-gray-900" x-text="s.name"></span>
                                                        <span class="text-[10px] text-gray-400" x-text="(subbags[s.subbagId] ? subbags[s.subbagId].replace('Subbagian ', '') : '') + ' | Tugas aktif: ' + s.assigned_today + '/6'"></span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-black border uppercase tracking-wider shrink-0"
                                                          :class="s.busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (s.busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                                          x-text="s.busy_level === 'Hi' ? 'Hi (Penuh)' : s.busy_level"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Solver 2 (Optional) -->
                                <div x-show="showSolver2" x-transition class="w-full space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Pilih Personel Solver 2</label>
                                        <button @click="showSolver2 = false; selectedSolver2Id = '';" type="button" class="text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded-lg transition-all text-[10px] font-bold flex items-center gap-1 cursor-pointer" title="Hapus Solver 2">
                                            <i data-lucide="minus" class="w-3 h-3"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                    <div class="relative" x-data="{ dropdownOpen2: false }">
                                        <button @click="dropdownOpen2 = !dropdownOpen2" type="button" class="w-full bg-white border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl px-4 py-3 text-xs outline-none transition-all font-semibold flex items-center justify-between cursor-pointer">
                                            <span class="flex items-center gap-2 min-w-0">
                                                <template x-if="solvers.find(s => s.id === selectedSolver2Id)">
                                                    <span class="flex items-center gap-2 text-left min-w-0">
                                                        <span class="text-gray-900 truncate max-w-[150px] sm:max-w-[200px]" x-text="solvers.find(s => s.id === selectedSolver2Id).name"></span>
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-black border uppercase tracking-wider animate-in fade-in shrink-0"
                                                              :class="solvers.find(s => s.id === selectedSolver2Id).busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (solvers.find(s => s.id === selectedSolver2Id).busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                                              x-text="solvers.find(s => s.id === selectedSolver2Id).busy_level + ' (' + solvers.find(s => s.id === selectedSolver2Id).assigned_today + '/6)'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!solvers.find(s => s.id === selectedSolver2Id)">
                                                    <span class="text-gray-400">Pilih Solver 2...</span>
                                                </template>
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform shrink-0" :class="dropdownOpen2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu items Solver 2 -->
                                        <div x-show="dropdownOpen2" @click.away="dropdownOpen2 = false" x-transition.origin.top.left class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100">
                                            <template x-for="s in solvers.filter(solver => (!selectedSubbagId || solver.subbagId === selectedSubbagId) && solver.id !== selectedSolverId)" :key="s.id">
                                                <button @click="if (s.busy_level !== 'Hi') { selectedSolver2Id = s.id; dropdownOpen2 = false; }" type="button" class="w-full text-left p-3 flex items-center justify-between text-xs transition-colors bg-white hover:bg-slate-50 cursor-pointer"
                                                        :class="s.busy_level === 'Hi' ? 'opacity-45 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 cursor-pointer bg-white'">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="font-bold text-gray-900" x-text="s.name"></span>
                                                        <span class="text-[10px] text-gray-400" x-text="(subbags[s.subbagId] ? subbags[s.subbagId].replace('Subbagian ', '') : '') + ' | Tugas aktif: ' + s.assigned_today + '/6'"></span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-black border uppercase tracking-wider shrink-0"
                                                          :class="s.busy_level === 'Hi' ? 'bg-rose-100 text-rose-800 border-rose-200' : (s.busy_level === 'Med' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200')"
                                                          x-text="s.busy_level === 'Hi' ? 'Hi (Penuh)' : s.busy_level"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <button @click="assignTicketToSolver()" 
                                        :disabled="!selectedSolverId"
                                        :class="!selectedSolverId ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer'"
                                        class="w-full font-bold text-xs py-3 px-4 rounded-xl transition-all shadow-sm hover:shadow-md">
                                    Tugaskan Solver
                                </button>
                            </div>
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
                                <div class="space-y-3.5 max-h-[250px] overflow-y-auto pr-1" id="comments-box">
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
                                            <template x-if="c.text && (c.text.includes('meet.google.com') || c.text.includes('Google Meet'))">
                                                <div class="mt-2.5 pt-2 border-t border-slate-200/60">
                                                    <a :href="extractMeetUrl(c.text)" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                                                        <i data-lucide="video" class="w-3.5 h-3.5"></i>
                                                        <span>Gabung Sesi Google Meet</span>
                                                        <i data-lucide="external-link" class="w-3 h-3 ml-0.5 opacity-80"></i>
                                                    </a>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <div x-show="getSelectedTicket().comments.filter(c => !['sistem', 'terima', 'penugasan', 'mulai_kerjakan', 'penyelesaian', 'eskalasi', 'tindaklanjuti'].includes(c.type)).length === 0" class="text-center py-6 text-gray-400 text-xs font-medium">
                                        Belum ada obrolan.
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN: Log Aktivitas -->
                            <div class="flex flex-col space-y-3">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="history" class="w-4 h-4 text-[#b26d27]"></i>
                                    <span>Log Aktivitas Sistem</span>
                                </h3>

                                <!-- Logs Thread -->
                                <div class="space-y-3.5 max-h-[250px] overflow-y-auto pr-1" id="logs-box">
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
                <i data-lucide="layers" class="w-12 h-12 mx-auto text-gray-300"></i>
                <p class="text-sm font-semibold">Pilih tiket dari daftar di sebelah kiri untuk melihat detail.</p>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function operatorTicketsPage() {
        return {
            tickets: @json($tickets),
            solvers: @json($solvers),
            selectedId: null,
            search: '',
            statusFilter: 'All',
            selectedSubbagId: '',
            selectedSolverId: '',
            selectedSolver2Id: '',
            showSolver2: false,

            subbags: {
                'k1': 'Subbagian Pengelolaan Infrastruktur dan Jaringan',
                'k2': 'Subbagian Pelayanan TIK',
                'k3': 'Subbagian Pengembangan Sistem Informasi Pemeriksaan',
                'k4': 'Subbagian Pengembangan Sistem Informasi Kelembagaan',
                'k5': 'Subbagian Sains Data',
                'k6': 'Subbagian Tata Kelola Data',
                'k7': 'Subbagian Keamanan Informasi',
                'k8': 'Subbagian MIOT'
            },

            init() {
                // Preselect from query params
                const params = new URLSearchParams(window.location.search);
                const qId = params.get('id');
                if (qId) {
                    this.selectedId = qId;
                    this.statusFilter = 'All';
                }
                
                const displayed = this.filteredTickets();
                if (displayed.length > 0 && !this.selectedId) {
                    this.selectedId = displayed[0].id;
                }
                
                const selected = this.getSelectedTicket();
                if (selected) {
                    this.selectedSubbagId = selected.kasubbagId;
                    this.selectedSolverId = selected.solverId || '';
                    this.selectedSolver2Id = selected.solver2Id || '';
                    this.showSolver2 = !!selected.solver2Id;
                }

                // Listen to selectedSubbagId changes to reset selectedSolverId if it doesn't match
                this.$watch('selectedSubbagId', (value) => {
                    const currentSolver = this.solvers.find(s => s.id === this.selectedSolverId);
                    if (currentSolver && currentSolver.subbagId !== value) {
                        this.selectedSolverId = '';
                    }
                });

                // Listen to global header search box
                window.addEventListener('search-tickets', (e) => {
                    this.search = e.detail;
                });
                
                // Fetch dynamic busy status
                this.fetchSolversBusyStatus();
                
                this.$nextTick(() => {
                    lucide.createIcons();
                    this.scrollComments();
                });
            },

            async fetchTickets() {
                try {
                    const res = await fetch('/api/tickets');
                    this.tickets = await res.json();
                    
                    await this.fetchSolversBusyStatus();
                    
                    const displayed = this.filteredTickets();
                    if (displayed.length > 0 && !this.selectedId) {
                        this.selectedId = displayed[0].id;
                    }
                    
                    const selected = this.getSelectedTicket();
                    if (selected) {
                        this.selectedSubbagId = selected.kasubbagId;
                        this.selectedSolverId = selected.solverId || '';
                        this.selectedSolver2Id = selected.solver2Id || '';
                        this.showSolver2 = !!selected.solver2Id;
                    }
                    
                    this.$nextTick(() => {
                        lucide.createIcons();
                        this.scrollComments();
                    });
                } catch (e) {
                    console.error(e);
                }
            },

            filteredTickets() {
                return this.tickets.filter(t => {
                    // Status filter
                    if (this.statusFilter !== 'All') {
                        if (t.status !== this.statusFilter) return false;
                    }
                    
                    // Search box
                    if (this.search) {
                        const s = this.search.toLowerCase();
                        return t.id.toLowerCase().includes(s) ||
                               t.pengirimName.toLowerCase().includes(s) ||
                               t.layanan.toLowerCase().includes(s) ||
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
                const t = this.getSelectedTicket();
                if (t) {
                    this.selectedSubbagId = t.kasubbagId;
                    this.selectedSolverId = t.solverId || '';
                    this.selectedSolver2Id = t.solver2Id || '';
                    this.showSolver2 = !!t.solver2Id;
                }
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

            getStatusBadgeClass(status) {
                if (status === 'Kembalikan tiket ke operator') {
                    return 'status-ditolak';
                }
                switch (status) {
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

            getOverdueHours(t) {
                if (!t || !t.created_at) return 0;
                const createdTime = new Date(t.created_at).getTime();
                if (isNaN(createdTime)) return 0;
                const elapsedMs = Date.now() - createdTime;
                const totalHours = Math.floor(elapsedMs / (1000 * 60 * 60));
                return Math.max(0, totalHours - 24);
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

            getRoleBadgeClass(role) {
                if (role === 'pengguna') return 'bg-[#fcf4ec] text-[#b26d27]';
                if (role === 'kasubbag') return 'bg-blue-100 text-blue-800';
                if (role === 'solver') return 'bg-purple-100 text-purple-800';
                return 'bg-slate-100 text-slate-700';
            },

            async reassignTicket() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.selectedSubbagId) return;

                const subbagName = this.subbags[this.selectedSubbagId];
                const operatorName = '{{ Auth::user()->name }}';

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Pending',
                            kasubbagId: this.selectedSubbagId,
                            kasubbagName: `Kasubbag ${subbagName}`,
                            solverId: '',
                            solverName: '',
                            solver2Id: '',
                            solver2Name: '',
                            comment: {
                                text: `Operator ${operatorName} mengalihkan rute tiket ke ${subbagName}.`,
                                type: 'penugasan'
                            }
                        })
                    });

                    if (response.ok) {
                        this.fetchTickets();
                    } else {
                        alert('Gagal memindahkan rute tiket.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async fetchSolversBusyStatus() {
                try {
                    const res = await fetch('/api/solvers/busy-status');
                    const data = await res.json();
                    this.solvers = data;
                } catch (e) {
                    console.error('Failed to load solver busy status', e);
                }
            },

            async assignTicketToSolver() {
                const ticket = this.getSelectedTicket();
                if (!ticket || !this.selectedSolverId) return;

                const solver1 = this.solvers.find(s => s.id === this.selectedSolverId);
                if (!solver1) return;

                let solver2 = null;
                if (this.showSolver2 && this.selectedSolver2Id) {
                    solver2 = this.solvers.find(s => s.id === this.selectedSolver2Id);
                }

                const subbagName = this.subbags[solver1.subbagId] || '';
                const operatorName = '{{ Auth::user()->name }}';

                let commentText = `Operator ${operatorName} menugaskan tiket langsung kepada solver: ${solver1.name}.`;
                if (solver2) {
                    commentText = `Operator ${operatorName} menugaskan tiket langsung kepada 2 solver: ${solver1.name} dan ${solver2.name}.`;
                }

                try {
                    const response = await fetch(`/api/tickets/${ticket.id}/actions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'Ditugaskan',
                            kasubbagId: solver1.subbagId,
                            kasubbagName: `Kasubbag ${subbagName}`,
                            solverId: solver1.id,
                            solverName: solver1.name,
                            solver2Id: solver2 ? solver2.id : null,
                            solver2Name: solver2 ? solver2.name : null,
                            comment: {
                                text: commentText,
                                type: 'penugasan'
                            }
                        })
                    });

                    if (response.ok) {
                        this.fetchTickets();
                    } else {
                        alert('Gagal menugaskan solver.');
                    }
                } catch (e) {
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
            },

            extractMeetUrl(text) {
                if (!text) return 'https://meet.google.com/new';
                const match = text.match(/https?:\/\/meet\.google\.com\/[^\s]+/);
                return match ? match[0] : 'https://meet.google.com/new';
            }
        };
    }
</script>
@endpush
