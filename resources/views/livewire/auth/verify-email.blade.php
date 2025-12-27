<x-layouts.auth>
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {{ __('auth.verify.message') }}
        </flux:text>

        @if (session('status') === 'verification-link-sent')
            <flux:text class="text-center font-medium !text-green-600 !dark:text-green-400">
                {{ __('auth.verify.sent') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full"
                >
                    {{ __('auth.verify.resend') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button
                    variant="ghost"
                    type="submit"
                    class="cursor-pointer text-sm"
                    data-test="logout-button"
                >
                    {{ __('auth.logout') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth>
