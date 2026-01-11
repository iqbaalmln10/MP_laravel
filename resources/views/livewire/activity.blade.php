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
            <div class="relative flex gap-6 pb-8 group">
                @if(!$loop->last)
                    <span class="absolute left-[1.2rem] top-10 h-full w-[2px] bg-gray-100"></span>
                @endif

                <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl shadow-sm border border-gray-100 bg-white transition-transform group-hover:scale-110
                    {{ $activity->action === 'completed' ? 'ring-2 ring-emerald-500/20' : '' }}
                    {{ $activity->action === 'created' ? 'ring-2 ring-blue-500/20' : '' }}
                    {{ $activity->action === 'archived' ? 'ring-2 ring-amber-500/20' : '' }}
                    {{ $activity->action === 'deleted' ? 'ring-2 ring-red-500/20' : '' }}
                    {{ $activity->action === 'updated' ? 'ring-2 ring-indigo-500/20' : '' }}">
                    
                    @if($activity->action === 'completed')
                        <svg class="size-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($activity->action === 'updated')
                        <svg class="size-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    @elseif($activity->action === 'created')
                        <svg class="size-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    @elseif($activity->action === 'archived')
                        <svg class="size-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9-4v4m4-4v4" />
                        </svg>
                    @elseif($activity->action === 'deleted')
                        <svg class="size-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    @else
                        <svg class="size-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                <div class="flex flex-col flex-1 pt-1 pb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 leading-relaxed font-medium">
                                <span class="text-gray-900 font-black">{{ $activity->description }}</span>
                                di proyek 
                                <span class="bg-gray-100 px-2 py-0.5 rounded-lg text-gray-700 font-bold border border-gray-200">
                                    {{ $activity->subject_title }}
                                </span>
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider italic">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                                <span class="text-gray-200">•</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                    {{ $activity->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
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