<?php

use function Livewire\Volt\{state, layout, title, with};
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');
title('Activity Feed');

with(function () {
    return [
        'activities' => Activity::where('user_id', Auth::id())
            ->latest()
            ->paginate(15)
    ];
});

?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-800">Activity Feed</h1>
        <p class="text-gray-500 text-sm">Riwayat aktivitas Anda dalam mengelola proyek.</p>
    </div>

    <div class="space-y-6">
        @forelse($activities as $activity)
            <div class="relative flex gap-4">
                @if(!$loop->last)
                    <span class="absolute left-5 top-10 h-full w-0.5 bg-gray-100"></span>
                @endif

                <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full 
                    {{ $activity->action === 'completed' ? 'bg-emerald-50 text-emerald-600' : '' }}
                    {{ $activity->action === 'created' ? 'bg-blue-50 text-blue-600' : '' }}
                    {{ $activity->action === 'archived' ? 'bg-amber-50 text-amber-600' : '' }}">
                    
                    @if($activity->action === 'completed')
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($activity->action === 'archived')
                         <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    @else
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    @endif
                </div>

                <div class="flex flex-col pb-8">
                    <p class="text-sm font-medium text-gray-600">
                        {{ $activity->description }} <span class="font-bold text-gray-900">"{{ $activity->subject_title }}"</span>
                    </p>
                    <time class="text-xs text-gray-400 mt-1 italic">
                        {{ $activity->created_at->diffForHumans() }}
                    </time>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100">
                <p class="text-gray-400 italic">Belum ada aktivitas tercatat.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </div>
</div>