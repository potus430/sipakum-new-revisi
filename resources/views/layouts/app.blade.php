<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}

        <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3"></div>
    </flux:main>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (event) => {
                    const container = document.getElementById('toast-container');
                    const toast = document.createElement('div');

                    // Logika Warna Berdasarkan Variant
                    const isError = event.variant === 'error';
                    const bgColor = isError ? 'bg-red-600' : 'bg-primary-600'; // Hijau untuk sukses
                    const icon = isError ?
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

                    toast.className =
                        `${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0`;

                    toast.innerHTML = `
                    <div class="bg-white/20 p-1 rounded-full">${icon}</div>
                    <div>
                        <div class="font-bold text-sm">${event.heading || 'Notifikasi'}</div>
                        <div class="text-xs opacity-90">${event.message}</div>
                    </div>
                `;

                    container.appendChild(toast);

                    // Animasi Masuk
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                    }, 100);

                    // Hapus Otomatis setelah 4 detik
                    setTimeout(() => {
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, 4000);
                });
            });
        </script>
    @endpush
    </flux:main>
</x-layouts::app.sidebar>
