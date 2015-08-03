<?php namespace App\Common;

trait CommonConstantTrait {

	public function getStockType() {
		return ['HK', 'SZ', 'SH'];
	}

	public function getStockSource() {
		return ['HK', 'A'];
	}


}