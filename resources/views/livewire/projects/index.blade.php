<?php

use function Livewire\Volt\{state};
use App\Models\Project;

// Logika: Ambil semua proyek milik user yang sedang login
state(['projects' => fn () => Project::where('user_id', auth()->id())->get()]);

?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach($projects as $project)
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <h3 class="font-bold text-xl text-gray-800">{{ $project->title }}</h3>
            <p class="text-gray-600 text-sm mt-2">{{ Str::limit($project->description, 100) }}</p>
            
            <div class="mt-4 flex justify-between items-center">
                <span class="px-2 py-1 text-xs rounded-full 
                    {{ $project->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ strtoupper($project->status) }}
                </span>
                <span class="text-gray-400 text-xs">{{ $project->created_at->format('d M Y') }}</span>
            </div>
        </div>
    @endforeach
</div>