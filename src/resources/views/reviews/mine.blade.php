<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Reviews') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                @forelse ($reviews as $review)
                    @include('reviews.partials.mine-row', ['review' => $review])
                @empty
                    <p class="text-gray-600">
                        {{ __("You haven't reviewed any books yet.") }}
                        <a href="{{ route('books.index') }}" class="text-gray-800 underline">{{ __('Browse the catalogue') }}</a>
                        {{ __('to find one.') }}
                    </p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
