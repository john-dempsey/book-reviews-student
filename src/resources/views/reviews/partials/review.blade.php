<div class="border-b border-gray-200 py-4 last:border-b-0">
    <div class="flex items-center justify-between">
        <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
        <span class="text-sm text-gray-500">{{ $review->rating }} / 5</span>
    </div>

    @if ($review->comment)
        <p class="text-gray-700 mt-1">{{ $review->comment }}</p>
    @endif

    <div class="flex items-center justify-between mt-1">
        <p class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</p>

        @if ($review->user_id === auth()->id())
            <div class="flex items-center gap-3">
                <a href="{{ route('reviews.edit', $review) }}" class="text-xs text-gray-500 hover:text-gray-800 hover:underline">
                    {{ __('Edit') }}
                </a>

                <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                      onsubmit="return confirm('Delete this review? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
