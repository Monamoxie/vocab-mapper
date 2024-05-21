<?php

namespace Monamoxie\VocabMapper\Providers;

use Composer\InstalledVersions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Services\VocabService;
use Illuminate\Foundation\AliasLoader;
use Monamoxie\VocabMapper\Facades\VocabCollector;
use Monamoxie\VocabMapper\Services\VocabCollectorService;
use Monamoxie\VocabMapper\Traits\VocabHelper;

class VocabMapperServiceProvider extends ServiceProvider
{
    use VocabHelper;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                dirname(__DIR__, 2) . '/config/vocab.php' => config_path('vocab.php'),
            ], 'vocab-mapper-config');

            $this->publishes([
                dirname(__DIR__, 2) . '/database/migrations' => $this->getMigrationDirectory(),
            ], 'vocab-mapper-migration');


            AboutCommand::add('Vocab Mapper', static fn () => array_filter([
                'Version' => InstalledVersions::getPrettyVersion('monamoxie/vocab-mapper'),
            ]));
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {

        $this->mergeConfigFrom(dirname(__DIR__, 2) . '/config/vocab.php', 'vocab');
        $this->loadMigrationsFrom(dirname(__DIR__, 2) . '/database/migrations');

        $loader = AliasLoader::getInstance();
        $loader->alias('VocabCollector', VocabCollector::class);

        $this->app->bind(VocabInterface::class, function ($app) {
            $config = $app['config']['vocab'];

            return new VocabService($config);
        });

        $this->app->bind('VocabCollector', function ($app) {
            $vocabService = $app->make(VocabInterface::class);

            return new VocabCollectorService($vocabService);
        });
    }
}
