<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Unit Kerja') }}
        </h2>
    </x-slot>

    @include('components.alert')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-sm">
                <div class="p-4 text-primary-70">
                    <livewire:work-unit-table />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>