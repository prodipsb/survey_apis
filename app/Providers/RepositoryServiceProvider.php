<?php

namespace App\Providers;

use App\Repositories\DeviceServiceIssueRepository;
use App\Repositories\DeviceServiceIssueRepositoryInterface;
use App\Repositories\DeviceServiceRepository;
use App\Repositories\DeviceServiceRepositoryInterface;
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
        $this->app->bind(DeviceServiceIssueRepositoryInterface::class, DeviceServiceIssueRepository::class);
        $this->app->bind(DeviceServiceRepositoryInterface::class, DeviceServiceRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
