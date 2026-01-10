<?php

use function Livewire\Volt\{state, layout, title, with};

use App\Exports\ExportAnalytics;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Exports\ProjectReportExport; // IMPORT INI
use Maatwebsite\Excel\Facades\Excel; // IMPORT INI

layout('components.layouts.app');
title('Analytics');

with(function () {
    $userId = Auth::id();

    // Statistik Dasar
    $projects = Project::where('user_id', $userId)->get();
    $totalProjects = $projects->count();
    $completedProjects = $projects->where('status', 'completed')->count();
    $onProgressProjects = $projects->where('status', 'on_progress')->count();
    $pendingProjects = $projects->where('status', 'pending')->count();

    // Statistik Tugas
    $tasksQuery = Task::whereHas('project', fn($q) => $q->where('user_id', $userId));
    $totalTasks = $tasksQuery->count();
    $completedTasks = (clone $tasksQuery)->where('is_completed', true)->count();

    // List 5 Tugas Terlambat (Overdue) untuk ditampilkan di tabel bawah
    $urgentTasks = (clone $tasksQuery)
        ->where('is_completed', false)
        ->where('due_date', '<', now())
        ->orderBy('due_date', 'asc')
        ->take(5)
        ->get();

    return [
        'stats' => [
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'on_progress_projects' => $onProgressProjects,
            'pending_projects' => $pendingProjects,
            'total_tasks' => $totalTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            'urgent_tasks' => $urgentTasks
        ]
    ];
});

$exportExcel = function () {
    $namaFile = 'Laporan_Proyek_' . now()->format('d-m-Y') . '.xlsx';
    return Excel::download(new ProjectReportExport, $namaFile);
};

?>

<div class="p-6 space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">Productivity Analytics</h1>
            <p class="text-gray-500 text-sm font-medium">Data performa proyek Anda secara real-time.</p>
        </div>
        <button
            wire:click="exportExcel"
            wire:loading.attr="disabled"
            class="group relative bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl transition-all duration-200 shadow-lg shadow-gray-200 flex items-center gap-2 font-bold text-sm cursor-pointer active:scale-95 disabled:opacity-75">
            <div wire:loading wire:target="exportExcel">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <svg wire:loading.remove wire:target="exportExcel" xmlns="http://www.w3.org/2000/svg" class="size-5 group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <span wire:loading.remove wire:target="exportExcel">Download Report (.xlsx)</span>
            <span wire:loading wire:target="exportExcel">Mempersiapkan...</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100 relative overflow-hidden">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-2">Project Completion</p>
            <h2 class="text-5xl font-black">{{ $stats['completed_projects'] }}<span class="text-xl opacity-50 ml-2">/{{ $stats['total_projects'] }}</span></h2>
            <div class="mt-4 text-blue-200 text-sm font-medium">Total proyek yang berhasil diselesaikan</div>
            <svg class="absolute -right-4 -bottom-4 size-32 opacity-10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87 6.91-1.01L12 2z" />
            </svg>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-center">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-4 text-center">Task Progress Rate</p>
            <div class="relative size-32 mx-auto">
                <svg class="size-full" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-100" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-emerald-500" stroke-width="3" stroke-dasharray="{{ $stats['completion_rate'] }}, 100" stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-black text-gray-800">{{ $stats['completion_rate'] }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-6 text-center">Project Status Distribution</p>
            <div class="space-y-4">
                @foreach(['Selesai' => [$stats['completed_projects'], 'bg-emerald-500'], 'Proses' => [$stats['on_progress_projects'], 'bg-blue-500'], 'Pending' => [$stats['pending_projects'], 'bg-orange-400']] as $label => $val)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full {{ $val[1] }}"></div>
                    <span class="text-xs font-bold text-gray-500 w-16">{{ $label }}</span>
                    <div class="flex-1 bg-gray-50 h-2 rounded-full overflow-hidden">
                        @php $p = $stats['total_projects'] > 0 ? ($val[0] / $stats['total_projects']) * 100 : 0 @endphp
                        <div class="{{ $val[1] }} h-full" style="width: {{ $p }}%"></div>
                    </div>
                    <span class="text-xs font-black text-gray-800">{{ $val[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Tugas Terlambat (Overdue)</h3>
            <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase rounded-lg">Butuh Perhatian Segera</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase">Tugas</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase">Proyek</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Deadline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stats['urgent_tasks'] as $task)
                    <tr class="hover:bg-gray-50/50 transition cursor-default">
                        <td class="px-8 py-5 font-bold text-gray-800 text-sm">{{ $task->title }}</td>
                        <td class="px-8 py-5">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-md">{{ $task->project->title }}</span>
                        </td>
                        <td class="px-8 py-5 text-right text-red-500 font-bold text-sm">
                            {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-10 text-center text-gray-400 italic text-sm">Hebat! Tidak ada tugas yang terlambat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>