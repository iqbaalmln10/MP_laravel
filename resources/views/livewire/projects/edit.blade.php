<?php

use function Livewire\Volt\{state, on};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

// Tambahkan 'status' ke dalam state agar bisa dibaca oleh dropdown
state([
    'projectId' => '', 
    'title' => '', 
    'description' => '', 
    'status' => ''
]);

/**
 * Fungsi pembantu untuk mengambil data terbaru dari database
 */
$refreshData = function ($id) {
    $project = Project::where('id', $id)
        ->where('user_id', Auth::id())
        ->first();

    if ($project) {
        $this->projectId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description;
        $this->status = $project->status; // Sinkronkan status database ke state
    }
};

/**
 * Listener saat tombol edit diklik
 */
on(['edit-project' => function () {
    $arguments = func_get_args();
    $data = $arguments[0] ?? null;
    $id = is_array($data) ? ($data['id'] ?? null) : $data;

    if ($id) {
        $this->refreshData($id);
    }
}]);

/**
 * KUNCI: Listener agar dropdown sinkron otomatis saat Task Manager mengubah status
 */
on(['project-updated' => function () {
    if ($this->projectId) {
        // Ambil ulang data status saja tanpa mengganggu input title/description yang sedang diketik
        $currentStatus = Project::where('id', $this->projectId)->value('status');
        $this->status = $currentStatus;
    }
}]);

$update = function () {
    $this->validate([
        'title' => 'required|min:3|max:255',
        'description' => 'nullable',
        'status' => 'required' // Pastikan status tervalidasi
    ]);

    $project = Project::where('id', $this->projectId)
        ->where('user_id', Auth::id())
        ->first();

    if ($project) {
        $project->update([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
        ]);
        $this->dispatch('notify', [
            'message' => 'Proyek berhasil diperbarui.',
            'type' => 'success'
        ]);
        $this->dispatch('project-updated');
        $this->dispatch('close-modal');
    }
};
?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">
            Edit Proyek
        </h2>

        <button
            type="button"
            @click="$dispatch('close-modal')"
            class="text-2xl font-bold text-gray-400 hover:text-gray-600">
            &times;
        </button>
    </div>

    <form wire:submit.prevent="update" class="space-y-5">
        <div>
            <label class="block mb-1 text-sm font-semibold text-gray-700">
                Nama Proyek
            </label>

            <input
                type="text"
                wire:model.defer="title"
                class="w-full px-4 py-2 transition border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500">

            @error('title')
            <span class="mt-1 text-xs text-red-500">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-semibold text-gray-700">
                Deskripsi
            </label>

            <textarea
                wire:model.defer="description"
                rows="4"
                class="w-full px-4 py-2 transition border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>

            @error('description')
            <span class="mt-1 text-xs text-red-500">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button
                type="button"
                @click="$dispatch('close-modal')"
                class="px-5 py-2 text-sm font-medium text-gray-500 transition hover:text-gray-800">
                Batal
            </button>

            <button
                type="submit"
                class="px-6 py-2 text-sm font-bold text-white transition bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700 shadow-blue-500/20">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>