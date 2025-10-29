<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::button
        wire:click="saveReading"
        type="submit"
        size="md"
        color="info"
        icon="heroicon-o-check-circle"
        icon-position="before"
    >
        Save Readings
    </x-filament::button>
</x-filament-panels::page>
