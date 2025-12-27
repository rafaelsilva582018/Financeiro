<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.confirm.title')"
            :description="__('auth.confirm.description')"
        />

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.confirm.store') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            <flux:input
                name="password"
                type="password"
                required
                autocomplete="current-password"
                viewable
                :label="__('auth.fields.password')"
                :placeholder="__('auth.fields.password')"
            />

            <flux:button
                variant="primary"
                type="submit"
                class="w-full"
                data-test="confirm-password-button"
            >
                {{ __('auth.confirm.button') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth>
