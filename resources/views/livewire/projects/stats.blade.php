<?php

use function Livewire\Volt\{state, on, with};
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

// Listener agar angka otomatis berubah jika ada perubahan di index/task-manager
on(['project-updated' => function () {}]);

with(fn () => [
    'totalProjects' => Project::where('user_id', Auth::id())->count(),
    'ongoingProjects' => Project::where('user_id', Auth::id())
        ->where('status', 'on_progress')->count(),
    'overdueTasks' => Task::whereHas('project', fn($q) => $q->where('user_id', Auth::id()))
        ->where('is_completed', false)
        ->where('due_date', '<', now())
        ->count(),
]);

?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="group relative overflow-hidden bg-white p-6 rounded-[2rem] border border-neutral-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Proyek</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl font-black text-neutral-800">{{ $totalProjects }}</h3>
                    <span class="text-xs font-medium text-neutral-400">Folder</span>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-2 -right-2 h-16 w-16 text-neutral-50 opacity-[0.05] group-hover:scale-110 transition-transform">
             <svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
    </div>

    <div class="group relative overflow-hidden bg-white p-6 rounded-[2rem] border border-neutral-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-400">On Progress</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $ongoingProjects }}</h3>
            </div>
        </div>
        <div class="absolute -bottom-2 -right-2 h-16 w-16 text-neutral-50 opacity-[0.05]">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
    </div>

    <div class="group relative overflow-hidden bg-white p-6 rounded-[2rem] border-2 border-red-50 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-red-400">Urgent</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl font-black text-neutral-800">{{ $overdueTasks }}</h3>
                    <span class="text-xs font-medium text-red-400">Overdue</span>
                </div>
            </div>
        </div>
    </div>
</div>