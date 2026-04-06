<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property bool $is_favourite
 * @property string|null $name
 * @property string|null $poster_image
 * @property string|null $preview_image
 * @property string|null $background_image
 * @property string|null $background_color
 * @property string|null $video_link
 * @property string|null $preview_video_link
 * @property string|null $description
 * @property string|null $director
 * @property array<array-key, mixed>|null $starring
 * @property int|null $run_time
 * @property int|null $released
 * @property string $imdb_id
 * @property string $status
 * @property int $is_promo
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, Favourite> $favourites
 * @property-read int|null $favourites_count
 * @property-read Collection<int, Genre> $genres
 * @property-read int|null $genres_count
 * @property-read mixed $rating
 * @property-read mixed $scores_count
 **/
class Film extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory;

    public $timestamps = false;
    public $appends = ['rating', 'scores_count'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'poster_image',
        'preview_image',
        'background_image',
        'background_color',
        'description',
        'video_link',
        'preview_video_link',
        'director',
        'starring',
        'run_time',
        'released',
        'imdb_id',
        'status',
        'is_promo'
    ];

    protected $casts = [
        'starring' => 'array',
    ];

    /**
     * @return HasMany<Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany<Genre>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return HasMany<Favourite>
     */
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Calculates a film rating
     * @return Attribute
     */
    protected function rating(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (array_key_exists('rating_avg', $this->attributes)) {
                    return (float)$this->attributes['rating_avg'];
                }
                return $this->comments()->avg('rating');
            }
        );
    }

    /**
     * Gets the number of reviews
     *
     * @return Attribute
     */
    protected function scoresCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (array_key_exists('scores_count', $this->attributes)) {
                    return (int)$this->attributes['scores_count'];
                }
                return $this->comments()->count();
            }
        );
    }
}
