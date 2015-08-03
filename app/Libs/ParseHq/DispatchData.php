<?php namespace App\Libs\ParseHq;

class DispatchData {

	private $_handle = '';

	public function __construct($target) {
		$this->_target = $target;
		$targetClass = "App\\Libs\\ParseHq\\Parse" . $target . "Hq";
		$this->_handle = new $targetClass;
		if(class_exists($targetClass)) {
			$this->_handle = new $targetClass;
			
		}
	}

	public function handle() {
		if(method_exists($this->_handle, 'handle')) {
			return $this->_handle->handle($this->_target);
		} else {
			return false;
		}
	}
}