<?php

namespace App\Models;

use Database\Factories\WritingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'author_id', 'title', 'slug', 'content', 'cover_image_path', 'language', 'status', 'is_featured', 'is_poem_of_day', 'is_poem_of_week', 'is_anonymous', 'view_count', 'like_count', 'published_at'])]
class Writing extends Model
{
    /** @use HasFactory<WritingFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'draft',
        'language' => 'en',
        'is_featured' => false,
        'is_poem_of_day' => false,
        'is_poem_of_week' => false,
        'is_anonymous' => false,
        'view_count' => 0,
        'like_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_poem_of_day' => 'boolean',
            'is_poem_of_week' => 'boolean',
            'is_anonymous' => 'boolean',
            'view_count' => 'integer',
            'like_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function readingHistories(): HasMany
    {
        return $this->hasMany(ReadingHistory::class);
    }
}
