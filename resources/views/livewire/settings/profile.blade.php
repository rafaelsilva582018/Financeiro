<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('settings.profile.title')"
        :subheading="__('settings.profile.subtitle')"
    >
        <form
            wire:submit="updateProfileInformation"
            class="my-6 w-full space-y-6"
        >
            <flux:input
                wire:model="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                :label="__('auth.fields.name')"
            />

            <div>
                <flux:input
                    wire:model="email"
                    type="email"
                    required
                    autocomplete="email"
                    :label="__('auth.fields.email')"
                />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                    && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('settings.profile.email_unverified') }}

                            <flux:link
                                class="cursor-pointer text-sm"
                                wire:click.prevent="resendVerificationNotification"
                            >
                                {{ __('settings.profile.resend_verification') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !text-green-600 !dark:text-green-400">
                                {{ __('settings.profile.verification_sent') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full"
                >
                    {{ __('common.save') }}
                </flux:button>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('common.saved') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
