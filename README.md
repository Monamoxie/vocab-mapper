<p align="center"><img src="files/img/logo-w.png"></p>

<p align="center">
<img alt="Architecture" src="https://img.shields.io/badge/Architecture-Multi_Tenancy-limegreen?style=plastic">
<a href="https://codecov.io/gh/Monamoxie/vocab-mapper" > 
 <img src="https://codecov.io/gh/Monamoxie/vocab-mapper/branch/master/graph/badge.svg"/> 
 </a>
<a href="https://github.com/Monamoxie/vocab-mapper/actions/workflows/build.yml" _target="blank"><img alt="Github Actions" src="https://github.com/Monamoxie/vocab-mapper/actions/workflows/build.yml/badge.svg?branch=master&event=push"></a>
<img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-limegreen?style=plastic&logo=laravel">
</p>

# VOCAB MAPPER

A Laravel package for both multi-tenant and single tenant architectures.

Vocab Mapper provides your tenants or users a personalized experience, by defining mappings between standard terminology (The Default Vocab) and user-preferred terminology (The Custom Vocab), especially when both terminologies are referring to the same core concept.

## INSTALLATION

```
composer require monamoxie/vocab-mapper
```

## VENDOR PUBLISHING

Vocab Mapper includes some default configurations out of the box, but it's recommended you publish and customize these settings to suit your needs.

#### PUBLISHING THE CONFIG

```
php artisan vendor:publish --tag=vocab-mapper-config
```

#### EXPLORING THE VOCAB CONFIG

###### MIGRATION SUB DIR

```
migration_sub_dir => null
```

This is the subdirectory where Vocab Mapper's migration files will be published. The default value is null, suitable for most single-tenant applications.

For multi-tenant systems needing a different subdirectory for tenant-specific migrations, you can simply set the subdirectory value here. For example, the stancl/tenancy multi-tenant package requires tenant migrations be kept in `database/migrations/tenant`.

If that is the case, then you should set the value of `migration_sub_dir => '/tenant'`

###### ENTITY MODEL

An entity in your application is a unique tenant or user who whiches to use a custom vocabulary for a core concept, different from the standard term.

###### Single-tenant systems

In single-tenant systems, this is typically the model class representing your users. For example, `\App\Models\User::class`, or whichever model namespace manages and identifies your users.

| key            | type           | Default Value             |
| -------------- | -------------- | ------------------------- |
| `entity_model` | `string,array` | `\App\Models\User::class` |

###### Multi-tenant systems

In multi-tenant systems, this is typically the model class representing the tenant. For example, `\App\Models\Tenant::class`, or whichever model namespace manages and identifies your tenants.

| key           | type           | Recommended                 |
| ------------- | -------------- | --------------------------- |
| `user_entity` | `string,array` | `\App\Models\Tenant::class` |

Please refer to the section on [BREAK DOWN OF TERMS](#break-down-of-terms) for a more information on this.

###### ENTITY HAS UUID

Enable this option if the entity model utilizes a UUID as its primary key.

| key                    | type      | Default Value |
| ---------------------- | --------- | ------------- |
| `user_entity_has_uuid` | `boolean` | `false`       |

#### PUBLISHING THE MIGRATION

```
php artisan vendor:publish --tag=vocab-mapper-migration
```

This action will publish the migration files based on the provided configuration in `migration_sub_dir`. If no specific path is set, the files will be published to Laravel's default migration directory.

## MIGRATION

Run the artisan command responsible for handling your Database migrations.

###### Single-tenant systems

```
php artisan migrate
```

###### Multi-tenant systems

The command to execute depends on your setup or package configuration. For example, projects utilizing the stancl/tenancy package would execute:

```
php artisan tenants:migrate
```

## MAKING YOUR ELOQUENT MODELS VOCABLE

A vocab is a name, identity or a domain driven terminology used by default across your application. In an educational project, it could be the word `SUBJECTS` for storing all the subjects taught in a school. In an automotive project, it could be the word `VEHICLES` for identifying all vehicles available in your system.

To make any of your models `VOCABLE`, (which is the ability to maintain and be represented in a different term across multiple tenants or users), you should include the `HasVocab` trait to the model

```
<?php

namespace App\Models;

use Monamoxie\VocabMapper\Traits\HasVocab;
use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
  use HasVocab;
}
```

That's all.

With this, this model, `MyModel`, should now be ready to accept and maintain different identites across each of your tenants or users.

#### CREATING A DEFAULT VOCAB

From any part of your application, you can call

```
<?php

use App\Models\MyModel;

(new MyModel)->createVocab(name: 'Vocab Name');
```

The `name` argument is optional. If not provided, the default vocab (term, identity) for this model would the table name.

#### MAPPING A VOCAB TO AN ENTITY

From any part of your application, you can call

```
<?php

use App\Models\MyModel;

(new MyModel)->mapVocabTo($entity, $customName);
```

where:

```
$entity = An instance of your entity model, as defined in the config, entity_model
```

```
$customName = The custom terminology set or choosen by this $entity.
```

This entity could be the currently logged in tenant or currently logged in user. It doesn't matter.

You must ensure the instance of this entity matches what you defined in the config, `entity_model`.

If there is a mis-match between the $entity instance and whatever was set in the config, `entity_model`, this will thrown an exception of type:

```
Monamoxie\VocabMapper\Exceptions\InvalidEntityModelException
```

##### THE DUALITY OF mapVocabTo

If you attempt to map a vocab to an entity and Vocab Mapper detects that the vocab doesn't exist, it will silently create a new vocab and proceed with the mapping.

#### GETTING THE VOCAB MAPPED TO AN ENTITY

From any part of your application, you can call

```
<?php

use App\Models\MyModel;

(new MyModel)->getVocabFor($entity);
```

where:

```
$entity is an instance of your entity model, as defined in the config, entity_model
```

#### THE VOCAB COLLECTOR

Alternatively, you could also take advantage of the Vocab Collector for getting the vocab mapped to a user:

```
<?php

use App\Models\MyModel;
use Monamoxie\VocabMapper\Facades\VocabCollector;

VocabCollector::getFor($entity, MyModel::class)
```

where:

```
$entity is an instance of your entity model.

This could be an instance of \App\Models\Tenant, or \App\Models\User or whatever. As long as it the namespace matches what you defined in the `user_model` config
```

#### THE DIFFERENCE BETWEEN getVocabFor and VocabCollector::getFor

`(new MyModel)->getVocabFor($entity)` will return an eloquent model, which is an instance of `Monamoxie\VocabMapper\VocabMapper`

You may want to use this to inspect the model or for any eloquent operations.

Alternatively, `VocabCollector::getFor` returns an instance of `Monamoxie\VocabMapper\Data\VocabResponse`, which is ideal for displaying this data in the client-facing part of your application.

The `VocabResponse` provides the custom vocabulary defined by the entity, including its singular and plural forms.

For instance, you could have a setup like this in your controller:

```
<?php

use App\Models\MyModel;
use Monamoxie\VocabMapper\Facades\VocabCollector;

public function index()
{
    $entity = Tenant::where('id', auth()->user()->id)->first();

    return view('academy.foo.bar', [
        'vocab' => VocabCollector::getFor($entity, MyModel::class),
        'data' => 'some random data',
        'data2' => 'some other random data'
    ]);
}
```

And if it's an API driven setup, you could also have something like this. It doesn't matter.

```
<?php

use Illuminate\Support\Facades\Response;
use App\Models\MyModel;
use Monamoxie\VocabMapper\Facades\VocabCollector;

public function get()
{
    $entity = Tenant::where('id', auth()->user()->id)->first();

    return Response::json([
      'message' => '',
      'data' => 'SOME DATA',
      'vocab' => VocabCollector::getFor($entity, MyModel::class),
    ], 200);
}
```

You are also not limited to working with just the instance of `Monamoxie\VocabMapper\Data\VocabResponse`.

You can convert the response into an array by calling the `toArray()` method on it.

```
VocabCollector::getFor($entity, MyModel::class)->toArray()
```

#### BREAK DOWN OF TERMS

<details>
  <summary>Entity</summary>
   <p>
   An entity in your application is an authenticated and unique tenant or user who uses a custom vocabulary for a core concept, different from the standard term.
    </p>
</details>
<details>
  <summary>Vocab</summary>
   <p>
  The name of the concept you provide, as known or defined by you. By default, the vocab is the model's table name.
    </p>
</details>
<details>
  <summary>Vocab Mapper</summary>
   <p>
    A mapping of custom names to existing vocabs. It is advisable to store custom names in their plural form. For instance, use "subjects" instead of "subject." The VocabResponse will handle the conversion between singular and plural forms in its response.
    </p>
</details>
<details>
  <summary>Handler</summary>
   <p>
   The class namespace responsible for managing this vocab, typically the model class for that vocab, but it can also be a non-model class.
    </p>
</details>
 
## TESTING

```
vendor/bin/phpunit
```

For console-based coverage tests:

```
vendor/bin/phpunit --coverage-text
```

OR

For html-based coverage tests:

```
 vendor/bin/phpunit --coverage-html test-coverage
```

## CONTRIBUTING

```
vendor/bin/testbench workbench:install
```

## LICENSE

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
