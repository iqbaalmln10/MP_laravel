<?php

use function Livewire\Volt\{state, rules, on};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
state([
    'projectId' => null,
    'title' => '',
    'description' => '',
]);

/*
|--------------------------------------------------------------------------
| Validation Rules
|--------------------------------------------------------------------------
*/
rules([
    'title' => 'required|min:3|max:255',
    'description' => 'nullable',
]);

/*
|--------------------------------------------------------------------------
| Listener Edit Project
|--------------------------------------------------------------------------
*/
on([
    'edit-project' => function ($payload) {
        $id = is_array($payload) ? ($payload['id'] ?? null) : $payload;

        if (! $id) {
            return;
        }

        $project = Project::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $project) {
            return;
        }

        $this->projectId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description;
    },

    // Cleanup saat modal ditutup
    'close-modal' => function () {
        $this->reset(['projectId', 'title', 'description']);
    },
]);

/*
|--------------------------------------------------------------------------
| Update Project
|--------------------------------------------------------------------------
*/
$update = function () {
    $this->validate();

    $project = Project::where('id', $this->projectId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // Cegah submit jika tidak ada perubahan
    if (
        $project->title === $this->title &&
        $project->description === $this->description
    ) {
        $this->dispatch('close-modal');
        return;
    }

    $project->update([
        'title' => $this->title,
        'description' => $this->description,
    ]);

    // Refresh dashboard
    $this->dispatch('project-updated')->to('projects.index');

    // Tutup modal
    $this->dispatch('close-modal');
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
            class="text-2xl font-bold text-gray-400 hover:text-gray-600"
        >
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
                class="w-full px-4 py-2 transition border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >

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
                class="w-full px-4 py-2 transition border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>

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
                class="px-5 py-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
            >
                Batal
            </button>

            <button
                type="submit"
                class="px-6 py-2 text-sm font-bold text-white transition bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700 shadow-blue-500/20"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
