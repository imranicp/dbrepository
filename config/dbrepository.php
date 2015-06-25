<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Disable package
    |--------------------------------------------------------------------------
    |
    | When you set this to true, data saved to your database won't be saved to
    | a repository table.
    |
    */

    'disabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Save user id that made changes
    |--------------------------------------------------------------------------
    |
    | If you wan't to track who made changes in your database, set this to true.
    | Package will get user id from Auth object
    |
    */

    'save_user' => false,

    /*
    |--------------------------------------------------------------------------
    | Observed models
    |--------------------------------------------------------------------------
    |
    | An array of model classes that are watched for saving event.
    | E.g. App\User
    |
    */

    'listen' => [],
];

