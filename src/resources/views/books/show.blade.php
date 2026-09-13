<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $book->title }}
            </h2>

            <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col sm:flex-row gap-6">
                <img src="{{ $book->image ? Storage::url($book->image) : asset('images/book-placeholder.png') }}"
                     alt="Cover of {{ $book->title }}"
                     class="w-full sm:w-48 h-48 object-contain rounded-md">

                <div class="flex-1">
                    <p class="text-gray-700">
                        {{ __('by') }}
                        @foreach ($book->authors as $author)
                            <a href="{{ route('authors.show', $author) }}" class="text-blue-600 hover:underline">{{ $author->name }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                        &middot; {{ $book->year }}
                    </p>

                    @if ($book->description)
                        <p class="text-gray-800 mt-4">{{ $book->description }}</p>
                    @endif

                    {{-- Part 1 keeps one implicit edition per book, so these
                         bibliographic fields live directly on the book. --}}
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm text-gray-600 mt-4">
                        @if ($book->isbn)
                            <dt class="font-medium">{{ __('ISBN') }}</dt>
                            <dd>{{ $book->isbn }}</dd>
                        @endif
                        @if ($book->publisher)
                            <dt class="font-medium">{{ __('Publisher') }}</dt>
                            <dd>{{ $book->publisher }}</dd>
                        @endif
                        @if ($book->edition_number)
                            <dt class="font-medium">{{ __('Edition') }}</dt>
                            <dd>{{ $book->edition_number }}</dd>
                        @endif
                        @if ($book->price)
                            <dt class="font-medium">{{ __('Price') }}</dt>
                            <dd>&euro;{{ number_format($book->price, 2) }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-900">
                    {{ __('Reviews') }}
                    @if ($book->reviews->isNotEmpty())
                        <span class="text-gray-500 font-normal text-base">
                            ({{ number_format($book->reviews->avg('rating'), 1) }} / 5 average, {{ $book->reviews->count() }} {{ Str::plural('review', $book->reviews->count()) }})
                        </span>
                    @endif
                </h3>

                <div class="mt-2">
                    @forelse ($book->reviews as $review)
                        @include('reviews.partials.review', ['review' => $review])
                    @empty
                        <p class="text-gray-600">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
