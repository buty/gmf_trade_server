<?php namespace App\Libs\StockTrade;

/**
 * 交易数据分发至不同的AR
 */
class ProxyPlugin
{
	private $_host = '192.168.0.4';

	private $_port = 8091;

	private $_decode = true;

	const VERSION = 1.0;

	public function __construct($host = '', $port = '') {
		$this->_host = $host ? $host : $this->_host;
		$this->_port = $port ? $port : $this->_port;
	}



	public function dispathPlugin($data, $from = 'DG') {
		$cont = $this->_sendProxyPlugin($data);
		if($this->_decode) {
			$cont = json_decode($cont, true);
		}
		return $cont;
	}

	private function _sendProxyPlugin($data) {
		if(!$this->_checkArrayParams($data)) {
			return false;
		}

		$data = $this->_mergeProxyData($data);
		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!$socket) {
			return false;
		}
		
		socket_connect($socket, $this->_host, $this->_port);

		socket_send($socket, $data, strlen($data), MSG_EOF);

		$msg = '';

		while (1) {
			$res = socket_read($socket, 10000);
			if($res === "" || !$res) {
				break;
			}
			$msg .= $res;
		}
		
		socket_close($socket);

		return $msg;
	}

	private function _mergeProxyData($data) {
		if(!isset($data['tags']['plugin_version'])) $data['tags']['plugin_version'] = (string)self::VERSION;

		$data = json_encode($data);

		return $data;
	}

	private function _checkArrayParams($data) {
		if($this->_checkTags($data) && $this->_checkInputs($data) && $this->_checkResults($data)) {
			return true;
		}
		return false;
	}

	private function _checkTags($data) {
		if(isset($data['tags'])) {
			return true;
		}
		return false;
	}

	private function _checkInputs($data) {
		if(isset($data['inputs'])) {
			return true;
		}
		return false;
	}

	private function _checkResults($data) {
		if(isset($data['returnKeys'])) {
			return true;
		}
		return false;
	}
}