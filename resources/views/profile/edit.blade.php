<x-admin-layout>
    <x-slot name="title">
        Pengaturan Profil
    </x-slot>
    
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold" style="color: var(--color-primary-dark);">Pengaturan Profil</h1>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" style="border: 1px solid var(--color-border);">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" style="border: 1px solid var(--color-border);">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" style="border: 1px solid var(--color-border);">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-admin-layout>
