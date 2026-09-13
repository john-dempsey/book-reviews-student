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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($books as $book)
                    @include('books.partials.card', ['book' => $book])
                @empty
                    <p class="text-gray-600 col-span-full">No books in the catalogue yet.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
