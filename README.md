
# Laravel 12 Passport Setup using `install:api --passport`

This guide sets up Laravel Passport authentication in Laravel 12 using the new `php artisan install:api --passport` command.

---

## ✅ Step 1: Install Laravel Passport

```bash
composer require laravel/passport
```

This will:
- Configure `auth.php` with Passport
- Update `User` model
- Set up API routes
- Register `Passport::routes()`
- Add `HasApiTokens` trait

---

## ✅ Step 2: Publish Passport Migrations

```bash
php artisan vendor:publish --tag=passport-migrations
```

Then run the migrations:

```bash
php artisan migrate
```

---

## ✅ Step 3: Install Passport and Create Personal Access Client

To install Passport:

```bash
php artisan passport:install
```

Or if you only need a personal access client:

```bash
php artisan passport:client --personal
```

> When prompted, enter a name for the client (e.g., `Auth API`).

---

## ✅ Step 4: Update AuthServiceProvider

In `app/Providers/AuthServiceProvider.php`, register Passport routes:

```php
use Laravel\Passport\Passport;

public function boot()
{
    $this->registerPolicies();

    Passport::routes();
}
```

---

## ✅ Step 5: Update User Model

In `app/Models/User.php`, use the `HasApiTokens` trait:

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

---

## ✅ Step 6: Update config/auth.php

In `config/auth.php`, update the `guards` section:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

---

## ✅ Step 7: Use Middleware in Routes

In `routes/api.php`:

```php
use Illuminate\Http\Request;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('refresh', [AuthController::class, 'refreshToken']);
    Route::post('logout', [AuthController::class, 'logout']);
});
```

---

## ✅ Step 8: Generate Personal Access Token

Generate a token in your controller or logic:

```php
$token = $user->createToken('Auth API')->accessToken;
```

---

## ✅ Step 9: (Optional) Set Token Expiry Time

To set token expiry time in `AuthServiceProvider.php`:

```php
Passport::personalAccessTokensExpireIn(now()->addMonths(6));
```

---

## ✅ Step 10: Test the Setup

1. Use the login route to get the access token using user credentials.
2. Use the token in headers: `Authorization: Bearer <token>`
3. Access protected routes.

---

## ✅ Done!

You have successfully configured Laravel Passport for API authentication using personal access tokens.
