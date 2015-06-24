# dbrepository
Library that automatically detects changes in your database and stores them in repository table.

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

First, add model class to dbrepository.php config file

    'listen' => [
        //...
        'App\User',
    ]
    
Next, run artisan command

<code>php artisan dbrepository:createtables</code>

This command will create table named like source table with *repository_* prefix as well as Repository[YourModelClass] extending Model class.

Event listener will fetch all *created*, *updated* and *deleted* events fired by the model classes declared in config file and save its data in repository table. Each row will be saved with corresponding event type - *created*, *updated* or *deleted*.

If you are using Auth service, you can save an id of a user, who made changes in database. To do so, you need to set <code>save_user</code> option to <code>true</code> in dbdrepository.php config file. This will be omitted if user isn't logged in.

### How to disable package?
If you want to disable package, you don't need to remove it from the project. You can change <code>disabled</code> option to <code>true</code> in dbrepository.php config file.

