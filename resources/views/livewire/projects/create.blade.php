<?php

use function Livewire\Volt\{state, rules, on};
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

state([
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
| Save Project
|--------------------------------------------------------------------------
*/
$save = function () {
    $this->validate();

    // 1. Simpan hasil pembuatan proyek ke dalam variabel $project
    $project = Project::create([
        'title' => $this->title,
        'description' => $this->description,
        'user_id' => Auth::id(),
        'status' => 'pending',
    ]);

    // 2. Sekarang variabel $project sudah ada, jadi log bisa dijalankan
        Activity::log(
        'Membuat proyek baru',
        $project->title,
        'project',
        'created'
    );

    $this->dispatch(
        'notify',
        message: 'Proyek baru berhasil ditambahkan.',
        type: 'success'
    );

    $this->reset(['title', 'description']);
    $this->dispatch('project-updated')->to('projects.index');
    $this->dispatch('close-modal');
};
/*
|--------------------------------------------------------------------------
| Cleanup ketika modal ditutup
|--------------------------------------------------------------------------
*/
on([
    'close-modal' => function () {
        $this->reset(['title', 'description']);
    },
]);

?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">
            Tambah Proyek Baru
        </h2>

        <button
            type="button"
            @click="$dispatch('close-modal')"
            class="text-2xl font-bold text-gray-400 hover:text-gray-600">
            &times;
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
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
                rows="3"
                class="w-full px-4 py-2 transition border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>

            @error('description')
            <span class="mt-1 text-xs text-red-500">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <button
                type="button"
                @click="$dispatch('close-modal')"
                class="px-5 py-2 text-sm font-medium text-gray-500 transition hover:text-gray-800">
                Batal
            </button>

            <button
                type="submit"
                class="px-6 py-2 text-sm font-bold text-white transition bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700 shadow-blue-500/20">
                Simpan Proyek
            </button>
        </div>
    </form>
</div>