# dbrepository
Library that automatically detect changes in your database and stores them in repository table.

## Warning
This library is under development. Don't use it in production environment!

## Installation
You can install this library via composer

<code>composer require wilgucki/dbrepository</code>

Next you need to add ServiceProvider in your app.php config file.


	'providers' => [
	 //... 
	 Wilgucki\DbRepository\DbRepositoryServiceProvider::class,
	]

We're almost done. Now add event listener in EventServiceProvider class (app/Providers/EventServiceProvider.php).

    protected $subscribe = [
        //...
        'Wilgucki\DbRepository\Listener\DbRepositoryListener'
    ];

Last but not least publish config file with artisan command

<code>php artisan vendor:publish</code>

## Usage
Having library installed, you can point which model classes you want to observe in order to save changes made upon them.

First add model class to dbrepository.php config file

    'listen' => [
        //...
        'App\User',
    ]
    
Next run artisan command <code>php artisan dbrepository:createtables</code>

This command will create table named like source table with *_repository* suffix.

Event listener will fetch all *saving* events fired by declared in config file model and save it in repository table.

##TODO
1. Tests.
2. Detect column type while creating repository table.
3. Others. Any ideas?