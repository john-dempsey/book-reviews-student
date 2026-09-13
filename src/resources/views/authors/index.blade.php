<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Authors') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse ($authors as $author)
                    @include('authors.partials.card', ['author' => $author])
                @empty
                    <p class="text-gray-600 col-span-full">No authors in the catalogue yet.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $authors->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
