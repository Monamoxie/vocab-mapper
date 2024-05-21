<?php

namespace Monamoxie\VocabMapper\Tests;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;
use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Providers\VocabMapperServiceProvider;
use Monamoxie\VocabMapper\Services\VocabService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Orchestra\Testbench\artisan;
use Orchestra\Testbench\Attributes\WithMigration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\Hash;
use Monamoxie\VocabMapper\Traits\HasVocab;

#[WithMigration]
abstract class TestCase extends BaseTestCase
{
    use WithWorkbench, RefreshDatabase;

    protected $enablesPackageDiscoveries = true;

    public $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model
        {
            use HasVocab;
        };
    }


    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            VocabMapperServiceProvider::class
        ];
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        artisan($this, 'migrate', ['--database' => 'sqlite']);

        // create a user class for the purpose of testing the user model
        if (!class_exists(config('vocab.user_entity'))) {
            artisan($this, 'make:model User');
        }

        $this->beforeApplicationDestroyed(
            fn () => artisan($this, 'migrate:rollback', ['--database' => 'sqlite'])
        );
    }

    public function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/../workbench');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        parent::getEnvironmentSetUp($app);
    }

    /**
     * Make user
     * 
     * Workbench's UserFactory class returns an instance of lluminate\Foundation\Auth\User
     * which is therefore rejected because it is not an instance of whatever was set in config('vocab.user_entity')
     * 
     * There are 2 ways to fix this ::: 
     * 
     * Either set config('vocab.user_entity') to lluminate\Foundation\Auth\User dynamically
     * Or create a user manually, without using the factory
     * 
     * @returns \App\Models\User
     */
    protected function makeUser(): User
    {
        $user = new User();
        $user->name = fake()->name;
        $user->email = fake()->email;
        $user->password = Hash::make(fake()->password);
        $user->save();

        return $user;
    }
}
