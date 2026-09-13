<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $author->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col sm:flex-row gap-6">
                <img src="{{ $author->image ?? asset('images/author-placeholder.png') }}"
                     alt="{{ $author->name }}"
                     class="w-32 h-32 rounded-full object-contain shrink-0">

                @if ($author->bio)
                    <p class="text-gray-800">{{ $author->bio }}</p>
                @endif
            </div>

            <div>
                <h3 class="font-semibold text-lg text-gray-900 mb-4">{{ __('Books') }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($author->books as $book)
                        @include('books.partials.card', ['book' => $book])
                    @empty
                        <p class="text-gray-600 col-span-full">No books by this author yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
