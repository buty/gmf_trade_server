$(function() {

	$("#buttonBuy,#buttonSell").click(function() {
		trade_type = $("#trade_type").val();
		stock_price= $("#stock_price").val();
		stock_code = $("#stock_code").val().substr(0, 6);
		stock_num  = $("#stock_num").val();
		_token = $("#_token").val();
		entrust_bs = $(this).attr('id') == 'buttonBuy' ? 1 : 2;
		$.post("/kgi/confirm-entrust", {trade_type:trade_type, entrust_price:stock_price, stock_code:stock_code, entrust_amount:stock_num, _token:_token, entrust_bs:entrust_bs}, function(data) {
			console.log(data);
			//下单后的逻辑
			if(data['error_no'] == 0) {
				getOrderList();
				alert('委托成功');
			} else {
				alert(data['error_info']);
			}
		});

		
	});


	$("#queryOrderList").click(function() {
		getOrderList();
	});
	$("#queryCancelOrder").click(function() {
		queryCancelOrder();
	});
	$("#queryDealList").click(function() {
		queryDealList();
	});
	$("#queryModifyList").click(function() {
		queryModifyList();
	});
});


function queryCancelOrder() {
	$.getJSON('/kgi/query-cancel-order', {}, function(data) {
		if(data.length) {
			$("#queryCancelOrderTr").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['cl_order_id']+"</td>";
				list += "<td>"+data[i]['orig_cl_order_id']+"</td>";
				list += "<td>"+data[i]['symbol']+"</td>";
				list += "<td>"+data[i]['price']+"</td>";
				list += "<td>"+data[i]['order_qty']+"</td>";
				list += "<td>"+getOrderSideName(data[i]['side'])+"</td>";
				//list += "<td>"+data[i]['order_status']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['order_status'])+"</td>";
				list += "<td>"+data[i]['text']+"</td>";
				list += "<td>"+data[i]['exchange']+"</td>";
				list += "<td>"+data[i]['transact_time']+"</td>";
				list += "<td>"+data[i]['created_at']+"</td>";
				list += "<td>"+data[i]['updated_at']+"</td>";
				list += "<td>-</td>";
				list += '</tr>';
				$("#queryCancelOrderTr").append(list);
			}
		} else  {
			$("#queryCancelOrderTr").children().first().children().html('暂无数据');
		}
	});
}

function queryDealList() {
	$.get("/kgi/deal-list", {}, function(data) {
		if(data.length) {
			$("#queryDealListTr").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['cl_order_id']+"</td>";
				list += "<td>"+data[i]['orig_cl_order_id']+"</td>";
				list += "<td>"+data[i]['symbol']+"</td>";
				list += "<td>"+data[i]['price']+"</td>";
				list += "<td>"+data[i]['order_qty']+"</td>";
				list += "<td>"+getOrderSideName(data[i]['side'])+"</td>";
				//list += "<td>"+data[i]['order_status']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['order_status'])+"</td>";
				list += "<td>"+data[i]['text']+"</td>";
				list += "<td>"+data[i]['exchange']+"</td>";
				list += "<td>"+data[i]['transact_time']+"</td>";
				list += "<td>"+data[i]['created_at']+"</td>";
				list += "<td>"+data[i]['updated_at']+"</td>";
				list += "<td>-</td>";
				list += '</tr>';
				$("#queryDealListTr").append(list);
			}
		} else {
			$("#queryDealListTr").children().first().children().html('暂无数据');
		}
	});
}

function getOrderList() {
	$.get("/kgi/order-list", {}, function(data) {
		if(data.length) {
			$("#stockOrder").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['cl_order_id']+"</td>";
				list += "<td>"+data[i]['orig_cl_order_id']+"</td>";
				list += "<td>"+data[i]['symbol']+"</td>";
				list += "<td>"+data[i]['price']+"</td>";
				list += "<td>"+data[i]['order_qty']+"</td>";
				list += "<td>"+getOrderSideName(data[i]['side'])+"</td>";
				//list += "<td>"+data[i]['order_status']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['order_status'])+"</td>";
				list += "<td>"+data[i]['text']+"</td>";
				list += "<td>"+data[i]['exchange']+"</td>";
				list += "<td>"+data[i]['transact_time']+"</td>";
				list += "<td>"+data[i]['created_at']+"</td>";
				list += "<td>"+data[i]['updated_at']+"</td>";
				list += "<td>"+getOrderStatusDesc(data[i]['cl_order_id'], data[i]['order_status'], data[i]['symbol'], data[i]['price'], data[i]['order_qty'])+"</td>";
				list += '</tr>';
				$("#stockOrder").append(list);
			}
		} else {
			$("#stockOrder").children().first().children().html('暂无数据');
		}
	});
}

function queryModifyList() {
	$.get("/kgi/modify-list", {}, function(data) {
		if(data.length) {
			$("#queryModifyListTr").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['cl_order_id']+"</td>";
				list += "<td>"+data[i]['orig_cl_order_id']+"</td>";
				list += "<td>"+data[i]['symbol']+"</td>";
				list += "<td>"+data[i]['price']+"</td>";
				list += "<td>"+data[i]['order_qty']+"</td>";
				list += "<td>"+getOrderSideName(data[i]['side'])+"</td>";
				//list += "<td>"+data[i]['order_status']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['order_status'])+"</td>";
				list += "<td>"+data[i]['text']+"</td>";
				list += "<td>"+data[i]['exchange']+"</td>";
				list += "<td>"+data[i]['transact_time']+"</td>";
				list += "<td>"+data[i]['created_at']+"</td>";
				list += "<td>"+data[i]['updated_at']+"</td>";
				list += "<td>-</td>";
				list += '</tr>';
				$("#queryModifyListTr").append(list);
			}
		} else {
			$("#queryModifyListTr").children().first().children().html('暂无数据');
		}
	});
}

function getOrderStatusDesc(order_no, order_status, symbol, price, order_qty) {
	msg = '-';
	if(checkIfCanCancelOrder(order_status)) {
		//msg = "<a href=\"javascript:cancalOrder('"+order_no+"');\">撤单</a>" + "|" + "<a data-toggle='modal' data-target='#myModal'>修改</a>";
		msg = "<a href=\"javascript:cancalOrder('"+order_no+"');\">撤单</a>" + "|" +
			"<a href=\"javascript:showModifyModal('"+order_no+"','"+symbol+"','"+price+"','"+order_qty+"');\">修改</a>";
	}

	return msg;
}

function checkIfCanCancelOrder(order_status) {
	if(order_status == '0' || order_status == '2' || order_status == '6' || order_status == 'E') {
		return true;
	}
	return false;
}


function getOrderStatusName(order_status) {
	var status_data = {'0':'New','1':'Partially filled','2':'Filled','3':'Done for day','4':'Canceled','5':'Replaced','6':'Pending','8':'Rejected','E':'Pending Replace','D':"Creating"};

	return status_data[order_status];
}
function getOrderSideName(side) {
	var SideName = {1:"买", 2:"卖"};
	return SideName[side];
}

function queryDealList() {
	$.get("/kgi/deal-list", {}, function(data) {
		if(data.length) {
			$("#queryDealListTr").empty();
			for(var i in data) {
				var list = '<tr>';
				list += "<td>"+data[i]['cl_order_id']+"</td>";
				list += "<td>"+data[i]['orig_cl_order_id']+"</td>";
				list += "<td>"+data[i]['symbol']+"</td>";
				list += "<td>"+data[i]['price']+"</td>";
				list += "<td>"+data[i]['order_qty']+"</td>";
				list += "<td>"+getOrderSideName(data[i]['side'])+"</td>";
				//list += "<td>"+data[i]['order_status']+"</td>";
				list += "<td>"+getOrderStatusName(data[i]['order_status'])+"</td>";
				list += "<td>"+data[i]['text']+"</td>";
				list += "<td>"+data[i]['exchange']+"</td>";
				list += "<td>"+data[i]['transact_time']+"</td>";
				list += "<td>"+data[i]['created_at']+"</td>";
				list += "<td>"+data[i]['updated_at']+"</td>";
				list += "<td>-</td>";
				list += '</tr>';
				$("#queryDealListTr").append(list);
			}
		} else {
			$("#queryDealListTr").children().first().children().html('暂无数据');
		}
	});
}

function cancalOrder(order_no) {
	_token = $("#_token").val();
	$.post('/kgi/cancel-order', {cl_order_id:order_no, _token:_token}, function(data) {
		//下单后的逻辑
		if(data['error_no'] == 0) {
			//getOrderList();
			queryCancelOrder();
			alert('撤消委托成功');
		} else {
			alert(data['error_info']);
		}
	});
}

function showModifyModal(order_id, symbol, price, order_qty) {
	$('#myModal').modal({keyboard: false});
	$("#modify_titel").empty().append("修改订单：" + order_id);
	$("#m_stock_code").val(symbol);
	$("#m_stock_price").val(price);
	$("#m_stock_num").val(order_qty);
	$("#m_order_id").val(order_id);
}

$("#buttonModify").click(function() {
	stock_price= $("#m_stock_price").val();
	stock_num  = $("#m_stock_num").val();
	order_id  = $("#m_order_id").val();
	_token = $("#_token").val();
	$.post("/kgi/modify-order", {order_id:order_id, price:stock_price, order_qty:stock_num, _token:_token}, function(data) {
		//下单后的逻辑
		if(data['error_no'] == 0) {
			queryModifyList();
			alert('修改委托成功');
			$('#myModal').modal('hide');
		} else {
			alert(data['error_info']);
		}
	});


});