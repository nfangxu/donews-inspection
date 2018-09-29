<?php
/**
 * Created by PhpStorm.
 * User: nfangxu
 * Date: 2018/9/29
 * Time: 13:21
 */

namespace Fangxu\SafetyInspection;

use Fangxu\SafetyInspection\SafetyInspection;
use Fangxu\SafetyInspection\AliyunSafetyInspectionService;
use Illuminate\Support\ServiceProvider;

class SafetyInspectionServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SafetyInspection::class, function ($app) {
            return new AliyunSafetyInspectionService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [AliyunSafetyInspectionService::class];
    }
}