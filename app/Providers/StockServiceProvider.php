<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libs\StockTrade\DGZQ;

class StockServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
		//$api = app('StockTrade\Common');var_dump($api);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('StockTrade\Common', function($app)
        {
            return new DGZQ($app['config']['stock']);
        });
	}

	/**
     * 取得提供者所提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return ['StockTrade\Common'];
    }

}
