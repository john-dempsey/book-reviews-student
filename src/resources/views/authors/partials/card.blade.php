<div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 flex gap-4">
    <img src="{{ $author->image ?? asset('images/author-placeholder.png') }}"
         alt="{{ $author->name }}"
         class="w-16 h-16 rounded-full object-contain shrink-0">

    <div>
        <h3 class="font-semibold text-lg text-gray-900">
            <a href="{{ route('authors.show', $author) }}" class="hover:underline">
                {{ $author->name }}
            </a>
        </h3>

        <p class="text-sm text-gray-500">
            {{ $author->books_count }} {{ Str::plural('book', $author->books_count) }}
        </p>
    </div>
</div>
