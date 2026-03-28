<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\User;
use App\Services\MovieService\MovieRepository;
use App\Services\MovieService\MovieRepositoryInterface;
use App\Services\VideoStorageService\LocalVideoService;
use App\Services\VideoStorageService\VideoServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MovieRepositoryInterface::class, MovieRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(RequestFactoryInterface::class, HttpFactory::class);
        $this->app->bind(VideoServiceInterface::class, LocalVideoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('comment-delete', function (User $user, Comment $comment) {
            if ($user->isModerator()) {
                return true;
            }

            return $user->id === $comment->user_id && $comment->children->isEmpty();
        });

        Gate::define('comment-update', function (User $user, Comment $comment) {
            if ($user->isModerator()) {
                return true;
            }

            return $user->id === $comment->user_id;
        });
    }
}
