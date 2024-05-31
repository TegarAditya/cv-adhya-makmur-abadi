<x-filament-panels::page.simple>
    <x-slot name="heading">
        <div>Formulir Pemesanan</div>
    </x-slot>
    <x-slot name="subheading">
        Mohon isi formulir berikut untuk melakukan pemesanan.
    </x-slot>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>
</x-filament-panels::page.simple>
