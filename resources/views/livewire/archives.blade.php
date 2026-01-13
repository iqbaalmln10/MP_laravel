<?php

use function Livewire\Volt\{state, layout, title, with};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');
title('Archives');

with(function () {
    return [
        // onlyTrashed() mengambil data yang sudah di-soft delete
        'archivedProjects' => Project::where('user_id', Auth::id())
            ->onlyTrashed()
            ->latest('deleted_at')
            ->get()
    ];
});

$restore = function ($id) {
    $project = Project::where('user_id', Auth::id())->onlyTrashed()->findOrFail($id);
    $project->restore(); // Mengembalikan ke Dashboard utama
    
    $this->dispatch('notify', message: 'Proyek berhasil dikembalikan!', type: 'success');
};

$forceDelete = function ($id) {
    $project = Project::where('user_id', Auth::id())->onlyTrashed()->findOrFail($id);
    $project->forceDelete(); // Hapus permanen dari database
    
    $this->dispatch('notify', message: 'Proyek dihapus permanen!', type: 'info');
};

?>

<div class="p-6 space-y-6">
    <div class="flex flex-col">
        <h1 class="text-2xl font-black text-gray-800">Archives</h1>
        <p class="text-gray-500 text-sm font-medium">Proyek yang telah Anda selesaikan atau simpan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($archivedProjects as $project)
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm group">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold text-gray-800">{{ $project->title }}</h3>
                    <div class="flex gap-2">
                        <button wire:click="restore({{ $project->id }})" class="p-2 hover:bg-emerald-50 text-emerald-600 rounded-xl transition cursor-pointer" title="Restore">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        </button>
                        <button wire:confirm="Hapus permanen proyek ini?" wire:click="forceDelete({{ $project->id }})" class="p-2 hover:bg-red-50 text-red-600 rounded-xl transition cursor-pointer" title="Hapus Permanen">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-gray-400 text-xs mb-4 line-clamp-2 italic">Diarsipkan pada {{ $project->deleted_at->format('d M Y') }}</p>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                <p class="text-gray-400 font-medium italic text-sm">Belum ada proyek di dalam arsip.</p>
            </div>
        @endforelse
    </div>
</div>