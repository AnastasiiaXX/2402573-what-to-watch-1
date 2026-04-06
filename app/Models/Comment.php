<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $film_id
 * @property int|null $user_id
 * @property string $comment
 * @property int|null $rating
 * @property int|null $parent_id
 * @property Carbon $created_at
 * @property-read mixed $author_name
 * @property-read Collection<int, Comment> $children
 * @property-read int|null $children_count
 * @property-read Film $film
 * @property-read Comment|null $parent
 * @property-read User|null $user
 */

class Comment extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    public const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'comment',
        'rating',
    ];

    protected $appends = ['author_name'];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Film>
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * @return BelongsTo<Comment>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * @return HasMany<Comment>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * @return Attribute
     */
    public function authorName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user->name ?? 'guest'
        );
    }
}
