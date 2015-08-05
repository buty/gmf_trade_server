<?php namespace App\Libs\ParseHq;

class BaseParse {

	private $_fp = '';

	private $_header = '';

	private $_size = 0;

	public function __construct($file = '') {
		if($file) {
			$this->_load($file);
		}
	}

	protected function _getFilePath($target) {
		return config('hqfile.' . $target);
	}

	protected function _load($file) {
		if($this->_fp) return $this->_fp;

		$this->_fp = dbase_open($file, 0);
		$this->_setHeader();
		$this->_setSize();
	}

	private function _setSize() {
		$this->_size = dbase_numrecords($this->_fp);
	}

	public function getSize() {
		return $this->_size;
	}

	private function _setHeader() {
		$this->_header = dbase_get_header_info($this->_fp);
	}

	public function getHeader() {
		return $this->_header;
	}

	public function getRecord($record_num) {
		$record = dbase_get_record_with_names($this->_fp, $record_num);

		return $record;
	}

	//统一转换需要的数据
	protected function _translateNeedKeys(Array $key_arr, $data) {
		$return_data = [];
		foreach ($key_arr as $k => $v) {
			
		}
	}

	protected function _getMyDataBaseKeys() {
		return ['stock_code', 'stock_name', 'close_price', 'open_price', 'business_price', 
			'business_amount', 'business_num', 'top_price', 'low_price', 'market_ratio',
			'price_up_down', 'market_value',
			'buy_1', 'buy_2', 'buy_3', 'buy_4', 'buy_5',
			'sell_1', 'sell_2', 'sell_3', 'sell_4', 'sell_5'
			];
	}

}