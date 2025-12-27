<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.login.title')"
            :description="__('auth.login.description')"
        />

        {{-- Status da sessão --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('login.store') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            {{-- E-mail --}}
            <flux:input
                name="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                :value="old('email')"
                :label="__('auth.fields.email')"
                :placeholder="__('auth.placeholders.email')"
            />

            {{-- Senha --}}
            <div class="relative">
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    viewable
                    :label="__('auth.fields.password')"
                    :placeholder="__('auth.fields.password')"
                />


                @if (Route::has('password.request'))
                    <flux:link
                        class="absolute top-0 end-0 text-sm"
                        :href="route('password.request')"
                        wire:navigate
                    >
                        {{ __('auth.login.forgot') }}
                    </flux:link>
                @endif
            </div>

            {{-- Lembrar --}}
            <flux:checkbox
                name="remember"
                :checked="old('remember')"
                :label="__('auth.login.remember')"
            />

            <flux:button
                variant="primary"
                type="submit"
                class="w-full"
                data-test="login-button"
            >
                {{ __('auth.login.button') }}
            </flux:button>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center text-zinc-600 dark:text-zinc-400 rtl:space-x-reverse">
                <span>{{ __('auth.login.no_account') }}</span>
                <flux:link :href="route('register')" wire:navigate>
                    {{ __('auth.register.link') }}
                </flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>
