<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.reset.title')"
            :description="__('auth.reset.description')"
        />

        {{-- Status da sessão --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.update') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            {{-- Token --}}
            <input
                type="hidden"
                name="token"
                value="{{ request()->route('token') }}"
            >

            {{-- E-mail --}}
            <flux:input
                name="email"
                type="email"
                required
                autocomplete="email"
                :value="request('email')"
                :label="__('auth.fields.email')"
            />

            {{-- Nova senha --}}
            <flux:input
                name="password"
                type="password"
                required
                autocomplete="new-password"
                viewable
                :label="__('auth.fields.password')"
                :placeholder="__('auth.fields.password')"
            />

            {{-- Confirmar senha --}}
            <flux:input
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                viewable
                :label="__('auth.fields.password_confirmation')"
                :placeholder="__('auth.fields.password_confirmation')"
            />

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                data-test="reset-password-button"
            >
                {{ __('auth.reset.button') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth>
