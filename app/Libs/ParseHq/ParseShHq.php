<?php namespace App\Libs\ParseHq;


/**
 * 解析上海行情
 */
class ParseShHq extends BaseParse {

	public function handle($target) {
		$file = $this->_getFilePath($target);
		$this->_load($file);

		$record_num = $this->getSize();
		for ($i = 1; $i < $record_num; $i++) { //记录数从1开头,到$record_numm
			$data = $this->getRecord($i);
			$this->_handleData($data);
		}

	}

	//数据处理
	private function _handleData($data) {
		$data = $this->_mixData($data);
		$res = $this->_saveData($data);
	}

	//数据对齐
	private function _mixData($data){

	}

	//数据入库(新增或更新)
	private function _saveData() {

	}

}