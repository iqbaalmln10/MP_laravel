<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h4 class="text-sm font-medium text-gray-500">Total Proyek</h4>
                <p class="text-3xl font-bold mt-2">10</p>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h4 class="text-sm font-medium text-gray-500">Status Pending</h4>
                <p class="text-3xl font-bold mt-2 text-yellow-500">5</p>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 text-green-500">
                <h4 class="text-sm font-medium text-gray-500">Selesai</h4>
                <p class="text-3xl font-bold mt-2">5</p>
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-semibold mb-4 text-neutral-800 dark:text-neutral-200">Daftar Proyek Saya</h2>
            
            <livewire:projects.index />
        </div>
    </div>
</x-layouts.app>