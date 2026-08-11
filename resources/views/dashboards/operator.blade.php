@extends('layouts.app')

@section('title', 'Operator Dashboard - Portal Layanan TI BPK')

@section('content')
<div class="space-y-6 animate-in fade-in duration-300">
    @php
        $total = $tickets->count();
        $openCount = $tickets->filter(fn($t) => $t->status !== 'Selesai' && $t->status !== 'Kembalikan tiket ke operator')->count();
        $completedCount = $tickets->filter(fn($t) => $t->status === 'Selesai')->count();
        $rejectedCount = $tickets->filter(fn($t) => $t->status === 'Kembalikan tiket ke operator')->count();
        $escalatedCount = $tickets->filter(fn($t) => $t->status === 'Dieskalasi')->count();

        $openTicketsList = $tickets->filter(fn($t) => $t->status !== 'Selesai' && $t->status !== 'Kembalikan tiket ke operator');
        $closedTicketsList = $tickets->filter(fn($t) => $t->status === 'Selesai' || $t->status === 'Kembalikan tiket ke operator');
    @endphp

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-xs border border-[#e2e6ea] flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[#fcf4ec] text-[#b26d27] flex items-center justify-center">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Total Tiket</p>
                <h3 class="text-lg font-bold text-gray-800">{{ $total }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-xs border border-[#e2e6ea] flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Terbuka / Aktif</p>
                <h3 class="text-lg font-bold text-gray-800">{{ $openCount }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-xs border border-[#e2e6ea] flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Selesai</p>
                <h3 class="text-lg font-bold text-gray-800">{{ $completedCount }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-xs border border-[#e2e6ea] flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Kembali ke Op</p>
                <h3 class="text-lg font-bold text-gray-800">{{ $rejectedCount }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-xs border border-[#e2e6ea] col-span-2 lg:col-span-1 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 animate-pulse"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider font-mono">Eskalasi Solver</p>
                <h3 class="text-lg font-bold text-gray-800">{{ $escalatedCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Quick Navigation / Welcome -->
    <div class="bg-gradient-to-r from-[#fefaec] to-[#fffbeb] rounded-2xl p-5 border border-[#e8ceab]/80 shadow-xs flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800 font-display">Halo Operator Biro TI BPK!</h3>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Anda dapat melakukan penugasan ulang subbagian untuk tiket yang salah rute, serta memantau analitik secara terpusat.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('operator.tiket') }}" class="bg-[#b26d27] hover:bg-[#9b5a1b] text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Kelola Semua Tiket
            </a>
            <a href="{{ route('operator.analitik') }}" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                Tampilkan Analitik
            </a>
        </div>
    </div>

    <!-- Overview Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Table Open/Active Tickets -->
        <div class="bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col max-h-[400px]">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50 shrink-0">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-4 h-4 text-blue-600 animate-pulse"></i>
                    <span>Daftar Tiket Terbuka / Aktif ({{ $openTicketsList->count() }})</span>
                </h4>
            </div>
            <div class="flex-1 overflow-y-auto">
                @if($openTicketsList->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs font-medium">
                        Tidak ada tiket terbuka saat ini.
                    </div>
                @else
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 text-[10px]">
                                <th class="p-3 pl-4">ID</th>
                                <th class="p-3">Pelapor</th>
                                <th class="p-3">Layanan</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @foreach($openTicketsList as $t)
                                <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer" onclick="window.location.href='{{ route('operator.tiket', ['id' => $t->id]) }}'">
                                    <td class="p-3 pl-4 font-mono font-bold text-[#b26d27]">{{ $t->id }}</td>
                                    <td class="p-3 font-semibold text-gray-900">{{ $t->pengirimName }}</td>
                                    <td class="p-3 truncate max-w-[150px]">{{ $t->layanan }}</td>
                                    <td class="p-3">
                                        <span class="status-badge {{ $t->status === 'Overdue' ? 'status-overdue' : ($t->status === 'Dieskalasi' ? 'status-dieskalasi' : ($t->status === 'Dikerjakan' ? 'status-dikerjakan' : ($t->status === 'Ditugaskan' ? 'status-ditugaskan' : ($t->status === 'Diterima' ? 'status-diterima' : 'status-pending')))) }}">
                                            {{ $t->status === 'Pending' ? 'Pending / New' : $t->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Table Completed/Rejected Tickets -->
        <div class="bg-white border border-[#e2e6ea] rounded-2xl shadow-xs overflow-hidden flex flex-col max-h-[400px]">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50 shrink-0">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>Selesai / Kembali ke Op ({{ $closedTicketsList->count() }})</span>
                </h4>
            </div>
            <div class="flex-1 overflow-y-auto">
                @if($closedTicketsList->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs font-medium">
                        Belum ada riwayat tiket selesai.
                    </div>
                @else
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 text-[10px]">
                                <th class="p-3 pl-4">ID</th>
                                <th class="p-3">Pelapor</th>
                                <th class="p-3">Layanan</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @foreach($closedTicketsList as $t)
                                <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer" onclick="window.location.href='{{ route('operator.tiket', ['id' => $t->id]) }}'">
                                    <td class="p-3 pl-4 font-mono font-bold text-[#b26d27]">{{ $t->id }}</td>
                                    <td class="p-3 font-semibold text-gray-900">{{ $t->pengirimName }}</td>
                                    <td class="p-3 truncate max-w-[150px]">{{ $t->layanan }}</td>
                                    <td class="p-3">
                                        <span class="status-badge {{ $t->status === 'Selesai' ? 'status-selesai' : 'status-ditolak' }}">
                                            {{ $t->status === 'Kembalikan tiket ke operator' ? 'Dikembalikan' : $t->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
