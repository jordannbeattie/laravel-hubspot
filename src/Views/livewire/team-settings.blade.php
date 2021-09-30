<div id="hubspot">

    <x-jet-section-border />

    <div class="mt-10 sm:mt-0">
        <x-jet-form-section submit="addTeamMember">
            <x-slot name="title">
                {{ __('Hubspot') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Connect your Hubspot account.') }}
            </x-slot>

            <x-slot name="form">
                <div class="col-span-6">
                    <div class="max-w-xl text-sm text-gray-600">
                        @if( $connected )
                            You are connected to Hubspot
                        @else
                            <x-hubspot::oauth-button />
                        @endif
                    </div>
                </div>
            </x-slot>

            @if( $connected )
                <x-slot name="actions">
                    <x-jet-danger-button wire:click="$toggle('confirmDisconnect')">
                        {{ __('Disconnect') }}
                    </x-jet-danger-button>
                </x-slot>
            @endif
        </x-jet-form-section>
    </div>

    <x-jet-confirmation-modal wire:model="confirmDisconnect">
        <x-slot name="title">
            Disconnect Hubspot
        </x-slot>

        <x-slot name="content">
            Are you sure you want to disconnect your account?
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmDisconnect')" wire:loading.attr="disabled">
                Nevermind
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="disconnect" wire:loading.attr="disabled">
                Disconnect
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

</div>
