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
				<div class="panel-heading">交易(功能号:300 委托股票;301 委托价格; 302 委托确认）</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
						<!--1:上海； 2:深圳-->
						<input type="hidden" name="exchange_type" id="exchange_type" value="1">

						<div class="form-group">
							<label class="col-md-4 control-label">类型</label>
							<div class="col-md-6">
								<select class="form-control" id="trade_type">
									<option value="1">普通交易</option>
									<option value="2">条件交易</option>
									<option value="3">竞价交易</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">代码</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="stock_code" name="stock_code" value="{{ old('stock_code') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">价格</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="stock_price" name="stock_price" value="{{ old('stock_price') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">数量</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="stock_num" id="stock_num" value="{{ old('stock_num') }}">
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
				<div class="panel-heading">心跳包(功能号:100 系统登录) <a href="javascript:;" id="sendHeartBeating">发送心跳包</a></div>
				    <table class="table table-bordered">
					    <thead>
				          <tr>
				            <th>当前交易日期</th>
				            <th>系统状态</th>
				            <th>系统当前日期</th>
				          </tr>
				        </thead>
				        <tbody id="systemInfo">
				          <tr>
				            <td colspan="3">...</td>
				          </tr>
				        </tbody>
				  	</table>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">用户登录(功能号:200 客户登录校验) <a href="javascript:;" id="userLogin">点击登录</a></div>
				    <table class="table table-bordered">
					    <thead>
				          <tr>
				            <th>客户号</th>
				            <th>客户姓名</th>
				            <th>币种类别[0:人民币; 1:美元; 2:港币]</th>
				            <th>可用金额</th>
				            <th>当前余额</th>
				            <th>营业部号</th>
				            <th>公司名称</th>
				          </tr>
				        </thead>
				        <tbody id="userLoginInfo">
				          <tr>
				            <td colspan="7">...</td>
				          </tr>
				        </tbody>
				  	</table>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">股东信息(功能号:407 股东信息) <a href="javascript:;" id="queryStockUser">查询股东信息</a></div>
				    <table class="table table-bordered">
					    <thead>
				          <tr>
				            <th>交易类别</th>
				            <th>证券帐户</th>
				            <th>股东状态[‘0’为正常，其他为不正常]</th>
				            <th>股东权限[股东权限 '0': 自动配股，'1':  自动配售 '2': 红利领取 'P': 代理配售申购 'D': 代理缴款 'G': 代理转配 'H': 代理转让 'I': 代理转转 'K': 代理申购 'n': ETF申购 'r': 买断回购 'g': 权证交易]</th>
				            <th>主副标志[为‘1’为主帐号]</th>
				            <th>可卖数量</th>
				          </tr>
				        </thead>
				        <tbody id="stockUser">
				          <tr>
				            <td colspan="7">...</td>
				          </tr>
				        </tbody>
				  	</table>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">交易(功能号:403 查询股票) <a href="javascript:;" id="queryTradeList">查询股东信息</a></div>
				
				    <table class="table table-bordered">
			        <thead>
			          <tr>
			            <th>操作</th>
			            <th>代码</th>
			            <th>名称</th>
			            <th>持有数量</th>
			            <th>可卖数量</th>
			            <th>市值</th>
			            <th>成本价</th>
			            <th>盈亏金额</th>
			            <th>今日盈亏</th>
			            <th>盈亏比例</th>
			          </tr>
			        </thead>
			        <tbody id="stockTrade">
			          	<tr>
			            	<td colspan="10">...</td>
			          	</tr>
			        </tbody>
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">订单记录(功能号:401 查询委托;304 委托撤单) <a href="javascript:;" id="queryOrderList">查询股东信息</a></div>
				
				    <table class="table table-bordered">
			        <thead>
			          <tr>
			            <th>方向</th>
			            <th>代码</th>
			            <th>名称</th>
			            <th>订单价格</th>
			            <th>订单数量</th>
			            <th>交易状态</th>
			            <th>已成交@均价</th>
			            <th>操作</th>
			            <th>下单时间</th>
			          </tr>
			        </thead>
			        <tbody id="stockOrder">
			          <tr>
			            <td colspan="9">...</td>
			          </tr>
			        </tbody>
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">可撤单查询(功能号:303 可撤单查询) <a href="javascript:;" id="queryStockCanCancelOrder">查看撤单</a></div>
				
				    <table class="table table-bordered">
			        <thead>
			          <tr>
			            <th>方向</th>
			            <th>代码</th>
			            <th>名称</th>
			            <th>订单价格</th>
			            <th>订单数量</th>
			            <th>委托状态</th>
			            <th>成交数量</th>
			            <th>操作</th>
			            <th>委托号</th>
			          </tr>
			        </thead>
			        <tbody id="stockCanCancelOrder">
			          <tr>
			            <td colspan="9">...</td>
			          </tr>
			        </tbody>
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">成交(功能号:402 查询成交) <a href="javascript:;" id="queryDealList">查看成交</a></div>
				
				    <table class="table table-bordered">
			        <thead>
			          <tr>
			            <th>日期</th>
			            <th>交易名称</th>
			            <th>股票代码</th>
			            <th>股票名称</th>
			            <th>买卖方向</th>
			            <th>成交价格</th>
			            <th>成交数量</th>
			            <th>成交状态</th>
			            <th>成交类别</th>
			            <th>成交笔数</th>
			            <th>合同号</th>
			            <th>申报号</th>
			            <th>成交金额</th>
			            <th>成交编号</th>
			          </tr>
			        </thead>
			        <tbody id="dealList">
			          <tr>
			            <td colspan="14">...</td>
			          </tr>
			        </tbody>
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">资金(功能号:405 查询资金) <a href="javascript:;" id="queryUserMoney">查询资金</a></div>
				    <table class="table table-bordered" id="userMoney">
				  	</table>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">客户信息(功能号:415 客户信息) <a href="javascript:;" id="queryUserinfo">客户信息</a></div>
				    <table class="table table-bordered" id="userInfo">
				  	</table>
				
			</div>
		</div>
	</div>



</div>
<script language="JavaScript" src="{{ URL::asset('/') }}js/number.js"></script>
<script language="JavaScript" src="{{ URL::asset('/') }}js/trade.js"></script>
@endsection

