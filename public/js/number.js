
	  $(function() {
	    $( "#stock_price" ).spinner({
	      step: 0.01,
	      numberFormat: "n",
	      min: 0.01
	    });
	    $("#stock_num").spinner({
	    	step: 100,
	    	min: 100
	    });
	  });
