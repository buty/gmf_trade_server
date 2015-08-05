<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Support\Facades\Cache;

class ProtectDGZQ extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'protect:DGZQ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'proected DGZQ.';

	protected $_stock_trade_api = '';

	const SYSTEM_UID = 1000;

	const BEAT_INTERVAL_TIME = 60; //间隔60秒

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$ac = $this->argument('ac');
		$params = $this->option('params');
		
		$method = '_do' . ucfirst($ac);

		if(method_exists($this, $method)) {
			$res = call_user_func_array([$this, $method], array($params));
		}

		$this->info($res);
	}

	private function _doInit() {
		$this->_stock_trade_api = app('StockTrade\Common');
		$res = $this->_stock_trade_api->initProxySystem();
		if(isset($res['results']) && is_array($res['results'][0]['comm_total_num']))  {
			$comm_total_num = $res['results'][0]['comm_total_num'];
			//存储东莞交易中间件的连接数
			$this->_saveCommTotalNum($comm_total_num);
			$msg = 'init DGZQ proxy success';
		} else {
			$msg = !empty($res['results'][0]['error_info']) ? $res['results'][0]['error_info'] : 'init DGZQ proxy failure';
		}

		return $msg;
	}

	private function _getCacheKey() {
		return 'DGZQ_COMM_TOTAL_NUM';
	}

	private function _saveCommTotalNum($comm_total_num) {
		return Cache::forever($this->_getCacheKey(), $comm_total_num);
	}

	private function _getCommTotalNum() {
		return Cache::get($this->_getCacheKey());
	}

	private function _doBeat() {
		$this->_stock_trade_api = app('StockTrade\Common');
		$num = $this->_getCommTotalNum();
		for($i = 0; $i < $num; $i++) {
			$this->_stock_trade_api->sendHeartBeating(self::SYSTEM_UID, $i);
			sleep(self::BEAT_INTERVAL_TIME);
		}
		if($num) {
			$msg = 'total num: ' . $num . ' sended over.';
		} else
			$msg = 'no data sended.';

		return $msg;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['ac', InputArgument::REQUIRED, 'subtle hint. ac=init or beat'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['params', null, InputOption::VALUE_OPTIONAL, 'An params option.', null],
		];
	}

}
