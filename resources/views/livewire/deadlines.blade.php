<?php

use function Livewire\Volt\{layout, title, with, action};
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');
title('Project Deadlines');

with(function () {
    $today = Carbon::today();
    $nearDeadline = Carbon::today()->addDays(3);

    // Query dasar: Task milik user yang belum selesai
    $baseQuery = Task::whereHas('project', function($query) {
        $query->where('user_id', Auth::id());
    })->where('is_completed', false);

    return [
        // 1. Task yang sudah lewat tanggal hari ini
        'overdue' => (clone $baseQuery)
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            ->get(),

        // 2. Task yang jatuh tempo hari ini
        'dueToday' => (clone $baseQuery)
            ->whereDate('due_date', $today)
            ->get(),

        // 3. Task dalam 3 hari ke depan
        'upcoming' => (clone $baseQuery)
            ->whereBetween('due_date', [$today->copy()->addDay(), $nearDeadline])
            ->orderBy('due_date', 'asc')
            ->get(),
    ];
});

// Fungsi untuk menyelesaikan task langsung dari halaman deadline
$toggleTask = function ($id) {
    $task = Task::find($id);
    if ($task) {
        $task->update(['is_completed' => true]);
        
        // Catat ke Activity Feed
        \App\Models\Activity::log(
            "Menyelesaikan tugas via Deadlines: {$task->title}", 
            $task->project->title, 
            'task', 
            'completed'
        );

        $this->dispatch('notify', message: 'Tugas diselesaikan!', type: 'success');
    }
};

?>

<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Deadlines</h1>
        <p class="text-gray-500 font-medium mt-1">Pantau tugas-tugas kritis yang mendekati batas waktu.</p>
    </div>

    <div class="space-y-12">
        
        {{-- Section 1: OVERDUE --}}
        <section>
            <div class="flex items-center gap-3 mb-6">
                <div class="px-3 py-1 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded-full ring-4 ring-red-50">Overdue</div>
                <div class="h-px flex-1 bg-red-100"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($overdue as $task)
                    <div class="group bg-white border-2 border-red-100 p-5 rounded-[2rem] hover:shadow-xl transition-all relative overflow-hidden">
                        <div class="mb-2">
                            <span class="text-[9px] font-black uppercase px-2 py-1 bg-gray-100 text-gray-500 rounded-lg">
                                {{ $task->project->title }}
                            </span>
                        </div>
                        <h4 class="font-black text-gray-900 mb-1">{{ $task->title }}</h4>
                        <p class="text-xs text-red-500 font-bold italic">Terlambat {{ $task->due_date->diffInDays(now()) }} Hari</p>
                        
                        <div class="mt-4 flex justify-between items-end">
                            <span class="text-[10px] font-black uppercase text-gray-400">Jatuh Tempo: {{ $task->due_date->format('d M Y') }}</span>
                            <button wire:click="toggleTask({{ $task->id }})" class="bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-red-700 transition-colors">
                                Selesaikan
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-6 px-4 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-100 flex items-center gap-3">
                        <svg class="size-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <p class="text-sm text-gray-400 font-medium italic">Tidak ada tugas yang terlambat. Luar biasa!</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Section 2: DUE TODAY --}}
        <section>
            <div class="flex items-center gap-3 mb-6">
                <div class="px-3 py-1 bg-amber-100 text-amber-600 text-[10px] font-black uppercase rounded-full ring-4 ring-amber-50">Due Today</div>
                <div class="h-px flex-1 bg-amber-100"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($dueToday as $task)
                    <div class="bg-white border-2 border-amber-100 p-5 rounded-[2rem] hover:shadow-lg transition-all border-dashed">
                        <div class="mb-2">
                            <span class="text-[9px] font-black uppercase px-2 py-1 bg-gray-100 text-gray-500 rounded-lg">
                                {{ $task->project->title }}
                            </span>
                        </div>
                        <h4 class="font-black text-gray-900 mb-1">{{ $task->title }}</h4>
                        <p class="text-xs text-amber-600 font-bold italic">Harus selesai hari ini!</p>
                        <div class="mt-4 flex justify-end">
                             <button wire:click="toggleTask({{ $task->id }})" class="text-xs font-black text-amber-600 hover:underline">Selesaikan Sekarang →</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic pl-4">Aman. Tidak ada tugas kritis hari ini.</p>
                @endforelse
            </div>
        </section>

        {{-- Section 3: UPCOMING --}}
        <section>
            <div class="flex items-center gap-3 mb-6">
                <div class="px-3 py-1 bg-blue-50 text-blue-500 text-[10px] font-black uppercase rounded-full">Upcoming (3 Days)</div>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @forelse($upcoming as $task)
                    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-gray-100 hover:shadow-md transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 font-black text-xs">
                                {{ $task->due_date->format('d') }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">{{ $task->title }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $task->project->title }} • {{ $task->due_date->diffForHumans() }}</p>
                            </div>
                        </div>
                        <button wire:click="toggleTask({{ $task->id }})" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic pl-4">Belum ada deadline mendesak di depan.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>