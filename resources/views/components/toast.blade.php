@php
    $isSuccess = session()->has('success');
    $isError = session()->has('error');
    $message = session('success') ?? session('error');
@endphp

@if ($isSuccess || $isError)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        x-transition:enter="transition ease-out duration-300 transform translate-y-2 opacity-0"
        x-transition:leave="transition ease-in duration-200 transform opacity-100" x-transition:leave-end="opacity-0"
        class="fixed top-5 right-5 z-[9999] w-full max-w-sm p-4 rounded-xl shadow-2xl flex items-center gap-3 border
        {{ $isSuccess ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex-shrink-0 {{ $isSuccess ? 'text-emerald-600' : 'text-red-600' }}">
            <flux:icon name="{{ $isSuccess ? 'check-circle' : 'exclamation-triangle' }}" variant="solid" class="size-6" />
        </div>

        <div class="flex-1">
            <p class="text-sm font-bold {{ $isSuccess ? 'text-emerald-900' : 'text-red-900' }}">
                {{ $isSuccess ? 'Berhasil' : 'Terjadi Kesalahan' }}
            </p>
            <p class="text-sm {{ $isSuccess ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $message }}
            </p>
        </div>

        <button @click="show = false" class="opacity-50 hover:opacity-100 transition-opacity">
            <flux:icon name="x-mark" class="size-4 {{ $isSuccess ? 'text-emerald-900' : 'text-red-900' }}" />
        </button>
    </div>
@endif
