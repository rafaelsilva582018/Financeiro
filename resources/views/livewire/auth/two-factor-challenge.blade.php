<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            {{-- Código do autenticador --}}
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('auth.2fa.code.title')"
                    :description="__('auth.2fa.code.description')"
                />
            </div>

            {{-- Código de recuperação --}}
            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('auth.2fa.recovery.title')"
                    :description="__('auth.2fa.recovery.description')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    {{-- OTP --}}
                    <div x-show="!showRecoveryInput">
                        <div class="my-5 flex items-center justify-center">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                :label="__('auth.2fa.fields.otp')"
                                label:sr-only
                                class="mx-auto"
                            />
                        </div>
                    </div>

                    {{-- Recovery code --}}
                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                :placeholder="__('auth.2fa.fields.recovery')"
                            />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >
                        {{ __('auth.2fa.continue') }}
                    </flux:button>
                </div>

                <div class="mt-5 text-center text-sm leading-5">
                    <span class="opacity-50">{{ __('auth.2fa.switch.prefix') }}</span>
                    <span
                        class="inline cursor-pointer font-medium underline opacity-80"
                        x-show="!showRecoveryInput"
                        @click="toggleInput()"
                    >
                        {{ __('auth.2fa.switch.to_recovery') }}
                    </span>
                    <span
                        class="inline cursor-pointer font-medium underline opacity-80"
                        x-show="showRecoveryInput"
                        @click="toggleInput()"
                    >
                        {{ __('auth.2fa.switch.to_code') }}
                    </span>
                </div>
            </form>
        </div>
    </div>
</x-layouts.auth>
