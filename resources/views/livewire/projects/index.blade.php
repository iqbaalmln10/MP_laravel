<?php

use function Livewire\Volt\{on, with};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

with(fn() => [
    'projects' => Project::where('user_id', Auth::id())->latest()->get()
]);

on(['project-updated' => function () {
    // Memaksa index mengambil data terbaru dari database
    $this->dispatch('$refresh');
}]);

$delete = function ($id) {
    Project::where('id', $id)->where('user_id', Auth::id())->delete();
    $this->dispatch('project-updated');
};

$toggleStatus = function ($id, $newStatus) {
    Project::where('id', $id)->where('user_id', Auth::id())->update(['status' => $newStatus]);
    $this->dispatch('project-updated');
};
?>

<div class="space-y-4">
    @forelse($projects as $project)
    <div class="flex items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-blue-200 transition group">
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
                class="text-xs border-gray-200 rounded-lg py-1">
                <option value="pending" @selected($project->status == 'pending')>Pending</option>
                <option value="on_progress" @selected($project->status == 'on_progress')>On Progress</option>
                <option value="completed" @selected($project->status == 'completed')>Selesai</option>
            </select>

            <button type="button"
                @click="$dispatch('trigger-edit', { id: {{ $project->id }} })"
                class="p-2 text-gray-400 hover:text-blue-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            <button type="button" wire:click="delete({{ $project->id }})" wire:confirm="Hapus proyek?" class="p-2 text-gray-400 hover:text-red-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>

            <button type="button"
                @click="$dispatch('trigger-detail', { id: {{ $project->id }} })"
                class="p-2 text-gray-400 hover:text-emerald-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </button>
        </div>
    </div>
    @empty
    <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
        <p class="text-gray-400 italic text-sm">Belum ada proyek aktif.</p>
    </div>
    @endforelse
</div>