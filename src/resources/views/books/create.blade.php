<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add a Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('books.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="year" :value="__('Year')" />
                        <x-text-input id="year" name="year" type="number" class="mt-1 block w-full" value="{{ old('year') }}" required />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="isbn" :value="__('ISBN')" />
                        <x-text-input id="isbn" name="isbn" type="text" class="mt-1 block w-full" value="{{ old('isbn') }}" />
                        <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="publisher" :value="__('Publisher')" />
                        <x-text-input id="publisher" name="publisher" type="text" class="mt-1 block w-full" value="{{ old('publisher') }}" />
                        <x-input-error :messages="$errors->get('publisher')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edition_number" :value="__('Edition')" />
                        <x-text-input id="edition_number" name="edition_number" type="text" class="mt-1 block w-full" value="{{ old('edition_number') }}" />
                        <x-input-error :messages="$errors->get('edition_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="price" :value="__('Price')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('price') }}" />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Add Book') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
