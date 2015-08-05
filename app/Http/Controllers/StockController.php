<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\UserTradeList;
use App\Models\OrderList;

class StockController extends Controller {

	private $_stock_trade_api = '';

	public function __construct() {
		$this->_stock_trade_api = app('StockTrade\Common');
	}

	private function _outPutJson($data, $is_single = true) {
		if($is_single)
			return response()->json($data['results'][0]);
		else
			return response()->json($data['results']);
	}

	private function _guessStockExchangeType($stock_code) {
		$exchange_type = 0;
		$prefix_str = substr($stock_code, 0, 1);
		if($prefix_str == '6') {
			$exchange_type = 1;
		} else {
			$exchange_type = 2;
		}

		return $exchange_type;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex(Request $request)
	{		
		return view('stock/index');
	}

	//获取股票信息
	public function getCodeInfo(Request $request) {
		$stock_code = $request->input('stock_code');
		//$exchange_type = $request->input('exchange_type');
		$exchange_type = $this->_guessStockExchangeType($stock_code);
		$uid = $request->user()->id;
		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->entrustStock(
			$uid, $trade_user_info['fund_account'], $trade_user_info['password'], 
			$exchange_type, '', $stock_code);
		
		return $this->_outPutJson($data);
	}

	//委托价格
	public function getEntrustPrice(Request $request) {
		$uid = $request->user()->id;
		$trade_user_info = $this->_getUserTradeInfo($uid);
		//$exchange_type = $request->input('exchange_type');
		$stock_account = '';
		$stock_code = $request->input('stock_code');
		$exchange_type = $this->_guessStockExchangeType($stock_code);
		$entrust_price = $request->input('entrust_price');

		$data = $this->_stock_trade_api->entrustPrice($uid, $trade_user_info['fund_account'], $trade_user_info['password']
			, $exchange_type, 
		$stock_account, $stock_code, $entrust_price, '0');

		return $this->_outPutJson($data);
	}

	//确认委托
	public function postConfirmEntrust(Request $request) {
		$uid = $request->user()->id;
		$trade_user_info = $this->_getUserTradeInfo($uid);
		//$exchange_type = $request->input('exchange_type');

		$stock_code = $request->input('stock_code');
		$exchange_type = $this->_guessStockExchangeType($stock_code);
		$entrust_amount = $request->input('entrust_amount');
		$entrust_price = $request->input('entrust_price');
		$entrust_bs = $request->input('entrust_bs');
		$stock_account = '';
		$data = $this->_stock_trade_api->entrustConfirm($uid, $trade_user_info['fund_account'], $trade_user_info['password'], $exchange_type, 
		$stock_account, $stock_code, $entrust_amount, $entrust_price,
		$entrust_bs, '0');

		if(isset($data['results'][0]) && $data['results'][0]['error_no'] == 0) {
			$this->_saveEntrustNo($trade_user_info['fund_account'], $uid, $data['results'][0]['entrust_no']);
		}
		
		return $this->_outPutJson($data);
	}

	/**
	 * 保存订单信息
	 * @param  int $pid        产品唯一标识符
	 * @param  int $uid        当前操作用户
	 * @param  int $entrust_no 需要记录的的订单号
	 * @return void            
	 */
	private function _saveEntrustNo($pid, $uid, $entrust_no) {
		$redis = app('DbRedis');
		//存储指定产品账号下面的订单
		$gid_cache_key = $pid . '_entrust_no';
		$redis->rPush($gid_cache_key, $entrust_no);
		$redis->expire($gid_cache_key, 86400);
		//委托单号与uid的对应
		$uid_map_cache_key = 'entrust_no_' . $entrust_no;
		$redis->set($uid_map_cache_key, $uid);
		$redis->expire($uid_map_cache_key, 86400);
	}

	private function _getStockPrice($uid, $stock_code_arr) {
		$price_arr = [];
		if(is_array($stock_code_arr) && !empty($stock_code_arr)) {
			foreach ($stock_code_arr as $k => $v) {
				$v = (string) $v;
				$stock_code = $v;
				$exchange_type = $this->_guessStockExchangeType($v);
				$trade_user_info = $this->_getUserTradeInfo($uid);
				$data = $this->_stock_trade_api->entrustStock(
				$uid, $trade_user_info['fund_account'], $trade_user_info['password'], 
				$exchange_type, '', $stock_code);
				
				if(isset($data['results']) && $data['results'][0]['last_price']) {
					$price_arr[$v] = array(
							'price' => $data['results'][0]['last_price'],
							'name'  => $data['results'][0]['stock_name']
							);
				}
			}
		}

		return $price_arr;	
	}

	//获取交易查询，持仓
	public function getTradeList(Request $request) {
		$uid = $request->user()->id;
		// $user_trade_list = new UserTradeList;
		// $data = $user_trade_list->where('uid', $uid)->select('stock_amount as current_amount', 'stock_code', 'cost_price', 
		// 	'enable_amount', 'last_amount', 'last_price')->orderBy('created_at', 'DESC')->limit(5)->get();
		// if($data) {
		// 	$data = $data->toArray();
		// 	foreach ($data as $k => $v) {
		// 		$price_arr = $this->_getStockPrice($uid, array($v['stock_code']));
		// 		if(!$price_arr) continue;
		// 		$current_price = $price_arr[$v['stock_code']]['price'];
		// 		$data[$k]['stock_name'] = $price_arr[$v['stock_code']]['name'];
		// 		$data[$k]['market_value'] = $current_price * $v['current_amount'];
		// 		$data[$k]['income_balance'] = ($current_price - $v['cost_price']) * $v['current_amount'];
		// 		$data[$k]['profit_loss_ratio'] = ( round( ( ($current_price - $v['cost_price']) / $v['cost_price']), 5) * 100) . '%';
		// 		$data[$k]['yesterday_income_balance'] = ($current_price - $v['last_price']) * $v['last_amount'];
		// 		$data[$k]['today_income_balance'] = $data[$k]['income_balance'] - $data[$k]['yesterday_income_balance'];
		// 	}

		// 	$data['results'] = $data;
		// }

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryStock($uid, $trade_user_info['fund_account'], $trade_user_info['password'], '', '');

		return $this->_outPutJson($data, false);
	}

	//获取订单，委托
	public function getOrderList(Request $request) {
		$uid = $request->user()->id;
		// $order_list = new OrderList;
		// $data = $order_list->where('uid', $uid)->select('stock_code', 'stock_name', 'order_price as entrust_price', 
		// 	'order_amount as entrust_amount',
		// 	'status_name', 'order_time', 'buyorsell', 'business_amount', 'business_price')->orderBy('created_at', 'DESC')->limit(10)->get();

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryEntrust($uid, $trade_user_info['fund_account'], $trade_user_info['password'], '', '');
		// if($data) {
		// 	$data['results'] = $data->toArray();
		// }

		return $this->_outPutJson($data, false);
	}

	//撤消委托
	public function postCancelOrder(Request $request) {
		$uid = $request->user()->id;
		$trade_user_info = $this->_getUserTradeInfo($uid);
		//$exchange_type = $request->input('exchange_type');

		$entrust_no = $request->input('entrust_no');
		$data = $this->_stock_trade_api->entrustWithdrawOrder($uid, $trade_user_info['fund_account'], $trade_user_info['password'], $entrust_no);

		return $this->_outPutJson($data);
	}

	public function getQueryCanCancelOrder(Request $request)  {
		$uid = $request->user()->id;

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryCanWithdrawOrder($uid, $trade_user_info['fund_account'], $trade_user_info['password']);

		return $this->_outPutJson($data, false);
	}

	public function getDealList(Request $request) {
		$uid = $request->user()->id;

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryDeal($uid, $trade_user_info['fund_account'], $trade_user_info['password'], '1', 'A607807514');

		return $this->_outPutJson($data, false);
	}

	//查询资金证券
	public function getQueryFund(Request $request) {
		$uid = $request->user()->id;
		$html = '';

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryFund($uid, $trade_user_info['fund_account'], $trade_user_info['password']);
		if(isset($data['results'][0])) {
			$v = $data['results'][0];
			$html = "<thead>
			          <tr>
			            <th>币种类别</th>
			            <th>当前余额</th>
			            <th>可用金额</th>
			            <th>可取金额</th>
			            <th>待入账利息</th>
			            <th>资产总值（不含基金市值）</th>
			            <th>可取现金</th>
			            <th>资金（= 资产总值 - 证券市值）</th>
			            <th>证券市值</th>
			            <th>基金市值</th>
			            <th>预计利息</th>
			          </tr></thead><tbody>
			          <tr>";
			$html .= sprintf("<td>%s</td>", $v['money_type'] == '0' ? '人民币' : '其它');
			$html .= sprintf("<td>%s</td>", $v['current_balance']);
			$html .= sprintf("<td>%s</td>", $v['enable_balance']);
			$html .= sprintf("<td>%s</td>", $v['fetch_balance']);
			$html .= sprintf("<td>%s</td>", $v['interest']);
			$html .= sprintf("<td>%s</td>", $v['asset_balance']);
			$html .= sprintf("<td>%s</td>", $v['fetch_cash']);
			$html .= sprintf("<td>%s</td>", $v['fund_balance']);
			$html .= sprintf("<td>%s</td>", $v['market_value']);
			$html .= sprintf("<td>%s</td>", $v['opfund_market_value']);
			$html .= sprintf("<td>%s</td>", $v['pre_interest']);
			$html .= '</tr></tbody>';
		}
		echo $html;
		die();
		
	}

	//查询用户信息
	public function getQueryUserinfo(Request $request)  {
		$uid = $request->user()->id;
		$html = '';

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryUserInfo($uid, $trade_user_info['fund_account'], $trade_user_info['password']);

		if(isset($data['results'][0])) {
			$v = $data['results'][0];
			$html = "<thead>
			          <tr>
			            <th>分支代码</th>
			            <th>资金卡号</th>
			            <th>客户姓名</th>
			            <th>出生日期</th>
			            <th>证件号码</th>
			            <th>投资人户名</th>
			            <th>移动电话</th>
			            <th>盈亏计算方式</th>
			            <th>客户性别</th>
			          </tr></thead><tbody>
			          <tr>";
			$html .= sprintf("<td>%s</td>", $v['branch_no']);
			$html .= sprintf("<td>%s</td>", $v['fund_card']);
			$html .= sprintf("<td>%s</td>", $v['client_name']);
			$html .= sprintf("<td>%s</td>", $v['birthday']);
			$html .= sprintf("<td>%s</td>", $v['id_no']);
			$html .= sprintf("<td>%s</td>", $v['last_name']);
			$html .= sprintf("<td>%s</td>", $v['mobiletelephone']);
			$html .= sprintf("<td>%s</td>", $v['profit_flag'] . '[盈亏计算方式(0买入成本价：历史买入金额／历史买入数量；1买入摊薄成本价：历史买卖金额差／历史买卖数量差；2保本价：扣除卖出费用后的成本价；3买入摊薄成本价体现当天买入：历史买卖金额差+本日买卖金额差／(历史买卖数量－本日买卖数量))]');
			$html .= sprintf("<td>%s</td>", $v['client_sex']);
			$html .= '</tr></tbody>';
		}

		echo $html;
		die();
	}

	//查询股东
	public function getQueryStockUser(Request $request) {
		$uid = $request->user()->id;
		$html = '';

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->queryStockHolder($uid, $trade_user_info['fund_account'], $trade_user_info['password']);

		return $this->_outPutJson($data, false);
	}

	//系统登录
	public function getQuerySystemInfo(Request $request) {
		$uid = $request->user()->id;
		$html = '';

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->sendHeartBeating($uid, 1, ['init_date', 'sys_status', 'sys_status_name', 'curr_date']);

		return $this->_outPutJson($data);
	}

	//证券用户登录
	public function postUserLogin(Request $request) {
		$uid = $request->user()->id;
		$html = '';

		$trade_user_info = $this->_getUserTradeInfo($uid);
		$data = $this->_stock_trade_api->userLogin($uid, $trade_user_info['fund_account'], $trade_user_info['password']);

		return $this->_outPutJson($data);
	}

	private function _getUserTradeInfo($uid) {
		return [
			"fund_account" => "100010225",
			"password" => "111111",
			];
	}
}
