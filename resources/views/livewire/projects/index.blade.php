<?php

use function Laravel\Prompts\confirm;
use function Livewire\Volt\{state, on, with, mount};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

// 1. Tambahkan state untuk filter
state([
    'filterStatus' => 'all',
    'confirmingDeletion' => false, // Status modal
    'projectToDelete' => null,     // ID proyek yang dipilih
    'confirmingArchive' => false,
    'projectToArchive' => null,
]);

mount(function () {
    $projectId = request()->query('open_project');

    if ($projectId) {
        // Kita kirim dispatch melalui Livewire, 
        // secara otomatis akan ditangkap oleh Alpine.js yang mendengarkan 'trigger-detail'
        $this->dispatch('trigger-detail', id: (int)$projectId);
    }
});

with(function () {
    $query = Project::where('user_id', Auth::id())->latest();

    // Logika Filter
    if ($this->filterStatus !== 'all') {
        $query->where('status', $this->filterStatus);
    }

    return [
        'projects' => $query->get()
    ];
});

on(['project-updated' => function () {
    // Memaksa index mengambil data terbaru dari database
    $this->dispatch('$refresh');
}]);

$confirmDelete = function ($id) {
    $this->projectToDelete = $id;
    $this->confirmingDeletion = true;
};

$archive = function () {
    if ($this->projectToArchive) {
        // Cari project-nya dulu sebagai instance Model agar SoftDeletes aktif
        $project = Project::where('id', $this->projectToArchive)
            ->where('user_id', Auth::id())
            ->first();

        if ($project) {
            $project->delete(); // Ini akan otomatis mengisi 'deleted_at'
        }

        $this->confirmingArchive = false;
        $this->projectToArchive = null;

        $this->dispatch('notify', message: 'Proyek berhasil diarsipkan!', type: 'success');
    }
};

$confirmArchive = function ($id) {
    $this->projectToArchive = $id;
    $this->confirmingArchive = true;
};

$archive = function () {
    if ($this->projectToArchive) {
        // Gunakan soft delete alih-alih update kolom 'archived'
        Project::where('id', $this->projectToArchive)
            ->where('user_id', Auth::id())
            ->delete(); // ini akan mengisi deleted_at tanpa menghapus permanen

        $this->confirmingArchive = false;
        $this->projectToArchive = null;

        $this->dispatch('project-updated');
        $this->dispatch('notify', message: 'Proyek berhasil dipindahkan ke Archives!', type: 'success');
    }
};


// Sesuaikan nama fungsi dengan yang dipanggil di HTML (updateStatus)
$updateStatus = function ($id, $newStatus) {
    Project::where('id', $id)->where('user_id', Auth::id())->update(['status' => $newStatus]);
    $this->dispatch('project-updated');
    $this->dispatch('notify', [
        'message' => 'Status proyek berhasil diperbarui.',
        'type' => 'success'
    ]);
};

?>

<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
            @foreach([
            'all' => 'Semua',
            'pending' => 'Pending',
            'on_progress' => 'Proses',
            'completed' => 'Selesai'
            ] as $key => $label)
            <button
                wire:click="$set('filterStatus', '{{ $key }}')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filterStatus === $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <span class="text-xs text-gray-400 font-medium">
            Total: {{ $projects->count() }} Proyek
        </span>
    </div>

    <div class="space-y-4">
        @forelse($projects as $project)
        <div wire:key="project-{{ $project->id }}" class="flex items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-blue-200 transition group">
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-gray-800">{{ $project->title }}</h3>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase 
                            {{ $project->status == 'completed' ? 'bg-green-100 text-green-600' : ($project->status == 'on_progress' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600') }}">
                        {{ str_replace('_', ' ', $project->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $project->description }}</p>
            </div>

            <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition">
                <select
                    wire:change="updateStatus({{ $project->id }}, $event.target.value)"
                    class="text-xs border-gray-200 rounded-lg py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending" @selected($project->status == 'pending')>Pending</option>
                    <option value="on_progress" @selected($project->status == 'on_progress')>On Progress</option>
                    <option value="completed" @selected($project->status == 'completed')>Selesai</option>
                </select>

                <div class="flex items-center border-l border-gray-100 ml-2 pl-2">
                    <button type="button"
                        @click="$dispatch('trigger-edit', { id: {{ $project->id }} })"
                        class="p-2 text-gray-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>

                    <button type="button"
                        @click="$dispatch('trigger-detail', { id: {{ $project->id }} })"
                        class="p-2 text-gray-400 hover:text-emerald-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        wire:click="confirmArchive({{ $project->id }})"
                        class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-amber-600 transition-colors cursor-pointer p-2 hover:bg-amber-50 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        Archive
                    </button>
                    <button
                        type="button"
                        wire:click="confirmDelete({{ $project->id }})"
                        class="p-2 text-gray-400 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                    a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                    m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-100">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-gray-400 italic text-sm">Tidak ada proyek ditemukan untuk kategori ini.</p>
        </div>
        @endforelse
    </div>
    <div x-data="{ open: @entangle('confirmingDeletion') }">
        <template x-teleport="body">
            <div x-show="open"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">

                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden"
                    @click.outside="open = false">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Proyek?</h3>
                        <p class="text-gray-500 text-sm">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <div class="flex border-t">
                        <button wire:click="$set('confirmingDeletion', false)"
                            class="flex-1 py-4 font-bold text-gray-500 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="flex-1 py-4 font-bold text-red-600 hover:bg-red-50 transition border-l">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <!-- Pop-up Archive -->
    <div x-data="{ open: @entangle('confirmingArchive') }">
        <template x-teleport="body">
            <div x-show="open"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">

                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden"
                    @click.outside="open = false">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Arsipkan Proyek?</h3>
                        <p class="text-gray-500 text-sm">Proyek akan dipindahkan ke menu Archives.</p>
                    </div>

                    <div class="flex border-t">
                        <button wire:click="$set('confirmingArchive', false)"
                            class="flex-1 py-4 font-bold text-gray-500 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button wire:click="archive"
                            class="flex-1 py-4 font-bold text-amber-600 hover:bg-amber-50 transition border-l">
                            Ya, Arsipkan
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>