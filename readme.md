# Env Syncer #
[![Build Status](https://travis-ci.org/bmitch/envsync.svg?branch=master)](https://travis-ci.org/bmitch/envsync)

## How to install ##

`composer require bmitch/envsync`

Then add `Bmitch\Envsync\EnvsyncServiceProvider::class` to the Providers array in config/app.php:

```
'providers' => [
   Bmitch\Envsync\EnvsyncServiceProvider::class,
]
```

Then run `php artisan` and you should see it listed as an available command.
