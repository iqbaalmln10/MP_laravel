<?php
use function Livewire\Volt\{state, with, on};
use App\Models\{Project, Task};

// Tambahkan state filter
state(['projectId' => null, 'newTaskTitle' => '', 'filter' => 'all']);

$updateProjectStatus = function ($project) {
    $total = $project->tasks()->count();
    $completed = $project->tasks()->where('is_completed', true)->count();

    if ($total === 0) {
        $newStatus = 'pending';
    } elseif ($completed === $total) {
        $newStatus = 'completed';
    } else {
        // UBAH KE UNDERSCORE: 'on_progress'
        $newStatus = 'on_progress'; 
    }

    $project->update(['status' => $newStatus]);
};

$addTask = function () {
    if (!$this->projectId || empty(trim($this->newTaskTitle))) return;

    // 1. Buat Task baru
    Task::create([
        'project_id' => $this->projectId,
        'title' => $this->newTaskTitle,
        'is_completed' => false
    ]);

    // 2. Ambil model project dan jalankan ulang logika status
    $project = Project::find($this->projectId);
    $this->updateProjectStatus($project); 

    $this->newTaskTitle = '';
    $this->dispatch('project-updated'); 
};

$toggleTask = function ($id) {
    $task = Task::find($id);
    if ($task) {
        $task->update(['is_completed' => !$task->is_completed]);
        
        $project = Project::find($this->projectId);
        $this->updateProjectStatus($project); // Jalankan update status
        
        $this->dispatch('project-updated');
    }
};

$deleteTask = function ($id) {
    $task = Task::find($id);
    if ($task) {
        $task->delete();
        $project = Project::find($this->projectId);
        $this->updateProjectStatus($project); // Jalankan update status
        $this->dispatch('project-updated');
    }
};

with(function () {
    $project = $this->projectId ? Project::with('tasks')->find($this->projectId) : null;
    
    // Logika Filter
    $tasksQuery = $project ? $project->tasks() : null;
    if ($tasksQuery) {
        if ($this->filter === 'active') $tasksQuery->where('is_completed', false);
        if ($this->filter === 'completed') $tasksQuery->where('is_completed', true);
    }
    $filteredTasks = $tasksQuery ? $tasksQuery->get() : [];

    // Statistik tetap dari semua task (bukan yang difilter)
    $total = $project?->tasks()->count() ?? 0;
    $completedCount = $project?->tasks()->where('is_completed', true)->count() ?? 0;
    $percent = $total > 0 ? round(($completedCount / $total) * 100) : 0;

    return [
        'project' => $project,
        'tasks' => $filteredTasks,
        'percent' => $percent,
        'total' => $total,
        'completed' => $completedCount
    ];
});
?>

<div>
    <div x-init="$wire.projectId = selectedId; $wire.$refresh()">

        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 tracking-tight">{{ $project?->title ?? 'Memuat...' }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">{{ $completed }}/{{ $total }} Tugas Selesai</p>
                    </div>
                </div>
            </div>
            <button @click="openDetail = false" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar" style="max-height: 70vh;">
            <div class="mb-8 bg-blue-50/50 p-4 rounded-2xl border border-blue-100/50">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-bold text-blue-700">Progress Proyek</span>
                    <span class="text-2xl font-black text-blue-700">{{ $percent }}<span class="text-sm ml-0.5">%</span></span>
                </div>
                <div class="w-full bg-blue-100 rounded-full h-3 overflow-hidden shadow-inner">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(37,99,235,0.5)]"
                        style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <div class="relative group mb-8">
                <input type="text"
                    wire:model="newTaskTitle"
                    wire:keydown.enter="addTask"
                    placeholder="Apa yang perlu dikerjakan selanjutnya?"
                    class="w-full pl-5 pr-14 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-0 transition-all outline-none text-sm shadow-sm font-medium italic group-hover:bg-gray-100/50 focus:group-hover:bg-white">
                <button wire:click="addTask"
                    class="absolute right-2 top-2 bottom-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-all active:scale-90 flex items-center justify-center group">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                @forelse($project?->tasks ?? [] as $task)
                <div wire:key="task-{{ $task->id }}"
                    class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:border-blue-200 hover:shadow-md hover:shadow-blue-500/5 transition-all group">
                    <div class="flex items-center gap-4 flex-1">
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox"
                                wire:click="toggleTask({{ $task->id }})"
                                @checked($task->is_completed)
                            class="peer sr-only">
                            <div class="w-6 h-6 border-2 border-gray-200 rounded-lg peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </label>
                        <span class="text-sm font-semibold {{ $task->is_completed ? 'line-through text-gray-400' : 'text-gray-700' }} transition-all">
                            {{ $task->title }}
                        </span>
                    </div>

                    <button wire:click="deleteTask({{ $task->id }})"
                        class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-400 italic">Belum ada tugas yang ditambahkan.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="p-6 border-t border-gray-50 flex justify-end bg-gray-50/30">
            <button @click="openDetail = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                Selesai
            </button>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
    </style>
</div>