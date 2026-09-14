<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'year',
        'image',
        'isbn',
        'publisher',
        'edition_number',
        'price',
    ];

    /**
     * The authors who wrote this book.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * The reviews left for this book.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Restrict the query to books whose title contains the given search
     * term. A blank/missing term leaves the query untouched, so this is
     * always safe to chain onto - a search box with nothing typed into it
     * should show the whole catalogue, not an empty page.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where('title', 'like', '%'.$term.'%');
    }
}
