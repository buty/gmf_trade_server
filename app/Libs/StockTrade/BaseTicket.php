<?php namespace App\Libs\StockTrade;

/**
 * 交易基础信息
 */
class BaseTicket {

	protected $_plugin_version = '1.0';

	protected $_version     = '';

	protected $_branch_no   = '5043';

	protected $_entrust_way = '';

	protected $_op_station  = '';
	
	protected $_plugin = '';

	protected $_platform_suffix = '_gmf';

	protected function _initProxyPlugin($config_key, $config) {
		if(!$this->_plugin) {
			$config = $this->_getConfig($config_key, $config);
			$this->_plugin = new ProxyPlugin($config['host'], $config['port']);			
		}

		return $this->_plugin;
	}

	protected function _getConfig($config_key, $config) {
		$config_key = substr(strrchr($config_key, '\\'), 1);
		if(isset($config[$config_key])) {
			return $config[$config_key];
		} else {
			return false;
		}
	}

	protected function _mergePlatformUserId($uid) {
		return  substr(md5($uid), -8) . $this->_platform_suffix;
	}

	protected function _mergeInputData($inputs, $results, $tags) {
		$data = array(
			'tags'   => array('plugin_version' => $this->_plugin_version),
			'inputs' => $inputs,
			'returnKeys'=> $results
			);
		if($tags) {
			$data['tags'] = array_merge($data['tags'], $tags);
		}
		return $data;
	}

	protected function _sendMsg($inputs, $results, $tags = ['cmd' => 'command']) {
		$data = $this->_mergeInputData($inputs, $results, $tags);
		$res = $this->_plugin->dispathPlugin($data);

		return $res;
	}
}
