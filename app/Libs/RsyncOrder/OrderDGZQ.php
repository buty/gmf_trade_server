<?php namespace App\Libs\RsyncOrder;


use App\Models\OrderList;

/**
 * 交易订单同步
 */
class OrderDGZQ {

	private $_max_num = 500; //最多一千行

	private $_order_status = 8;//已成

	private $_stock_trade_api = '';

	public function handle() {
		
		$return_arr = array();
		$this->_stock_trade_api = app('StockTrade\Common');
		$uid = '10000';
		$trade_user_info = $this->_getUserTradeInfo($uid);
		
		$redis = app('DbRedis');
		$gid_queue_key = $trade_user_info['fund_account'] . '_entrust_no';
		$calculate_queue_key = 'calculate_entrust_no';
		$failure_entrust_no = [];
		while ($entrust_no = $redis->lPop($gid_queue_key)) {
			//获取本地订单信息
			$order = new OrderList();
			$order_info = $order->where('order_id', $entrust_no)->first();
			if($order_info) {
				$order_info = $order_info->toArray();
			}
			if($order_info['order_status'] == 8) continue;


			$data = $this->_getSingleOrderRecord($uid, $trade_user_info['fund_account'], $trade_user_info['password'], $entrust_no);
			if(isset($data['results']) && is_array($data['results'])) {
				$uid_map_cache_key = 'entrust_no_' . $entrust_no;
				$current_uid = $redis->get($uid_map_cache_key);
				$return_arr[] = $this->_handleData($order_info, $data['results'], $current_uid, $trade_user_info['fund_account']);

				//缓存当天的订单数据
				
				//只记录状态完成的订单号
				if($data['results'][0]['entrust_status'] == 8) {
					//记录待计算的订单号
					$redis->rPush($calculate_queue_key, $entrust_no);
				} else {
					$failure_entrust_no[] = $entrust_no;
				}
			} else {
				$failure_entrust_no[] = $entrust_no;
			}
		}

		if(!empty($failure_entrust_no)) {
			foreach ($failure_entrust_no as $k => $v) {
				$redis->rPush($gid_queue_key, $v);
			}
		}

		return $return_arr;
	}

	private function _handleData($order_info, $data, $uid, $gid) {
		$res = [];
		foreach ($data as $k => $v) {
			$order = new OrderList();
			//如果此信息已经存在，则更新
			if($order_info['id']) {
				$order->id = $order_info['id'];
				$order->where(
						'id', $order_info['id']
					)->update(
						[
							'order_status' => $v['entrust_status'],
							'order_time'   => $v['entrust_date'],
							'order_amount' => $v['entrust_amount'],
							'order_price'  => $v['entrust_price'],
							'status_name'  => $v['status_name'],
							'business_amount'  => $v['business_amount'],
							'business_price'  => $v['business_price'],
						]
					);
			} else {
				$order->uid = $uid;
				$order->gid = $gid;
				$order->order_id = $v['entrust_no'];
				$order->stock_code = $v['stock_code'];
				$order->stock_name = $v['stock_name'];
				$order->order_price = $v['entrust_price'];
				$order->order_amount = $v['entrust_amount'];
				$order->order_status = $v['entrust_status'];
				$order->order_time = $v['entrust_date'];
				$order->buyorsell = $v['entrust_bs'];
				$order->status_name = $v['status_name'];
				$order->business_amount = $v['business_amount'];
				$order->business_price = $v['business_price'];
				$order->save();
				$res[] = $order->id;				
			}
			break;
		}

		return $res;
	}

	private function _getSingleOrderRecord($uid, $fund_account, $password, $entrust_no) {
		$data = $this->_stock_trade_api->queryEntrust($uid, $fund_account, $password, '', '', '', $entrust_no);

		return $data;
	}

	private function _getUserTradeInfo($uid) {
		return [
			"fund_account" => "100010225",
			"password" => "111111",
			];
	}

}