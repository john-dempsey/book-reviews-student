<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review') }}: {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="space-y-6">
                    @csrf

                    @include('reviews.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
