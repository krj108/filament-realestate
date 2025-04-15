<x-filament::page>
    <div class="max-w-3xl mx-auto space-y-8">
        {{-- Profile Information --}}
        <x-filament::card class="!p-6 !rounded-xl">
            <form wire:submit.prevent="saveProfile">
                {{ $this->getForm('form') }} 
                
                <div class="flex justify-end pt-6">
                    <x-filament::button 
                        type="submit"
                        icon="heroicon-o-check"
                        size="sm"
                        color="primary">
                        Save Profile
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>

        {{-- Password Update --}}
        <x-filament::card class="!p-6 !rounded-xl mt-8">
            <form wire:submit.prevent="savePassword">
                {{ $this->getForm('passwordForm') }} <!-- تعديل هنا -->
                
                <div class="flex justify-end pt-6">
                    <x-filament::button 
                        type="submit"
                        icon="heroicon-o-lock-closed"
                        size="sm"
                        color="danger">
                        Update Password
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>
    </div>
</x-filament::page>