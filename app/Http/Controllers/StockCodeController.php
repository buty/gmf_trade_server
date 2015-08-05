<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\StockCodeList;
use App\Common\CommonConstantTrait;

use Exception;

class StockCodeController extends Controller {

	use CommonConstantTrait;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getSearch(Request $request)
	{
		$code = 1; $msg = ''; $data = array();
		try {
			$stock_source = $request->input('stock_source');
			$stock_type = $request->input('stock_type');
			$code_num = $request->input('code_num');

			$this->_checkSearchParams($code_num, $stock_source, $stock_type);
			$data = $this->_searchData($code_num, $stock_source, $stock_type);
			
			$code = 0;
		} catch (Exception $e) {
			$msg  = $e->getMessage();
			$code = $e->getCode();
		}
		$callback = $request->input('callback');

		echo $callback . '(' . json_encode($data) . ')';die();
		return response()->json($data);
	}

	private function _searchData($code_num, $stock_source, $stock_type) {
		$obj = StockCodeList::where('code_num', 'like', '%'.$code_num.'%');
			
		if($stock_type){
			$obj = $obj->where('stock_type', $stock_type);
		}
		if($stock_source) {
			$obj = $obj->where('stock_source', $stock_source);
		}
		$data = $obj->select('code_num', 'stock_name', 'stock_type')
			->limit(5)->get();

		$res = [];
		if($data) {
			foreach ($data->toArray() as $k => $v) {
				$res[] = $v['code_num'] . ' ' . $v['stock_type'] . ' ' . $v['stock_name'];
			}
		}

		return $res;
	}

	private function _checkSearchParams($code_num, $stock_source, $stock_type)
	{
		// if(!in_array($stock_source, $this->getStockSource())) {
		// 	$this->_throwMsg('params stock_source is error', 20002);
		// }

		// if(!in_array($stock_type, $this->getStockType())) {
		// 	$this->_throwMsg('params stock_type is error', 20002);
		// }

		// if(!$code_num) {
		// 	$this->_throwMsg('params code_num empty', 20001);
		// }

	}

}
