<?php namespace App\Libs\StockTrade;

/**
* 东莞证券交易api
*/
class DGZQ extends BaseTicket {

	protected $_version     = '3.8';

	protected $_branch_no   = '';

	protected $_entrust_way = '7';

	protected $_op_station  = '';

	public function __construct($config) {
		$this->_initProxyPlugin(__CLASS__, $config);
	}

	private function _mergeHeadData($data, $function_id, $branch_no = '0', $uid = '', $op_entrust_way = '', $version = '') {
		$common_data = array('function_id' => (string)$function_id);
		
		$common_data['branch_no'] = $common_data['op_branch_no'] = $branch_no;
		
		if($uid) {
			$common_data['op_station'] = $this->_mergePlatformUserId($uid);
		}
		
		$common_data['op_entrust_way'] = $op_entrust_way ? $op_entrust_way : $this->_entrust_way;
		$common_data['version'] = $version ? $version : $this->_version;

		return array_merge($common_data, $data);
	}

	//初始化插件，获得连接数据，用于心跳
	public function initProxySystem() {
		$inputs = [];
		$results = [];
		$tags = ['cmd' => 'status'];

		$res = $this->_sendMsg($inputs, $results, $tags);

		return $res;
	}

	public function sendHeartBeating($uid, $comm_no, $results = []) {
		$inputs = [];
		
		$tags = ['cmd' => 'beat', 'comm' => $comm_no];
		$inputs = $this->_mergeHeadData($inputs, 100, '0', $uid);
		$res = $this->_sendMsg($inputs, $results, $tags);

		return $res;
	}

	//功能号：407 (查询股东) 
	public function queryStockHolder($uid, $fund_account, $password, $exchange_type = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type
			);
		$inputs = $this->_mergeHeadData($inputs, 407, '0', $uid);

		$results = array('exchange_type', 'exchange_name','stock_account', 
			'holder_status', 'holder_rights', 'holder_kind', 'main_flag', 'enable_amount');

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：200 (客户登录校验) 用户信息(可用余额) 查询用户的 branch_no
	public function userLogin($uid, $fund_account, $password) {
		$inputs = array(
				'input_content' => '1',
				'content_type'  => '0',
				'account_content'=> $fund_account,
				'password'      => $password
			);
		$inputs = $this->_mergeHeadData($inputs, 200, '0', $uid);
		
		$results = array('error_no', 'error_info', 'content_type', 'account_content',
			'branch_no', 'fund_account', 'online_time', 'client_id', 'client_name', 'fundaccount_count', 'money_count',
			'money_type', 'square_flag', 'enable_balance', 'current_balance', 'client_rights', 'bank_no', 'exchange_type',
			'exchange_name', 'stock_account', 'login_date', 'login_time', 'last_op_entrust_way', 'last_op_station', 'last_op_ip',
			'bank_trans_flag', 'tabconfirm_flag', 'initpasswd_flag', 'init_date', 'last_date', 'company_name', 'message_flag',
			'sys_status', 'sys_status_name', 'remark', 'corp_client_group');
		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}
	
	//功能号：300 (委托股票) 显示当前股票基本卖买信息
	public function entrustStock($uid, $fund_account, $password, $exchange_type, 
		$stock_account, $stock_code, $entrust_prop = '0') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'entrust_prop'  => $entrust_prop
			);
		$inputs = $this->_mergeHeadData($inputs, 300, '0', $uid);

		$results = array("error_no", "error_info", "branch_no", "fund_account", "exchange_type", "exchange_name", "stock_account", "stock_code", "stock_name", "stock_type", "money_type", "last_price", "up_price", "down_price", "cost_price", "hand_flag", "income_balance", "enable_amount", "transmit_amount", "enable_balance", "fetch_balance", "stock_interest", "notice_no", "notice_info", "high_amount", "low_amount", "par_value", "stbtrans_type", "stkcode_status", "stkcode_status_name");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}


	//功能号：301 (委托价格) 查询可买入数量   bk_enable_balance参数可不要（苏工通过抓包没有发现此参数）
	public function entrustPrice($uid, $fund_account, $password, $exchange_type, 
		$stock_account, $stock_code, $entrust_price, $entrust_prop = '0') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'entrust_price' => $entrust_price,
				'entrust_prop'  => $entrust_prop
			);
		$inputs = $this->_mergeHeadData($inputs, 301, '0', $uid);

		$results = array('enable_amount', 'error_no', 'error_info');

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：302 (委托确认)
	public function entrustConfirm($uid, $fund_account, $password, $exchange_type, 
		$stock_account, $stock_code, $entrust_amount, $entrust_price, 
		$entrust_bs, $entrust_type, $entrust_prop = '0') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'entrust_amount'=> $entrust_amount,
				'entrust_price' => $entrust_price,
				'entrust_bs'    => $entrust_bs,
				'entrust_type'  => $entrust_type,
				'entrust_prop'  => $entrust_prop
			);
		$inputs = $this->_mergeHeadData($inputs, 302, '0', $uid);

		$results = array('entrust_no', 'error_no', 'error_info');

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：303 (可撤单查询)
	public function queryCanWithdrawOrder($uid, $fund_account, $password, $entrust_no = '', 
		$stock_account = '', $request_num = '10', $position_str = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'stock_account' => $stock_account,
				'entrust_no'    => $entrust_no,
				'request_num'   => $request_num,
				'position_str'  => $position_str,
			);
		$inputs = $this->_mergeHeadData($inputs, 303, '0', $uid);

		$results = array("exchange_type", "stock_account", "stock_code", "stock_name", "entrust_no", "entrust_bs", "entrust_price", "entrust_amount", "business_amount", "entrust_status");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：304 (委托撤单)
	public function entrustWithdrawOrder($uid, $fund_account, $password, $entrust_no, $batch_flag = '0', $exchange_type = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'entrust_no' 	=> $entrust_no,
				'batch_flag'    => $batch_flag,
				'exchange_type' => $exchange_type
			);
		$inputs = $this->_mergeHeadData($inputs, 304, '0', $uid);

		$results = array("error_no", "error_info", "entrust_no");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：401 (查询委托)
	public function queryEntrust($uid, $fund_account, $password, $exchange_type, $stock_account, 
		$stock_code = '', $locate_entrust_no = '', $query_type = '1', $query_direction = '1', $sort_direction = '1', 
		$action_in = '1', $en_entrust_status = '', $request_num = '10', $position_str = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'locate_entrust_no' => $locate_entrust_no,
				'query_type'    => $query_type,
				'query_direction'   => $query_direction,
				'sort_direction'=> $sort_direction,
				'action_in'		=> $action_in,
				'en_entrust_status' => $en_entrust_status,
				'request_num'	=> $request_num,
				'position_str'	=> $position_str
			);
		$inputs = $this->_mergeHeadData($inputs, 401, '0', $uid);

		$results = array("position_str", "entrust_no", "exchange_type", "stock_account", "stock_code", "stock_name", "entrust_bs", "bs_name", "entrust_price", "entrust_amount", "business_amount", "business_price", "report_no", "report_time", "entrust_type", "type_name", "entrust_status", "status_name", "cancel_info", "entrust_prop", "entrust_prop_name", "entrust_way", "entrust_way_name", "withdraw_amount", "entrust_time", "entrust_date", "curr_date");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}
	
	//功能号：402(查询成交)
	public function queryDeal($uid, $fund_account, $password, $exchange_type, $stock_account, 
		$stock_code = '', $query_type = '0', $query_direction = '1', $query_mode = '0', 
		$serial_no = '', $request_num = '50', $position_str = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'serial_no' 	=> $serial_no,
				'query_type'    => $query_type,
				'query_direction'   => $query_direction,
				'query_mode'	=> $query_mode,
				'request_num'	=> $request_num,
				'position_str'	=> $position_str
			);
		$inputs = $this->_mergeHeadData($inputs, 402, '0', $uid);

		$results = array("position_str", "serial_no", "date", "exchange_type", "exchange_name", "stock_account", "stock_code", "stock_name", "entrust_bs", "bs_name", "business_price", "business_amount", "business_time", "business_status", "status_name", "business_type", "type_name", "business_times", "entrust_no", "report_no", "business_balance", "business_no");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：403 (查询股票)  — 持仓
	public function queryStock($uid, $fund_account, $password, $exchange_type, $stock_account, 
		$stock_code = '', $query_direction = '1', $query_mode = '0', 
		$request_num = '50', $position_str = '') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'exchange_type' => $exchange_type,
				'stock_account' => $stock_account,
				'stock_code'    => $stock_code,
				'query_direction'   => $query_direction,
				'query_mode'	=> $query_mode,
				'request_num'	=> $request_num,
				'position_str'	=> $position_str
			);
		$inputs = $this->_mergeHeadData($inputs, 403, '0', $uid);

		$results = array("position_str", "exchange_type", "stock_account", "stock_code", "stock_name", "current_amount", "enable_amount", "last_price", "cost_price", "income_balance", "hand_flag", "market_value", "sum_buy_amount", "sum_buy_balance", "real_buy_amount", "real_buy_balance", "sum_sell_amount", "sum_sell_balance", "real_sell_amount", "real_sell_balance", "correct_amount", "income_balance_nofare", "uncome_buy_amount", "uncome_sell_amount", "begin_amount", "stock_type", "delist_flag", "delist_date", "par_value");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}

	//功能号：405 (查询资金)  — 当前帐户资金详细
	public function queryFund($uid, $fund_account, $password, $money_type = '0') {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password,
				'money_type' 	=> $money_type
			);
		$inputs = $this->_mergeHeadData($inputs, 405, '0', $uid);

		$results = array("money_type", "current_balance", "enable_balance", "fetch_balance", "interest", "asset_balance", "fetch_cash", "fund_balance", "market_value", "opfund_market_value", "pre_interest");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}
	
	//功能号：415 (查询客户信息) 
	public function queryUserInfo($uid, $fund_account, $password) {
		$inputs = array(
				'fund_account'  => $fund_account,
				'password'      => $password
			);
		$inputs = $this->_mergeHeadData($inputs, 415, '0', $uid);

		$results = array("error_no", "error_info", "branch_no", "client_name", "client_status", "fund_card", "id_kind", "id_no", "last_name", "mail_name", "zipcode", "address", "id_address", "phonecode", "mobiletelephone", "beeppager", "fax", "e_mail", "risk_info", "risk_name", "account_data", "account_data_name", "organ_prop", "organ_name", "client_group", "group_name", "profit_flag", "home_tel", "id_begindate", "id_term", "term_flag", "client_sex", "birthday", "contract_person", "contact_mobile", "relation_idtype", "relation_id", "contract_tel", "instrepr_name", "instrepr_idtype", "instrepr_id", "instrepr_telephone", "sale_licence");

		$res = $this->_sendMsg($inputs, $results);

		return $res;
	}
}