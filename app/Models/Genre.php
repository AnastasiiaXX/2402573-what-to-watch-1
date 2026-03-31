<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model represents film genres
 *
 * @property int $id
 * @property string $name
 * @property-read Collection<int, Film> $films
 * @property-read int|null $films_count
 */
class Genre extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\GenreFactory> */
    use HasFactory;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Returns Film associated with this genre
     *
     * @return BelongsToMany<Film>
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
