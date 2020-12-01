<?php

namespace Sneedus\Acquiring\Providers;

use GuzzleHttp\Client;
use Sneedus\Acquiring\Acquiring;
use \Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Sneedus\Acquiring\Clients\BankClient;
use Sneedus\Acquiring\Clients\BetaBankClient;
use Sneedus\Acquiring\Clients\SphereBankClient;
use Sneedus\Acquiring\Exceptions\AcquiringException;

class AcquiringServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BankClient::class, function(Application $app){
            return $this->makeClient($app["config"]["acquiring"]["driver"]);
        });
        $this->app->bind(Acquiring::class, function(Application $app){
            return new Acquiring($app->make(BankClient::class));
        });
    }

    private function makeClient(string $driver): BankClient
    {
        switch($driver){
            case "betabank":
                return new BetaBankClient(new Client);
            case "spherebank":
                return new SphereBankClient(new Client);
            default:
            throw new AcquiringException("Driver {$driver} not defined");
        }
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->publishes([
            __DIR__.'/../../config/acquiring.php' => config_path('acquiring.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../../config/acquiring.php', 'acquiring'
        );
    }
}