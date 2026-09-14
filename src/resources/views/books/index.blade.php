<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Books') }}
            </h2>

            <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Add Book') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('books.index') }}" class="mb-6 flex gap-3">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Search') }}
                </button>

                @if (request('search'))
                    <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 text-xs text-gray-500 hover:text-gray-800 hover:underline">
                        {{ __('Clear') }}
                    </a>
                @endif
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($books as $book)
                    @include('books.partials.card', ['book' => $book])
                @empty
                    <p class="text-gray-600 col-span-full">
                        @if (request('search'))
                            {{ __('No books match ":search".', ['search' => request('search')]) }}
                        @else
                            {{ __('No books in the catalogue yet.') }}
                        @endif
                    </p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
