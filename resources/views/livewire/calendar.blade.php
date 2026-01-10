<?php

use function Livewire\Volt\{state, mount, layout};
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');

state([
    'currentMonth' => null,
    'currentYear' => null,
    'tasks' => [],
    'selectedDate' => null, // Tambahkan ini agar tidak undefined
    'showDetail' => false   // Tambahkan ini untuk kontrol modal
]);

mount(function () {
    $now = Carbon::now();
    $this->currentMonth = $now->month;
    $this->currentYear = $now->year;
    $this->loadTasks();
});

$loadTasks = function () {
    $start = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth();
    $end = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->endOfMonth();

    // Pastikan menggunakan with('project') agar nama project muncul di kalender
    $this->tasks = Task::with('project')
        ->whereHas('project', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->whereBetween('due_date', [$start, $end])
        ->get()
        ->groupBy(function ($date) {
            return Carbon::parse($date->due_date)->format('j');
        })
        ->toArray();
};

// Fungsi ini yang dipanggil saat tanggal diklik
$openDayDetail = function ($day) {
    $this->selectedDate = $day;
    $this->showDetail = true;
};

$changeMonth = function ($direction) {
    $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);

    if ($direction === 'next') {
        $date->addMonth();
    } else {
        $date->subMonth();
    }

    $this->currentMonth = $date->month;
    $this->currentYear = $date->year;
    $this->loadTasks();
};

?>

<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-neutral-800">Kalender Tugas</h2>
            <p class="text-sm text-neutral-500">Pantau jadwal deadline proyek Anda.</p>
        </div>

        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
            <button wire:click="changeMonth('prev')" class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <span class="px-4 font-bold text-gray-700 min-w-[150px] text-center">
                {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('id')->isoFormat('MMMM Y') }}
            </span>

            <button wire:click="changeMonth('next')" class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex-1 flex flex-col">
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
            <div class="py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                {{ $dayName }}
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 flex-1 auto-rows-fr">
            @php
            $date = Carbon::createFromDate($currentYear, $currentMonth, 1);
            $daysInMonth = $date->daysInMonth;
            $startDayOfWeek = $date->dayOfWeek;
            $today = Carbon::now();
            @endphp

            @for ($i = 0; $i < $startDayOfWeek; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/30 min-h-[120px]">
        </div>
        @endfor

        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
            $isToday=$today->day == $day && $today->month == $currentMonth && $today->year == $currentYear;
            $dayTasks = $tasks[$day] ?? [];
            @endphp

            <div wire:click="openDayDetail({{ $day }})"
                class="border-b border-r border-gray-100 p-2 min-h-[120px] relative hover:bg-blue-50/20 transition cursor-pointer group">

                <!-- Nomor tanggal -->
                <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full
                    {{ $isToday ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-700 group-hover:bg-gray-100' }}">
                    {{ $day }}
                </span>

                <!-- Preview task (tetap dipertahankan) -->
                <div class="mt-2 space-y-1 overflow-hidden">
                    @foreach(array_slice($dayTasks, 0, 3) as $task)
                    <div class="text-[9px] px-1.5 py-0.5 rounded border truncate
                {{ $task['is_completed']
                    ? 'bg-green-50 text-green-700 border-green-100 opacity-60'
                    : 'bg-white text-blue-700 border-blue-100 shadow-sm' }}">
                        <span class="font-bold">
                            [{{ $task['project']['title'] ?? 'N/A' }}]
                        </span>
                        {{ $task['title'] }}
                    </div>
                    @endforeach

                    @if(count($dayTasks) > 3)
                    <div class="text-[9px] text-gray-400 font-bold pl-1">
                        +{{ count($dayTasks) - 3 }} lainnya
                    </div>
                    @endif
                </div>

                <!-- Tombol + (MUNCUL SAAT HOVER) -->
                <div
                    class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100
               transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                    <div class="bg-blue-600 text-white p-1 rounded-lg shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
            </div>

            @endfor

            @php
            $totalCells = $startDayOfWeek + $daysInMonth;
            $remainingCells = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for ($i = 0; $i < $remainingCells; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/30">
    </div>
    @endfor
</div>
</div>

@if($showDetail)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-300">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800 text-xl">Tugas: {{ $selectedDate }} {{ \Carbon\Carbon::create(null, $currentMonth)->locale('id')->monthName }}</h3>
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Detail Pekerjaan</p>
            </div>
            <button wire:click="$set('showDetail', false)" class="w-10 h-10 flex items-center justify-center hover:bg-white rounded-full transition text-gray-400 hover:text-red-500 shadow-sm border border-gray-100">✕</button>
        </div>

        <div class="p-6 max-h-[450px] overflow-y-auto space-y-3 bg-white">
            @forelse($tasks[$selectedDate] ?? [] as $task)
            <a href="{{ route('dashboard', ['open_project' => $task['project_id']]) }}"
                wire:navigate
                class="flex items-center justify-between p-4 rounded-2xl border-2 border-gray-50 hover:border-blue-500 hover:bg-blue-50/50 transition-all group">
                <div class="flex-1 pr-4">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-tighter">{{ $task['project']['title'] ?? 'Proyek' }}</span>
                    <h4 class="font-bold text-gray-800 leading-tight group-hover:text-blue-700 transition-colors">{{ $task['title'] }}</h4>
                </div>
                <div class="flex items-center gap-2">
                    @if($task['is_completed'])
                    <span class="bg-green-100 text-green-600 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg></span>
                    @endif
                    <svg class="w-5 h-5 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-gray-200 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-gray-400 font-medium italic">Hari ini kosong.</p>
            </div>
            @endforelse
        </div>

        <div class="p-6 bg-gray-50/80 border-t border-gray-100">
            <a href="{{ route('dashboard') }}" wire:navigate class="w-full flex items-center justify-center gap-3 py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Task di Dashboard
            </a>
        </div>
    </div>
</div>
@endif
</div>