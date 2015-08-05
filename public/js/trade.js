$(function() {

    $( "#stock_code" ).autocomplete({
      source: function( request, response ) {

        $.ajax({
          url: "/stockcode/search",
          dataType: "jsonp",
          data: {
            code_num: request.term,
          },
          success: function( data ) {
            response( data );
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {
      	$("#stock_code").val( this.value);
      },
      open: function() {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });


	$("#buttonBuy,#buttonSell").click(function() {
		trade_type = $("#trade_type").val();
		stock_price= $("#stock_price").val();
		stock_code = $("#stock_code").val().substr(0, 6);
		stock_num  = $("#stock_num").val();
		_token = $("#_token").val();
		exchange_type = $("#exchange_type").val();
		entrust_bs = $(this).attr('id') == 'buttonBuy' ? 1 : 2;

		$.post("/stock/confirm-entrust", {trade_type:trade_type, entrust_price:stock_price, stock_code:stock_code, entrust_amount:stock_num, _token:_token, entrust_bs:entrust_bs, exchange_type:exchange_type}, function(data) {
			//下单后的逻辑
			if(data['error_no'] == 0) {
				getTradeList();
				getOrderList();
				alert('委托成功');
			} else {
				alert(data['error_info']);
			}
		});

		
	});

	$("#stock_code").blur(function() {
		stock_code = $("#stock_code").val().substr(0, 6);
		exchange_type = $("#exchange_type").val();
		$.getJSON("/stock/code-info", {stock_code:stock_code, exchange_type:exchange_type}, function(data) {
			if(data['error_no'] == '0') {
				$("#stock_price").val(data['last_price']);
				$("#stock_num").val(data['low_amount']);
				$("#max_sell").html(data['enable_amount']);
				getMaxBuyAmount(exchange_type, data['last_price'], stock_code);
			} else {
				alert(data['error_info']);
			}
		});
	});

	// if($("#stock_code").val()) {
	// 	$("#stock_code").blur();
	// }
	
	

	$("#queryDealList").click(function() {
		queryDealList();
	});
	$("#queryStockCanCancelOrder").click(function() {
		getCanCancelOrder();
	});
	$("#queryUserMoney").click(function() {
		queryFund();
	});
	$("#queryUserinfo").click(function() {
		queryUserinfo();
	});
	$("#queryStockUser").click(function() {
		queryStockUser();
	});
	$("#sendHeartBeating").click(function() {
		sendBeating();
	});
	$("#userLogin").click(function() {
		userLogin();
	});
	$("#queryTradeList").click(function() {
		getTradeList();
	});
	$("#queryOrderList").click(function() {
		getOrderList();
	});
	//setTimeout('initPage2()', 3000);
	//alert($("#stockTrade").children().first().html());
});
	// setInterval('getTradeList()', 5000);
	// setInterval('getOrderList()', 3000);


function getMaxBuyAmount(exchange_type, entrust_price, stock_code) {
	$.getJSON("/stock/entrust-price", {exchange_type:exchange_type, entrust_price:entrust_price, stock_code:stock_code}, function(data) {
		$("#max_buy").html(parseInt(data['enable_amount']));
	});
}

function getCanCancelOrder() {
	$.getJSON('/stock/query-can-cancel-order', {}, function(data) {
		if(data.length) {
			$("#stockCanCancelOrder").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+((data[i]['bs_name'] == 1) ? '买入' : '卖出')+"</td>";
				list += "<td>"+data[i]['stock_code']+"</td>";
				list += "<td>"+data[i]['stock_name']+"</td>";
				list += "<td>"+data[i]['entrust_price']+"</td>";
				list += "<td>"+data[i]['entrust_amount']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['entrust_status'])+"</td>";
				list += "<td>"+data[i]['business_amount']+"</td>";
				list += "<td>"+getOrderStatusDesc(data[i]['entrust_no'], data[i]['entrust_status'])+"</td>";
				list += "<td>"+data[i]['entrust_no']+"</td>";
				list += '</tr>';
				$("#stockCanCancelOrder").append(list);
			}
		} else  {
			$("#stockCanCancelOrder").children().first().children().html('暂无数据');
		}
	});
}

function getTradeList() {
	$.get("/stock/trade-list", {}, function(data) {
		if(data.length) {
			$("#stockTrade").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>买卖</td>";
				list += "<td>"+data[i]['stock_code']+"</td>";
				list += "<td>"+data[i]['stock_name']+"</td>";
				list += "<td>"+data[i]['current_amount']+"</td>";
				list += "<td>"+data[i]['enable_amount']+"</td>";
				list += "<td>"+data[i]['market_value']+"</td>";
				list += "<td>"+data[i]['cost_price']+"</td>";
				list += "<td>"+data[i]['income_balance']+"</td>";
				list += "<td>"+data[i]['today_income_balance']+"</td>";
				profit_loss_ratio = ((data[i]['last_price'] - data[i]['cost_price']) / data[i]['cost_price']).toFixed(5) * 100 + '%';
				list += "<td>"+profit_loss_ratio+"</td>";
				list += '</tr>';
				$("#stockTrade").append(list);
			}
		} else {
			$("#stockTrade").children().first().children().html('暂无数据');
		}
	});
}

function getOrderList() {
	$.get("/stock/order-list", {}, function(data) {
		if(data.length) {
			$("#stockOrder").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['bs_name']+"</td>";
				list += "<td>"+data[i]['stock_code']+"</td>";
				list += "<td>"+data[i]['stock_name']+"</td>";
				list += "<td>"+data[i]['entrust_price']+"</td>";
				list += "<td>"+data[i]['entrust_amount']+"</td>";
				list += "<td>"+data[i]['status_name']+"</td>";
				list += "<td>"+data[i]['business_amount']+"@"+data[i]['business_price']+"</td>";
				list += "<td>"+getOrderStatusDesc(data[i]['entrust_no'], data[i]['entrust_status'])+"</td>";
				list += "<td>"+data[i]['entrust_date']+data[i]['entrust_time']+"</td>";
				list += '</tr>';
				$("#stockOrder").append(list);
			}
		} else {
			$("#stockOrder").children().first().children().html('暂无数据');
		}
	});
}

function getOrderStatusDesc(order_no, order_status) {
	msg = '-';
	if(checkIfCanCancelOrder(order_status)) {
		msg = "<a href=\"javascript:cancalOrder('"+order_no+"');\">撤单</a>";
	}

	return msg;
}

function checkIfCanCancelOrder(order_status) {
	if(order_status == '0' || order_status == '2' || order_status == '7') {
		return true;
	}
	return false;
}

function cancalOrder(order_no) {
	_token = $("#_token").val();
	$.post('/stock/cancel-order', {entrust_no:order_no, _token:_token}, function(data) {
		//下单后的逻辑
		if(data['entrust_no']) {
			getOrderList();
			alert('撤消委托成功');
		} else {
			alert(data['error_info']);
		}
	});
}

function getOrderStatusName(order_status) {
	var status_data = {'0':'未报','1':'待报','2':'已报','3':'已报持撤','4':'部分待撤','5':'部撤','6':'已撤','7':'部成','8':'已成','9':'废单'};

	return status_data[order_status];
}

function queryDealList() {
	$.get("/stock/deal-list", {}, function(data) {
		if(data.length) {
			$("#dealList").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['date']+data[i]['business_time']+"</td>";
				list += "<td>"+data[i]['exchange_name']+"</td>";
				list += "<td>"+data[i]['stock_code']+"</td>";
				list += "<td>"+data[i]['stock_name']+"</td>";
				list += "<td>"+data[i]['bs_name']+"</td>";
				list += "<td>"+data[i]['business_price']+"</td>";
				list += "<td>"+data[i]['business_amount']+"</td>";
				list += "<td>"+data[i]['business_status']+"</td>";
				list += "<td>"+data[i]['business_type']+"</td>";
				list += "<td>"+data[i]['business_times']+"</td>";
				list += "<td>"+data[i]['entrust_no']+"</td>";
				list += "<td>"+data[i]['report_no']+"</td>";
				list += "<td>"+data[i]['business_balance']+"</td>";
				list += "<td>"+data[i]['business_no']+"</td>";
				$("#dealList").append(list);
			}
		} else {
			$("#dealList").children().first().children().html('暂无数据');
		}
	});
}

function queryFund() {
	$.get("/stock/query-fund", {}, function(data) {
		$("#userMoney").html(data);
	});
}

function queryUserinfo() {
	$.get("/stock/query-userinfo", {}, function(data) {
		$("#userInfo").html(data);
	});
}

function queryStockUser() {
	$.get("/stock/query-stock-user", {}, function(data) {
		if(data.length) {
			$("#stockUser").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['exchange_name']+"</td>";
				list += "<td>"+data[i]['stock_account']+"</td>";
				list += "<td>"+data[i]['holder_status']+"</td>";
				list += "<td>"+data[i]['holder_rights']+"</td>";
				list += "<td>"+data[i]['main_flag']+"</td>";
				list += "<td>"+data[i]['enable_amount']+"</td>";
				$("#stockUser").append(list);
			}
		} else  {
			$("#stockUser").children().first().children().html('暂无数据');
		}
	});
}

function sendBeating() {
	$.get("/stock/query-system-info", {}, function(data) {
		$("#systemInfo").empty();
		var list = '<tr>';
		list += "<td>"+data['init_date']+"</td>";
		list += "<td>"+data['sys_status_name']+"</td>";
		list += "<td>"+data['curr_date']+"</td>";

		$("#systemInfo").append(list);
	});
}
function userLogin() {
	_token = $("#_token").val();
	$.post("/stock/user-login", {_token:_token}, function(data) {
		$("#userLoginInfo").empty();
		var list = '<tr>';
		list += "<td>"+data['client_id']+"</td>";
		list += "<td>"+data['client_name']+"</td>";
		list += "<td>"+data['money_type']+"</td>";
		list += "<td>"+data['enable_balance']+"</td>";
		list += "<td>"+data['current_balance']+"</td>";
		list += "<td>"+data['branch_no']+"</td>";
		list += "<td>"+data['company_name']+"</td>";
		$("#userLoginInfo").append(list);
	});
}