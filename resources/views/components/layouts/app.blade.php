<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
<div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        showNotice(event) {
            // Livewire 3 mengirim data dalam event.detail
            // Kita coba ambil message dari event.detail.message atau event.detail[0].message
            const data = event.detail;
            this.message = data.message || (data[0] ? data[0].message : 'Operasi Berhasil');
            this.type = data.type || (data[0] ? data[0].type : 'success');
            
            this.show = true;
            setTimeout(() => { this.show = false }, 3000);
        }
    }" 
    @notify.window="showNotice($event)"
    class="fixed bottom-5 right-5 z-[100] space-y-2">
    
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         :class="{
            'bg-emerald-600': type === 'success',
            'bg-red-600': type === 'error',
            'bg-blue-600': type === 'info'
         }"
         class="flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl text-white font-bold min-w-[250px]">
        
        <template x-if="type === 'success'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </template>
        
        <template x-if="type === 'error'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </template>

        <span x-text="message" class="text-sm tracking-wide text-white"></span>
    </div>
</div>
</x-layouts.app.sidebar>