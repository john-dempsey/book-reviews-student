<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add a Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    @include('books.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
