<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.register.title')"
            :description="__('auth.register.description')"
        />

        {{-- Status da sessão --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('register.store') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            {{-- Nome --}}
            <flux:input
                name="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                :value="old('name')"
                :label="__('auth.fields.name')"
                :placeholder="__('auth.placeholders.name')"
            />

            {{-- E-mail --}}
            <flux:input
                name="email"
                type="email"
                required
                autocomplete="email"
                :value="old('email')"
                :label="__('auth.fields.email')"
                :placeholder="__('auth.placeholders.email')"
            />

            {{-- Senha --}}
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
            >
                {{ __('auth.register.button') }}
            </flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400 rtl:space-x-reverse">
            <span>{{ __('auth.register.have_account') }}</span>
            <flux:link :href="route('login')" wire:navigate>
                {{ __('auth.login.link') }}
            </flux:link>
        </div>
    </div>
</x-layouts.auth>
