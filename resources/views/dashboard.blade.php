<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl"
        x-data="{ 
            openCreate: false, 
            openEdit: false, 
            openDetail: false, 
            selectedId: null 
        }"
        @trigger-edit.window="selectedId = $event.detail.id; openEdit = true; $dispatch('edit-project', { id: $event.detail.id })"

        @trigger-detail.window="
            selectedId = $event.detail.id; 
            openDetail = true;
            /* Tambahkan baris ini untuk memberitahu Livewire agar segera memuat data */
            $dispatch('set-project', { id: $event.detail.id })
        "

        @close-modal.window="openCreate = false; openEdit = false; openDetail = false">

        <livewire:projects.stats />

        <div class="relative min-h-[500px] flex-1 overflow-hidden rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm transition-all">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-800 tracking-tight">Daftar Proyek Saya</h2>
                    <p class="text-sm text-neutral-500">Kelola dan pantau progress pekerjaan Anda di sini.</p>
                </div>

                <button
                    @click="openCreate = true"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-500/25 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Proyek Baru
                </button>
            </div>

            <livewire:projects.index />
        </div>

        <div x-show="openCreate"
            style="display: none;"
            class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="openCreate = false"></div>
            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
                    <livewire:projects.create wire:key="modal-create" />
                </div>
            </div>
        </div>

        <div x-show="openEdit"
            style="display: none;"
            class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="openEdit = false"></div>
            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
                    <livewire:projects.edit wire:key="modal-edit" />
                </div>
            </div>
        </div>

        <div x-show="openDetail" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="openDetail = false"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden">

                <template x-if="openDetail">
                    <livewire:projects.task-manager
                        x-bind:project-id="selectedId"
                        x-bind:key="'tm-' + selectedId" />
                </template>

            </div>
        </div>
    </div>
</x-layouts.app>