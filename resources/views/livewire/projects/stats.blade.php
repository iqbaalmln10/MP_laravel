<?php
use function Livewire\Volt\{state, on};
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

state([
    'total' => fn() => Project::where('user_id', Auth::id())->count(),
    'pending' => fn() => Project::where('user_id', Auth::id())->where('status', 'pending')->count(),
    'completed' => fn() => Project::where('user_id', Auth::id())->where('status', 'completed')->count(),
]);

on(['project-updated' => function () {
    $this->total = Project::where('user_id', Auth::id())->count();
    $this->pending = Project::where('user_id', Auth::id())->where('status', 'pending')->count();
    $this->completed = Project::where('user_id', Auth::id())->where('status', 'completed')->count();
}]);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Total Proyek</p>
        <p class="text-4xl font-bold mt-2 text-neutral-900">{{ $total }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Status Pending</p>
        <p class="text-4xl font-bold mt-2 text-orange-500">{{ $pending }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Selesai</p>
        <p class="text-4xl font-bold mt-2 text-emerald-500">{{ $completed }}</p>
    </div>
</div>