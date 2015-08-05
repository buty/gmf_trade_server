<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Services\KgiClass as Kgi;

class KgiController extends Controller {

	private $_stock_trade_api = '';

	public function __construct() {
		#$this->_stock_trade_api = app('StockTrade\Common');
	}


	private function _outPutJson($data, $is_single = true) {
		return response()->json($data);
	}

    //确认委托
    public function postConfirmEntrust(Request $request) {
        //$uid = $request->user()->id;
        //$trade_user_info = $this->_getUserTradeInfo($uid);
        //$exchange_type = $request->input('exchange_type');

		$symbol = $request->input('stock_code');
		$qty = $request->input('entrust_amount');
        $price = $request->input('entrust_price');
		$side = $request->input('entrust_bs');
        $cl_order_id = Kgi::_get_cl_order_id();
        Kgi::create_order($cl_order_id, $symbol, $side, $price, $qty);

        $data = array(
            "error_no" => 0,
            "error_info" => ""
        );

        return $this->_outPutJson($data);
    }

	//DealList
	public function getDealList(Request $request) {
		$msg_type=array("D");
		$status=array(2);
		$results = Kgi::get_order_list($msg_type, $status);

		$res = array();
		foreach($results as $k=>$v) {
			$res[$k]['cl_order_id'] = $v['cl_order_id'];
			$res[$k]['orig_cl_order_id'] = $v['orig_cl_order_id'];
			$res[$k]['symbol'] = $v['symbol'];
			$res[$k]['side'] = $v['side'];
			$res[$k]['price'] = $v['price'];
			$res[$k]['order_qty'] = $v['order_qty'];
			$res[$k]['order_status'] = $v['order_status'];
			$res[$k]['exchange'] = $v['exchange'];
			$res[$k]['transact_time'] = $v['transact_time'];
			$res[$k]['created_at'] = $v['created_at'];
			$res[$k]['updated_at'] = $v['updated_at'];
			$res[$k]['text'] = $v['text'];
		}

		return $this->_outPutJson($res);;
	}

	public function getOrderList(Request $request) {
		$msg_type=array("D");
		$status=array(0,1,3,6,8,"E","N");
		$results = Kgi::get_order_list($msg_type, $status);

		$res = array();
		foreach($results as $k=>$v) {
			$res[$k]['cl_order_id'] = $v['cl_order_id'];
			$res[$k]['orig_cl_order_id'] = $v['orig_cl_order_id'];
			$res[$k]['symbol'] = $v['symbol'];
			$res[$k]['side'] = $v['side'];
			$res[$k]['price'] = $v['price'];
			$res[$k]['order_qty'] = $v['order_qty'];
			$res[$k]['order_status'] = $v['order_status'];
			$res[$k]['exchange'] = $v['exchange'];
			$res[$k]['transact_time'] = $v['transact_time'];
			$res[$k]['created_at'] = $v['created_at'];
			$res[$k]['updated_at'] = $v['updated_at'];
			$res[$k]['text'] = $v['text'];
		}

		return $this->_outPutJson($res);;
	}

	public function getQueryCancelOrder() {
		$msg_type=array("F");
		$status=array(0,1,2,3,4,5,6,8,"E","N");
		$results = Kgi::get_order_list($msg_type, $status);

		$res = array();
		foreach($results as $k=>$v) {
			$res[$k]['cl_order_id'] = $v['cl_order_id'];
			$res[$k]['orig_cl_order_id'] = $v['orig_cl_order_id'];
			$res[$k]['msg_type'] = $v['msg_type'];
			$res[$k]['symbol'] = $v['symbol'];
			$res[$k]['side'] = $v['side'];
			$res[$k]['price'] = $v['price'];
			$res[$k]['order_qty'] = $v['order_qty'];
			$res[$k]['order_status'] = $v['order_status'];
			$res[$k]['exchange'] = $v['exchange'];
			$res[$k]['transact_time'] = $v['transact_time'];
			$res[$k]['created_at'] = $v['created_at'];
			$res[$k]['updated_at'] = $v['updated_at'];
			$res[$k]['text'] = $v['text'];
		}

		return $this->_outPutJson($res);;
	}

	public function getModifyList() {
		$msg_type=array("G");
		$status=array(0,1,2,3,4,5,6,8,"E","N");
		$results = Kgi::get_order_list($msg_type, $status);

		$res = array();
		foreach($results as $k=>$v) {
			$res[$k]['cl_order_id'] = $v['cl_order_id'];
			$res[$k]['orig_cl_order_id'] = $v['orig_cl_order_id'];
			$res[$k]['symbol'] = $v['symbol'];
			$res[$k]['side'] = $v['side'];
			$res[$k]['price'] = $v['price'];
			$res[$k]['order_qty'] = $v['order_qty'];
			$res[$k]['order_status'] = $v['order_status'];
			$res[$k]['exchange'] = $v['exchange'];
			$res[$k]['transact_time'] = $v['transact_time'];
			$res[$k]['created_at'] = $v['created_at'];
			$res[$k]['updated_at'] = $v['updated_at'];
			$res[$k]['text'] = $v['text'];
		}

		return $this->_outPutJson($res);;
	}

	public function postCancelOrder(Request $request) {
		$cancel_order_id = $request->input('cl_order_id');
		$order_info = Kgi::get_order_info_by_cl_order_id($cancel_order_id);

		//$symbol, $side, $cancel_order_id
		$cl_order_id = Kgi::_get_cl_order_id();
		Kgi::cancel_order($cl_order_id, $order_info['symbol'], $order_info['side'], $cancel_order_id);

		$data = array(
			"error_no" => 0,
			"error_info" => ""
		);

		return $this->_outPutJson($data);
	}

	public function postModifyOrder(Request $request) {
		$modify_order_id = $request->input('order_id');
		$modify_price = $request->input('price');
		$modify_qty = $request->input('order_qty');

		$order_info = Kgi::get_order_info_by_cl_order_id($modify_order_id);

		//$cl_order_id, $symbol, $side, $modif_order_id, $price, $qty
		$cl_order_id = Kgi::_get_cl_order_id();
		Kgi::modify_order($cl_order_id, $order_info['symbol'], $order_info['side'], $modify_order_id, $modify_price, $modify_qty);

		$data = array(
			"error_no" => 0,
			"error_info" => ""
		);

		return $this->_outPutJson($data);
	}

}
