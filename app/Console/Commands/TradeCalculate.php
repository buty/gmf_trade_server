<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\UserTradeList;
use App\Models\OrderList;

class TradeCalculate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'trade:calculate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'calculate stock trade.';

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
		$redis = app('DbRedis');
		$calculate_queue_key = 'calculate_entrust_no';
		$order_list = new OrderList;
		

		$trade_data = [];
		while ($entrust_no = $redis->lPop($calculate_queue_key)) {
			$data = $order_list->where('order_id', $entrust_no)->first()->toArray();
			
			if($data)
				$trade_data = $this->_handleTradeData($entrust_no, $trade_data, $data);
		}

		if(is_array($trade_data) && !empty($trade_data)) {
			foreach ($trade_data as $k => $v) {
				$this->_tradeDataInsertDb($v);
			}
		}
		
		$this->info('task finished...');
	}

	private function _tradeDataInsertDb($v) {
		$user_trade_list = new UserTradeList;
		$user_trade_info = $user_trade_list->where('uid', $v['uid'])
								->where('gid', $v['gid'])
								->where('stock_code', $v['stock_code'])
								->first();
		if($user_trade_info) {
			$user_trade_info = $user_trade_info->toArray();
		}
						
		if(isset($user_trade_info['id'])) {
			$user_trade_list->where(
				'id', $user_trade_info['id']
				)->update(
					[
					'stock_amount' => $this->_reCalculateStockAmount($user_trade_info, $v['order_amount']),
					'enable_amount'=> $this->_reCalculateStockEnableAmount($user_trade_info, $v['enable_amount']),
					'cost_price'   => $this->_reCalculateAveragePrice($user_trade_info, $v['order_amount'], $v['order_price'])
					]
				);
			$user_trade_list->id = $user_trade_info['id'];
		} else {
			$user_trade_list->uid = $v['uid'];
			$user_trade_list->gid = $v['gid'];
			$user_trade_list->stock_code = $v['stock_code'];
			$user_trade_list->stock_amount = $this->_reCalculateStockAmount($user_trade_info, $v['order_amount']);
			$user_trade_list->enable_amount = $this->_reCalculateStockEnableAmount($user_trade_info, $v['enable_amount']);
			$user_trade_list->cost_price = $this->_reCalculateAveragePrice($user_trade_info, $v['order_amount'], $v['order_price']);
			$user_trade_list->save();			
		}
	}

	private function _reCalculateStockAmount($user_trade_info, $stock_amount) {
		$new_stock_amount = $stock_amount;
		if(isset($user_trade_info['stock_amount'])) {
			$new_stock_amount = $this->_calculateStockAmount($user_trade_info['stock_amount'], $stock_amount);
		}

		return $new_stock_amount;
	}

	private function _reCalculateStockEnableAmount($user_trade_info, $stock_enable_amount) {
		$new_stock_enable_amount = $stock_enable_amount;
		if(isset($user_trade_info['enable_amount'])) {
			$new_stock_enable_amount = $this->_calculateStockEnableAmount($user_trade_info['enable_amount'], $stock_enable_amount);
		}

		return $new_stock_enable_amount;
	}

	private function _reCalculateAveragePrice($user_trade_info, $stock_amount, $stock_price) {
		$new_price = $stock_price;
		if(isset($user_trade_info['id'])) {
			$new_price = $this->_calculateAveragePrice($user_trade_info['stock_amount'], $stock_amount, $user_trade_info['cost_price'], $stock_price);
		}

		return $new_price;
	}

	//计算交易数据
	private function _handleTradeData($entrust_no, $trade_data, $data) {
		if(isset($trade_data[$entrust_no])) {
			$order_amount = $trade_data[$entrust_no]['order_amount'];
			//股票持有
			$trade_data[$entrust_no]['order_amount']   = $this->_calculateStockAmount($order_amount, $data['order_amount'], $data['buyorsell'], $data['order_status']);
			//股票可卖数量
			$trade_data[$entrust_no]['enable_amount']  = $this->_calculateStockEnableAmount($order_amount, $data['order_amount'], $data['buyorsell'], $data['order_status']);
			//股票均价
			$trade_data[$entrust_no]['order_price']   = $this->_calculateAveragePrice($trade_data[$entrust_no]['order_amount'], $data['order_amount'], $trade_data[$entrust_no]['order_price'], $data['order_price'], $data['buyorsell'], $data['order_status']);

		} else {
			$data['enable_amount'] = $data['order_amount'];
			$trade_data[$entrust_no] = $data;
		}

		return $trade_data;
	}

	//计算股票持有数量.  bs 1:买; 2:卖  $order_status 8 为完成 其他为未完成
	private function _calculateStockAmount($old_amount, $amount, $bs = 1, $order_status = 8) {
		$total_amount = 0;
		if($bs == 1 && $order_status == 8) {
			$total_amount = $old_amount + $amount;
		} elseif($bs == 2 && $order_status == 8) {
			$total_amount = $old_amount - $amount;
		} else {
			$total_amount = $old_amount;
		}
		
		return $total_amount;
	}
	//计算股票可卖数量
	private function _calculateStockEnableAmount($old_amount, $amount, $bs = 1, $order_status = 8) {
		$total_amount = 0;
		if($bs == 1 && $order_status == 8) {
			$total_amount = $old_amount + $amount;
		} elseif($bs == 2 || ($order_status == 8)) { //可卖数量的计算
			$total_amount = $old_amount - $amount;
		} else {
			$total_amount = $old_amount;
		}
		
		return $total_amount;
	}

	private function _calculateAveragePrice($old_amount, $amount, $old_price, $price, $bs = 1) {
		$new_price = 0;
		if($bs == 1) { //买入
			$new_price = (($old_amount * $old_price) + ($amount * $price)) / ($old_amount + $amount);
		} else {
			$new_price = (($old_amount * $old_price) - ($amount * $price)) / ($old_amount - $amount);
		}

		return $new_price;
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
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
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
