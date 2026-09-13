<div class="bg-white overflow-hidden shadow-sm rounded-lg flex flex-col">
    <img src="{{ $book->image ? Storage::url($book->image) : asset('images/book-placeholder.png') }}"
         alt="Cover of {{ $book->title }}"
         class="w-full h-48 object-contain">

    <div class="p-4 flex flex-col grow">
        <h3 class="font-semibold text-lg text-gray-900">
            <a href="{{ route('books.show', $book) }}" class="hover:underline">
                {{ $book->title }}
            </a>
        </h3>

        <p class="text-sm text-gray-600 mt-1">
            {{ $book->authors->pluck('name')->join(', ') ?: 'Unknown author' }}
            &middot; {{ $book->year }}
        </p>

        <p class="text-sm text-gray-500 mt-auto pt-2">
            @if ($book->reviews_count > 0)
                {{ number_format($book->reviews_avg_rating, 1) }} / 5
                ({{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }})
            @else
                No reviews yet
            @endif
        </p>
    </div>
</div>
