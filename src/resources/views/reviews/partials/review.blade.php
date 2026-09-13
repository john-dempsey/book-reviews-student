<div class="border-b border-gray-200 py-4 last:border-b-0">
    <div class="flex items-center justify-between">
        <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
        <span class="text-sm text-gray-500">{{ $review->rating }} / 5</span>
    </div>

    @if ($review->comment)
        <p class="text-gray-700 mt-1">{{ $review->comment }}</p>
    @endif

    <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('d M Y') }}</p>
</div>
