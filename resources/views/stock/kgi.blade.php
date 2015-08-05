@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">概述</div>
				<div class="panel-body">
					<div class="jumbotron">
					  <h1>13个交易接口测试</h1>
					  <p>...</p>
					  <p><a class="btn btn-primary btn-lg" href="#" role="button">Go On</a></p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">交易(目前只能交易0008）</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">类型</label>
							<div class="col-md-6">
								<select class="form-control" id="trade_type">
									<option value="1">普通交易</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">代码</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="stock_code" name="stock_code" value="0008">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">价格</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="stock_price" name="stock_price" value="4.5">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">数量</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="stock_num" id="stock_num" value="1000">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">&nbsp;</label>
							<div class="col-md-6">
								<h6>最大可买 <span class="text-primary" id="max_buy">1000</span></h6>
								<h6>最大可卖 <span class="text-warning" id="max_sell">0</span></h6>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-3">
								<button type="button" id="buttonBuy" class="btn btn-primary">买入</button>
								<button type="button" id="buttonSell" class="btn btn-warning">卖出</button>		
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">订单记录 <a href="javascript:;" id="queryOrderList">订单记录</a></div>
				
				    <table class="table table-bordered">
			        <thead>
			          <tr>
			            <th>cl_order_id</th>
			            <th>orig_cl_order_id</th>
			            <th>symbol</th>
			            <th>price</th>
			            <th>order_qty</th>
			            <th>side</th>
			            <th>order_status</th>
						  <th>text</th>
						  <th>exchange</th>
						  <th>transact_time</th>
						  <th>created_at</th>
						  <th>updated_at</th>
			            <th>operation</th>
			          </tr>
			        </thead>
			        <tbody id="stockOrder">
			          <tr>
			            <td colspan="13">...</td>
			          </tr>
			        </tbody>
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">撤单查询 <a href="javascript:;" id="queryCancelOrder">查看撤单</a></div>

				<table class="table table-bordered">
					<thead>
					<tr>
						<th>cl_order_id</th>
						<th>orig_cl_order_id</th>
						<th>symbol</th>
						<th>price</th>
						<th>order_qty</th>
						<th>side</th>
						<th>order_status</th>
						<th>text</th>
						<th>exchange</th>
						<th>transact_time</th>
						<th>created_at</th>
						<th>updated_at</th>
						<th>operation</th>
					</tr>
					</thead>
					<tbody id="queryCancelOrderTr">
					<tr>
						<td colspan="13">...</td>
					</tr>
					</tbody>
				</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">修改单查询 <a href="javascript:;" id="queryModifyList">修改单查询 </a></div>

				<table class="table table-bordered">
					<thead>
					<tr>
						<th>cl_order_id</th>
						<th>orig_cl_order_id</th>
						<th>symbol</th>
						<th>price</th>
						<th>order_qty</th>
						<th>side</th>
						<th>order_status</th>
						<th>text</th>
						<th>exchange</th>
						<th>transact_time</th>
						<th>created_at</th>
						<th>updated_at</th>
						<th>operation</th>
					</tr>
					</thead>
					<tbody id="queryModifyListTr">
					<tr>
						<td colspan="13">...</td>
					</tr>
					</tbody>
				</table>

			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">成交 <a href="javascript:;" id="queryDealList">查看成交</a></div>

				<table class="table table-bordered">
					<thead>
					<tr>
						<th>cl_order_id</th>
						<th>orig_cl_order_id</th>
						<th>symbol</th>
						<th>price</th>
						<th>order_qty</th>
						<th>side</th>
						<th>order_status</th>
						<th>text</th>
						<th>exchange</th>
						<th>transact_time</th>
						<th>created_at</th>
						<th>updated_at</th>
						<th>operation</th>
					</tr>
					</thead>
					<tbody id="queryDealListTr">
					<tr>
						<td colspan="13">...</td>
					</tr>
					</tbody>
				</table>
				
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="panel-heading" id="modify_titel">修改订单</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
						<div class="form-group">
							<label class="col-md-4 control-label">代码</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="m_stock_code" name="stock_code" value="">
								<input type="hidden" class="form-control" id="m_order_id" name="order_id" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">价格</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="m_stock_price" name="stock_price" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">数量</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="stock_num" id="m_stock_num" value="">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-3">
								<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
								<button type="button" id="buttonModify" class="btn btn-primary">修改</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>




</div>
<script language="JavaScript" src="{{ URL::asset('/') }}js/number.js"></script>
<script language="JavaScript" src="{{ URL::asset('/') }}js/kgi_trade.js"></script>
@endsection

