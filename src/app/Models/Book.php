<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
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
}
