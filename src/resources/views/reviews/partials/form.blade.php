{{-- Shared between create.blade.php and edit.blade.php. $review is only set
     when editing - old('field', $review->field ?? '') falls back to the
     review's current value there, or to an empty string on create, and to
     whatever was just typed in on either page if validation just failed. --}}
<div>
    <x-input-label for="rating" :value="__('Rating')" />
    <select id="rating" name="rating" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a rating') }}</option>
        @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}" @selected(old('rating', $review->rating ?? '') == $i)>{{ $i }} / 5</option>
        @endfor
    </select>
    <x-input-error :messages="$errors->get('rating')" class="mt-2" />
</div>

<div>
    <x-input-label for="comment" :value="__('Comment')" />
    <textarea id="comment" name="comment" rows="4"
              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment', $review->comment ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('comment')" class="mt-2" />
</div>

<div class="flex items-center gap-4">
    <x-primary-button>{{ isset($review) ? __('Save Changes') : __('Submit Review') }}</x-primary-button>
</div>
