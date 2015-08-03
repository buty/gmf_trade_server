<?php namespace App\Libs\RsyncOrder;

class DispatchOrder {

	private $_order = '';

	public function __construct($target) {
		$target = "App\\Libs\\RsyncOrder\\Order" . $target;
		$this->_order = new $target;
		if(class_exists($target)) {
			$this->_order = new $target;
			
		}
	}

	public function handle() {
		if(method_exists($this->_order, 'handle')) {
			return $this->_order->handle();
		} else {
			return false;
		}
	}

}