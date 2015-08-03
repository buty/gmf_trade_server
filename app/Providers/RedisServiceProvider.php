<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redis;

class RedisServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	public function register()
	{
		$this->app->singleton('DbRedis', function($app)
        {
            return Redis::connection();
        });
	}

	/**
     * 取得提供者所提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return ['DbRedis'];
    }

}
