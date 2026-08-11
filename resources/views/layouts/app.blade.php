<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAF4EE]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Layanan TI - BIRO TI BPK RI')</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FAF4EE;
        }
        .font-mono {
            font-family: 'Poppins', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2e6ea;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>
<body class="h-full text-gray-800 antialiased selection:bg-[#fcf4ec] selection:text-[#b26d27]" x-data="{ mobileMenuOpen: false }">

    @php
        $currentUser = Auth::user();
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
        $subbagName = $currentUser && $currentUser->subbagId ? ($subbagMaster[$currentUser->subbagId] ?? '') : '';

        // Role display helper
        $roleDisplay = 'Pegawai BPK RI';
        if ($currentUser) {
            switch ($currentUser->role) {
                case 'pengguna': $roleDisplay = 'Pegawai BPK RI'; break;
                case 'kasubbag': $roleDisplay = 'Kasubbag TI'; break;
                case 'solver': $roleDisplay = 'Solver TI'; break;
                case 'operator': $roleDisplay = 'Operator Biro TI'; break;
            }
        }

        // Sidebar nav items mapping
        $navItems = [];
        if ($currentUser) {
            if ($currentUser->role === 'pengguna') {
                $navItems = [
                    ['label' => 'Beranda', 'route' => 'dashboard', 'icon' => 'home'],
                    ['label' => 'Status Tiket', 'route' => 'dashboard.tiket', 'icon' => 'layers'],
                    ['label' => 'Detail Tiket', 'route' => 'dashboard.detail', 'icon' => 'file-text'],
                    ['label' => 'FAQ', 'route' => 'dashboard.faq', 'icon' => 'help-circle'],
                ];
            } elseif ($currentUser->role === 'kasubbag') {
                $navItems = [
                    ['label' => 'Inbox Tiket', 'route' => 'kasubbag', 'icon' => 'inbox'],
                ];
            } elseif ($currentUser->role === 'solver') {
                $navItems = [
                    ['label' => 'Tugas Saya', 'route' => 'solver', 'icon' => 'check-square'],
                ];
            } elseif ($currentUser->role === 'operator') {
                $navItems = [
                    ['label' => 'Overview', 'route' => 'operator', 'icon' => 'layout-dashboard'],
                    ['label' => 'Semua Tiket', 'route' => 'operator.tiket', 'icon' => 'file-spreadsheet'],
                    ['label' => 'Analitik', 'route' => 'operator.analitik', 'icon' => 'bar-chart-3'],
                    ['label' => 'Kelola FAQ', 'route' => 'operator.faq', 'icon' => 'help-circle'],
                ];
            }
        }
    @endphp

    <div class="min-h-screen flex flex-col">
        <!-- TOP BAR (Full Width) -->
        <header class="bg-white border-b border-[#e2e6ea] h-16 flex items-center justify-between px-6 z-30 shrink-0">
            <!-- Left Side: Brand Logo -->
            <div class="flex items-center gap-2.5">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-1.5 text-gray-500 hover:bg-slate-100 rounded-lg cursor-pointer">
                    <i data-lucide="menu" class="w-5.5 h-5.5"></i>
                </button>
                <a href="/" class="flex items-center gap-2.5">
                    <img src="/logo-melati.png" alt="Logo Melati" class="w-7 h-7 object-contain">
                    <span class="text-base font-extrabold text-[#b26d27] tracking-tight">Melati V2</span>
                </a>
            </div>



            <!-- Right Side: User Actions -->
            <div class="flex items-center gap-4.5">


                <!-- Notification Bell -->
                <div class="relative" x-data="{ 
                    open: false, 
                    notifications: [], 
                    userRole: '{{ Auth::user() ? Auth::user()->role : '' }}',
                    async fetchNotifications() {
                        try {
                            const res = await fetch('/api/notifications');
                            this.notifications = await res.json();
                        } catch(e) {}
                    },
                    async markAsRead() {
                        try {
                            await fetch('/api/notifications/read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                                }
                            });
                            // Fetch again to update read status
                            this.fetchNotifications();
                        } catch(e) {}
                    },
                    getNotificationUrl(n) {
                        if (!n.ticket_id) return '#';
                        if (this.userRole === 'pengguna') {
                            return '/dashboard/detail?id=' + n.ticket_id;
                        } else if (this.userRole === 'solver') {
                            return '/solver?id=' + n.ticket_id;
                        } else if (this.userRole === 'kasubbag') {
                            return '/kasubbag?id=' + n.ticket_id;
                        } else if (this.userRole === 'operator') {
                            return '/operator/tiket?id=' + n.ticket_id;
                        }
                        return '#';
                    },
                    handleNotificationClick(e, n) {
                        const targetUrl = this.getNotificationUrl(n);
                        if (targetUrl === '#') return;
                        
                        // If we are already on that exact URL path + search query, force a reload to get the latest comments/status
                        if (window.location.pathname + window.location.search === targetUrl) {
                            window.location.reload();
                        }
                    },
                    get unreadCount() {
                        return this.notifications.filter(n => !n.is_read).length;
                    },
                    init() {
                        this.fetchNotifications();
                        // Poll every 10 seconds
                        setInterval(() => this.fetchNotifications(), 10000);
                    }
                }" @click.away="open = false">
                    <button @click="open = !open; if(open) markAsRead()" class="text-gray-500 hover:text-gray-800 p-1.5 rounded-full hover:bg-slate-50 transition-all cursor-pointer relative flex items-center justify-center">
                        <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                        <span x-show="unreadCount > 0" class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                    </button>
 
                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2.5 w-80 bg-white border border-[#e2e6ea] rounded-2xl shadow-xl z-50 overflow-hidden" 
                         style="display: none;">
                        
                        <!-- Header -->
                        <div class="px-4 py-3 bg-slate-50 border-b border-[#e2e6ea] flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-800">Notifikasi</span>
                            <span x-show="unreadCount > 0" class="text-[10px] font-bold bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full" x-text="unreadCount + ' Baru'"></span>
                        </div>
 
                        <!-- Scrollable list -->
                        <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                            <!-- Template for notifications -->
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="getNotificationUrl(n)"
                                   @click="handleNotificationClick($event, n)"
                                   class="p-3.5 text-left block hover:bg-slate-50/50 transition-colors no-underline cursor-pointer"
                                   :class="!n.is_read ? 'bg-[#fffbeb]/20' : 'bg-transparent'">
                                    <div class="flex justify-between items-start gap-1.5 mb-1">
                                        <span class="text-[11px] font-bold text-gray-900" x-text="n.title"></span>
                                        <span class="text-[8px] text-gray-400 font-mono" x-text="new Date(n.created_at).toLocaleDateString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span>
                                    </div>
                                    <p class="text-[10px] text-gray-600 leading-relaxed font-medium" x-text="n.message"></p>
                                </a>
                            </template>

                            <!-- Empty state -->
                            <div x-show="notifications.length === 0" class="py-10 text-center text-gray-400 text-xs flex flex-col items-center justify-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><path d="M13.73 21a2 2 0 0 1-3.46 0"/><path d="M18.63 13 A17.89 17.89 0 0 1 18 8"/><path d="M6.26 6.26A5.86 5.86 0 0 0 6 8v7a3 3 0 0 1-3 3h15"/><path d="m2 2 20 20"/><path d="M18 8A6 6 0 0 0 8.13 4.13"/></svg>
                                <span>Tidak ada notifikasi.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Info -->
                <div class="flex items-center gap-2.5 pl-3 border-l border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 text-gray-700 flex items-center justify-center font-extrabold text-xs">
                        {{ $currentUser ? substr($currentUser->name, 0, 2) : 'US' }}
                    </div>
                    <div class="text-left leading-tight hidden sm:block">
                        <div class="text-xs font-bold text-gray-900">{{ $currentUser->name ?? '' }}</div>
                        <div class="text-[10px] text-gray-400 font-medium">{{ $roleDisplay }}</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- LAYOUT INNER CONTAINER -->
        <div class="flex-1 flex relative">
            
            <!-- DESKTOP SIDEBAR -->
            <aside class="hidden md:flex md:w-56 md:flex-col border-r border-[#e2e6ea] bg-white h-[calc(100vh-4rem)] shrink-0 select-none">
                <div class="flex flex-col h-full justify-between py-4">
                    <!-- Navigation Items -->
                    <nav class="px-3 space-y-1">
                        @foreach($navItems as $item)
                            @php
                                $isActive = Route::currentRouteName() === $item['route'];
                            @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isActive ? 'bg-[#fcf4ec] text-[#b26d27]' : 'text-gray-500 hover:bg-slate-50 hover:text-gray-800' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4.5 h-4.5"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <!-- Sidebar Footer / Logout -->
                    <div class="px-3">
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-3" onsubmit="localStorage.removeItem('melati_chat_history_' + '{{ Auth::user()->username }}'); localStorage.removeItem('melati_chat_history_time_' + '{{ Auth::user()->username }}');">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-xs font-bold text-gray-400 hover:text-red-600 rounded-xl transition-all cursor-pointer text-left">
                                <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- MOBILE DRAWER -->
            <div x-show="mobileMenuOpen" class="fixed inset-0 z-40 flex md:hidden" style="display: none;">
                <!-- Backdrop -->
                <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 transition-opacity"></div>
                
                <!-- Drawer Container -->
                <div class="relative flex flex-col w-56 bg-white border-r border-slate-100 h-full p-4 justify-between z-50">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#b26d27]">MENU</span>
                            <button @click="mobileMenuOpen = false" class="p-1 hover:bg-slate-50 rounded">
                                <i data-lucide="x" class="w-4.5 h-4.5"></i>
                            </button>
                        </div>
                        <nav class="space-y-1">
                            @foreach($navItems as $item)
                                @php
                                    $isActive = Route::currentRouteName() === $item['route'];
                                @endphp
                                <a href="{{ route($item['route']) }}" @click="mobileMenuOpen = false"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isActive ? 'bg-[#fcf4ec] text-[#b26d27]' : 'text-gray-500 hover:bg-slate-50 hover:text-gray-800' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-4.5 h-4.5"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-3" onsubmit="localStorage.removeItem('melati_chat_history_' + '{{ Auth::user()->username }}'); localStorage.removeItem('melati_chat_history_time_' + '{{ Auth::user()->username }}');">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-xs font-bold text-gray-400 hover:text-red-600 rounded-xl transition-all cursor-pointer text-left">
                                <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-6 overflow-y-auto h-[calc(100vh-4rem)] bg-[#FAF4EE]">
                <div class="max-w-7xl mx-auto h-full">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
