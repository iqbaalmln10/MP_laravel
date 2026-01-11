<?php

use function Livewire\Volt\{layout, title, state, with};
use App\Models\Note;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');
title('Notes');

state([
    'title' => '',
    'content' => '',
    'project_id' => null,
    'search' => '',
    'isCreating' => false
]);

with(fn () => [
    'notes' => Note::where('user_id', Auth::id())
        ->where('title', 'like', '%' . $this->search . '%')
        ->latest()
        ->get(),
    'projects' => Project::where('user_id', Auth::id())->get()
]);

$saveNote = function () {
    $this->validate([
        'title' => 'required|min:3',
        'content' => 'required'
    ]);

    $note = Note::create([
        'user_id' => Auth::id(),
        'project_id' => $this->project_id,
        'title' => $this->title,
        'content' => $this->content,
    ]);

    // AMBIL NAMA PROYEK (Jika ada)
    $projectTitle = $note->project ? $note->project->title : 'Umum';

    // TAMBAHKAN LOG UNTUK MEMBUAT NOTE
    Activity::log(
        "Membuat catatan baru: {$note->title}", 
        $projectTitle, 
        'note', 
        'created'
    );

    $this->reset(['title', 'content', 'project_id', 'isCreating']);
    $this->dispatch('notify', message: 'Catatan disimpan!', type: 'success');
};

$deleteNote = function ($id) {
    $note = Note::where('id', $id)->where('user_id', Auth::id())->first();
    
    if ($note) {
        $projectTitle = $note->project ? $note->project->title : 'Umum';

        // TAMBAHKAN LOG SEBELUM DIHAPUS
        Activity::log(
            "Menghapus catatan: {$note->title}", 
            $projectTitle, 
            'note', 
            'deleted'
        );

        $note->delete();
        $this->dispatch('notify', message: 'Catatan dihapus!', type: 'error');
    }
};

?>

<div class="p-8 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Notes</h1>
            <p class="text-gray-500 font-medium">Simpan ide, referensi, atau coretan cepat Anda.</p>
        </div>
        <button wire:click="$set('isCreating', {{ !$isCreating }})" 
            class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
            {{ $isCreating ? 'Batal' : '+ Catatan Baru' }}
        </button>
    </div>

    {{-- Form Input Catatan --}}
    @if($isCreating)
    <div class="mb-10 bg-white p-6 rounded-[2rem] border-2 border-indigo-50 shadow-sm animate-in fade-in zoom-in duration-300">
        <div class="space-y-4">
            <input wire:model="title" type="text" placeholder="Judul Catatan..." 
                class="w-full border-none focus:ring-0 text-xl font-bold placeholder:text-gray-300">
            
            <textarea wire:model="content" placeholder="Tulis sesuatu..." rows="4"
                class="w-full border-none focus:ring-0 text-gray-600 placeholder:text-gray-300 resize-none"></textarea>
            
            <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                <select wire:model="project_id" class="text-xs font-bold border-none bg-gray-50 rounded-xl focus:ring-0 text-gray-500">
                    <option value="">Umum (Tanpa Proyek)</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
                <button wire:click="saveNote" class="bg-gray-900 text-white px-6 py-2 rounded-xl text-xs font-black">
                    Simpan Catatan
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Search Bar --}}
    <div class="mb-8">
        <input wire:model.live="search" type="text" placeholder="Cari catatan..." 
            class="w-full max-w-md bg-gray-100 border-none rounded-2xl px-6 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
    </div>

    {{-- Grid Catatan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($notes as $note)
            <div class="group bg-white border border-gray-100 p-6 rounded-[2rem] hover:shadow-xl transition-all relative">
                <div class="flex justify-between items-start mb-4">
                    @if($note->project)
                        <span class="text-[9px] font-black uppercase px-2 py-1 bg-indigo-50 text-indigo-500 rounded-lg">
                            {{ $note->project->title }}
                        </span>
                    @else
                        <span class="text-[9px] font-black uppercase px-2 py-1 bg-gray-100 text-gray-400 rounded-lg">Umum</span>
                    @endif
                    <button wire:click="deleteNote({{ $note->id }})" class="opacity-0 group-hover:opacity-100 text-red-300 hover:text-red-500 transition-all">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
                <h3 class="font-black text-gray-900 mb-2">{{ $note->title }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-4">{{ $note->content }}</p>
                <p class="mt-4 text-[10px] text-gray-300 font-bold uppercase">{{ $note->created_at->format('d M Y') }}</p>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
                <p class="text-gray-400 font-medium italic">Belum ada catatan. Mulai tulis ide Anda!</p>
            </div>
        @endforelse
    </div>
</div>