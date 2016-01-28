# DbRepository

DbRepository is simple yet powerful library designed to fetch and store changes made to watched Laravel Models.
With this libraby you can easly access to historical data saved to repositiory table. You can preview historical data by id or by date.
From version 2 you can check diff of two given revisions.

__Since version 2 DbRepository uses json as column type. Make sure that your database supports it.__

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

<code>php artisan vendor:publish --provider="Wilgucki\DbRepository\DbRepositoryServiceProvider"</code>

## Usage
Having DbRepositiory package installed, add all models you want to track to _listen_ array in dbrepositiory.php config file.

    'listen' => [
        //...
        'App\User',
    ]
    
Run dbrepository:generate command.

<code>php artisan dbrepository:generate</code>

It will create migration file as well as model class named Repository[YourModelClass] for each model class added to _listen_ array.
Last step is to run migration to create repository tables.

<code>php artisian migrate</code>

Built-in event listener will fetch all *created*, *updated* and *deleting* events fired by the model classes declared
in config file and save fetched data into repository table as json object. Each row will be saved with corresponding event type -
*created*, *updated* or *deleted*.

If you are using Auth service, you can save an id of a user, who made the changes.
To do so, you need to set <code>save_user</code> option to <code>true</code> in dbdrepository.php config file.
Saving user will work only if user is logged in. In other case row will be savd without user id.

## Fetching revisions

DbRepository provides <code>DbRepository</code> trait which can be used to fetch revisions for specific model.
First of all you can use relation to fetch all saved revisions.

    $users = User::with('revisions')->all();
    foreach ($users as $user) {
        $repository = $user->repository;
        // do magic with repositories
    }

If you need you can fetch repository by its id.

    $user->getRevision(23);

To make things easier trait provides methods to fetch first and last revision.

    $user->getFirstRevision();
    
    $user->getLastRevision();

You also can fetch revisions to and from specific date. As date parameter you can use plain text as well as Carbon object.

    $user->getRevisionsToDate(Carbon::parse('last week'));
    
    $user->getRevisionsFromDate('2015-11-09');

Last but not least - diff. Yes, you can make diff on two revisions and check the differences between them.

    $user->compareRevisions(12, 27);

As a result you will get an array containing name of the column that was changed and new and old value of this column.

    [name] => Array
            (
                [type] => change
                [newvalue] => some new name
                [oldvalue] => some old name
            )

### How to disable package?
If you want to disable package, you don't need to remove it from the project. You can change <code>disabled</code>
option to <code>true</code> in dbrepository.php config file.

## TODO

- handle soft deletes
