# dbextender
It extends laravel \ lumen default Query builder.

# How to install
<code>composer require chiragardeshna/dbextender 1.*</code>

# Laravel Setup
Run <code>php artisan vendor:publish</code>.<br/>
Put <code>Chiragardeshna\Dbextender\DBExtenderServiceProvider::class</code> in config/app.php in providers array.

# Lumen Setup
Create dbextender.php file and put it in config/dbextender.php.<br/>

<b>dbextender.php</b>

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query Builder
    |--------------------------------------------------------------------------
    |
    | Here you can specify a class which extends default Illuminate\Database\Query\Builder.
    | It's important that specified class extend behaviour from default Query Builder class.
    |
    */
    'builder' =>  'App\Query\Builder',

];
```

Register this new config file in bootstrap/app.php.<br/>
<code>$app->register(Chiragardeshna\Dbextender\DBExtenderServiceProvider::class);</code><br/>
<code>$app->configure('dbextender');</code><br/>

Create app/query/Builder.php file

```php
<?php

namespace App\Query;

use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{
    // put your methods here.
}
```

And that's it enjoy.



