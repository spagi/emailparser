<?php declare(strict_types=1);

namespace Spagi\EmailParser;

use Illuminate\Support\ServiceProvider;
use Spagi\EmailParser\Observers\PaymentsObserver;

class EmailParserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->app->bind(
            'Spagi\EmailParser\Integration'
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/emailparser.php' => config_path('emailparser.php'),
        ]);
        Payments::observe(PaymentsObserver::class);
    }
}
