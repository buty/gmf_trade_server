<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Exception;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	protected function _throwMsg($msg, $code) {
		throw new Exception($msg, $code);
		
	}

	protected function _apiOutPut($code, $msg, $data = array(), $http_code = 200) {

		if(!$msg) $msg = 'ok';
		$json_data = ['code' => (int) $code, 'msg' => (string) $msg, 'data' => (array) $data];
		return app('Illuminate\Routing\ResponseFactory')->json($json_data, $http_code, array('Content-Type' => 'application/json; charset=UTF-8'), JSON_UNESCAPED_UNICODE);
	}

}
