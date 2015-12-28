# DbRepository

DbRepository is simple yet powerful library designed to fetch and store changes made to watched Laravel Models.
With this libraby you can easly access to historical data saved to repositiory table. You can fetch historical data by id or by date.

## Installation
Require this package with composer:

<code>composer require wilgucki/dbrepository</code>

After updating composer, add the ServiceProvider to your service providers array in app.php config file.

	'providers' => [
	    //... 
	    Wilgucki\DbRepository\DbRepositoryServiceProvider::class,
	]

We're almost done. Now add event listener in EventServiceProvider class (app/Providers/EventServiceProvider.php).

    protected $subscribe = [
        //...
        'Wilgucki\DbRepository\Listener\DbRepositoryListener'
    ];

Final step - publish config file with artisan command

<code>php artisan vendor:publish</code>

## Usage
Having DbRepositiory package installed, add all models you want to track to listen array in dbrepositiory.php config file.

    'listen' => [
        //...
        'App\User',
    ]
    
Next you need to run dbrepository:generate command:

<code>php artisan dbrepository:generate</code>

It will create migration file as well as model class named Repository[YourModelClass] for each model class added to listen array.
Last step is to run migration to create repository tables. If you want to, you can modify migration file.

<code>php artisian migrate</code>

Built-in event listener will fetch all *created*, *updated* and *deleted* events fired by the model classes declared
in config file and save fetched data into repository table. Each row will be saved with corresponding event type -
*created*, *updated* or *deleted*.

If you are using Auth service, you can save an id of a user, who made the changes.
To do so, you need to set <code>save_user</code> option to <code>true</code> in dbdrepository.php config file.
Saving user will work only if user is logged in. In other case row will be savd without user id.

### How to disable package?
If you want to disable package, you don't need to remove it from the project. You can change <code>disabled</code>
option to <code>true</code> in dbrepository.php config file.

