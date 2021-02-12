composer require webcityro/larasearch

php artisan vendor:publish --tag=larasearch-config

php artisan larasearch:make:request --multi FetchUsersRequest

php artisan larasearch:make:query UserQuery --model=Models/User

php artisan larasearch:make:repository UserRepository Models/User

php artisan make:resource UserResource

Add the next line to your service provider's boot method.
$this->app->bind(\App\Repositories\Contracts\UserRepositoryContract::class, \App\Repositories\Eloquent\UserRepositoryContract::class);
