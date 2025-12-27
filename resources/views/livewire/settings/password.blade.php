<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('settings.password.title')"
        :subheading="__('settings.password.subtitle')"
    >
        <form
            method="POST"
            wire:submit="updatePassword"
            class="mt-6 space-y-6"
        >
            <flux:input
                wire:model="current_password"
                type="password"
                required
                autocomplete="current-password"
                :label="__('settings.password.current')"
            />

            <flux:input
                wire:model="password"
                type="password"
                required
                autocomplete="new-password"
                :label="__('settings.password.new')"
            />

            <flux:input
                wire:model="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                :label="__('auth.fields.password_confirmation')"
            />

            <div class="flex items-center gap-4">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full"
                >
                    {{ __('common.save') }}
                </flux:button>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('common.saved') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
