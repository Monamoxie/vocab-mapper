<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Migration Sub Dir
    |--------------------------------------------------------------------------
    |
    | This is the sub directory within database/migrations where migration files will be published to. 
    | In some Multi-tenant packages, tenant migrations are kept in a separate folder within database/migrations
    | If that is the case for your application, you should add specify the path e.g tenant, tenancy
    |   
    | The default value is an empty string, which means if migrations files are publised, they will be published
    | into database/migrations, which should suffice for almost all use cases. 
    |
    */

    'migration_sub_dir' => null,

    /*
    |--------------------------------------------------------------------------
    | Entity Model
    |--------------------------------------------------------------------------
    |
    | Your application's entity model, or as the case may be, the model for identifying each unique entity
    | This can either be a string or an array of model namespaces, 
    | e.g \App\Models\User::class or [\App\Models\User::class, \App\Models\Tenant::class]
    |
    | When mapping a vocab to an entity, the entity model that was passed as an argument must match this value or one of these values.  
    |
    */
    'entity_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | entity Model Has UUID
    |--------------------------------------------------------------------------
    |
    | Set to true if one or more of your entity_entity above uses uuid for it's id 
    */
    'entity_has_uuid' => false
];
