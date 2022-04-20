<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $models = array (
            'User',
            'Category',
            'Product',
        );
        foreach($models as $model)
        {
            $this->app->bind("App\Repositories\\{$model}\\{$model}RepositoryInterface","App\Repositories\\{$model}\\{$model}Repository");
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
