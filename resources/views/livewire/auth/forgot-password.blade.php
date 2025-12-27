<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.forgot.title')"
            :description="__('auth.forgot.description')"
        />

        {{-- Status da sessão --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.email') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            <flux:input
                name="email"
                type="email"
                required
                autofocus
                :label="__('auth.fields.email')"
                :placeholder="__('auth.placeholders.email')"
            />

            <flux:button
                variant="primary"
                type="submit"
                class="w-full"
                data-test="email-password-reset-link-button"
            >
                {{ __('auth.forgot.button') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('auth.forgot.back_prefix') }}</span>
            <flux:link :href="route('login')" wire:navigate>
                {{ __('auth.login.link') }}
            </flux:link>
        </div>
    </div>
</x-layouts.auth>
