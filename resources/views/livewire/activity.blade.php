<?php

use function Livewire\Volt\{state, layout, title, with};
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

layout('components.layouts.app');
title('Activity Feed');

with(function () {
    return [
        'groupedActivities' => Activity::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('d F Y'))
    ];
});

?>
<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-12">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Activity Feed</h1>
        <p class="text-gray-500 font-medium mt-1">Riwayat produktivitas Anda tercatat secara otomatis.</p>
    </div>

    <div class="relative">
        @forelse($groupedActivities as $date => $activities)
            <div class="sticky top-0 z-10 flex items-center gap-4 bg-white/80 backdrop-blur-md py-4 mb-6">
                <div class="h-px flex-1 bg-gray-100"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 bg-gray-50 px-4 py-1.5 rounded-full border border-gray-100">
                    {{ $date }}
                </span>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>

            <div class="space-y-1 mb-10 pl-4">
                @foreach($activities as $activity)
                    <div class="group relative flex gap-6 p-4 rounded-[2rem] hover:bg-gray-50 transition-all duration-300">
                        
                        <div class="relative flex flex-col items-center">
                            <div class="z-20 flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm group-hover:shadow-md transition-all duration-300
                                {{ $activity->action === 'completed' ? 'text-emerald-500' : '' }}
                                {{ $activity->action === 'created' ? 'text-blue-500' : '' }}
                                {{ $activity->action === 'archived' ? 'text-amber-500' : '' }}
                                {{ $activity->action === 'deleted' ? 'text-red-500' : '' }}
                                {{ $activity->action === 'updated' ? 'text-indigo-500' : '' }}">
                                
                                @if($activity->action === 'completed')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                @elseif($activity->action === 'archived')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                @elseif($activity->action === 'deleted')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                @elseif($activity->action === 'updated')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                @else
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col flex-1 justify-center">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black text-gray-400 tabular-nums">{{ $activity->created_at->format('H:i') }}</span>
                                <h4 class="text-sm font-bold text-gray-800">
                                    {{ $activity->description }} 
                                    <span class="text-gray-400 mx-1">/</span> 
                                    <span class="text-indigo-600">"{{ $activity->subject_title }}"</span>
                                </h4>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 font-medium italic">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
                <div class="p-6 bg-white rounded-full shadow-sm mb-4">
                    <svg class="size-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-gray-400 font-bold italic tracking-wide">Belum ada aktivitas hari ini.</p>
            </div>
        @endforelse
    </div>
</div>