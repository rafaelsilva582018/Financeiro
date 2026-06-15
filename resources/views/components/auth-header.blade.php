@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <p class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400">Finance Online</p>
    <flux:heading size="xl" class="mt-2">{{ $title }}</flux:heading>
    <flux:subheading class="mt-2">{{ $description }}</flux:subheading>
</div>
