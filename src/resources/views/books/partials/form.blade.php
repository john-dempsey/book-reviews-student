{{-- Shared between create.blade.php and edit.blade.php. $book is only set
     when editing - old('field', $book->field ?? '') falls back to the
     book's current value there, or to an empty string on create, and to
     whatever was just typed in on either page if validation just failed. --}}
<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $book->title ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="4"
              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $book->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div>
    <x-input-label for="year" :value="__('Year')" />
    <x-text-input id="year" name="year" type="number" class="mt-1 block w-full" value="{{ old('year', $book->year ?? '') }}" required />
    <x-input-error :messages="$errors->get('year')" class="mt-2" />
</div>

<div>
    <x-input-label for="isbn" :value="__('ISBN')" />
    <x-text-input id="isbn" name="isbn" type="text" class="mt-1 block w-full" value="{{ old('isbn', $book->isbn ?? '') }}" />
    <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
</div>

<div>
    <x-input-label for="publisher" :value="__('Publisher')" />
    <x-text-input id="publisher" name="publisher" type="text" class="mt-1 block w-full" value="{{ old('publisher', $book->publisher ?? '') }}" />
    <x-input-error :messages="$errors->get('publisher')" class="mt-2" />
</div>

<div>
    <x-input-label for="edition_number" :value="__('Edition')" />
    <x-text-input id="edition_number" name="edition_number" type="text" class="mt-1 block w-full" value="{{ old('edition_number', $book->edition_number ?? '') }}" />
    <x-input-error :messages="$errors->get('edition_number')" class="mt-2" />
</div>

<div>
    <x-input-label for="price" :value="__('Price')" />
    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('price', $book->price ?? '') }}" />
    <x-input-error :messages="$errors->get('price')" class="mt-2" />
</div>

<div>
    <x-input-label for="image" :value="__('Cover image')" />

    @isset($book)
        @if ($book->image)
            <img src="{{ Storage::url($book->image) }}" alt="Current cover of {{ $book->title }}" class="mt-2 h-32 object-contain">
        @endif
    @endisset

    <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700">
    <x-input-error :messages="$errors->get('image')" class="mt-2" />
</div>

<div class="flex items-center gap-4">
    <x-primary-button>{{ isset($book) ? __('Save Changes') : __('Add Book') }}</x-primary-button>
</div>
