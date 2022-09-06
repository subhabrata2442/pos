$(document).ready(function() {
    $("#search_product").keyup(function() {
        var search = $(this).val();
        if (search != "") {
            $.ajax({
                url: prop.ajaxurl,
                type: 'post',
                data: {
                    search: search,
                    action: 'get_sell_product_search',
                    _token: prop.csrf_token
                },
                dataType: 'json',
                success: function(response) {
                    var len = response.result.length;

                    $("#product_search_result").empty();
                    for (var i = 0; i < len; i++) {
						//response.result[i]['product_barcode'] + '-' + 
                        var id		= response.result[i]['id'];
                        var name	= response.result[i]['product_name']+' '+ response.result[i]['product_size'];
                        $("#product_search_result").append("<li value='" + id + "'>" + name + "</li>");
                    }
                    // binding click event to li
                    $("#product_search_result li").bind("click", function() {
                        $('.loader_section').show();
                        setProductRow(this);
                    });
                }
            });
        }
    });
});

// Set Text to search box and get details
function setProductRow(element) {
    var value = $(element).text();
    var product_id = $(element).val();
	
    $("#search_product").val('');
    $("#product_search_result").empty();

    // Request User Details
    $.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            product_id: product_id,
            action: 'get_sell_product_byId',
            _token: prop.csrf_token
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == '1') {
				var html = '';
				var item_detail = response.product_result;
				
				var barcode = '';
				if (item_detail.product_barcode != null || item_detail.product_barcode != undefined) {
                    barcode = item_detail.product_barcode;
                }
				
				var product_name = '';
				if (item_detail.brand_name != null || item_detail.brand_name != undefined) {
                    product_name = item_detail.brand_name;
                }
				
				var product_id= item_detail.branch_stock_product_id;
				
				var item_prices_length= item_detail.item_prices.length;
				
				var w_stock=0;
				var c_stock=0;
				var product_mrp=0;
				if(item_prices_length>1){
					w_stock=item_detail.item_prices[0].w_qty;
					c_stock=item_detail.item_prices[0].c_qty;
					product_mrp=item_detail.item_prices[0].product_mrp;
				}else{
					w_stock=item_detail.item_prices[0].w_qty;
					c_stock=item_detail.item_prices[0].c_qty;
					product_mrp=item_detail.item_prices[0].product_mrp;
				}
				
				//alert(w_stock);
				
				var item_row = 0;
				if($('#product_sell_record_sec tr').length == 0){
					item_row++;
				}
				else{
					var max = 0;
					$("#product_sell_record_sec tr").each(function() {
						var value = parseInt($(this).data('id'));
						max = (value > max) ? value : max;
					});
					item_row = max + 1;
				}
				
				
				
				
				html += '<tr id="sell_product_' + product_id + '" data-id="' + item_row + '">' +
						'<input type="hidden" id="product_w_stock_' + product_id + '" value="' + w_stock + '">' +
						'<input type="hidden" id="product_c_stock_' + product_id + '" value="' + c_stock + '">' +
						'<td>'+barcode+'</td>'+
						'<td>'+product_name+'</td>'+
						'<td id="product_stock_'+product_id+'">W-'+w_stock+'</br>C-'+c_stock+'</td>'+
						'<td id="product_mrp_'+product_id+'">'+product_mrp+'</td>'+
						'<td><input type="number" id="product_qty_'+product_id+'" class="input-3" value="1"></td>'+
						'<td><input type="text" id="product_disc_percent_'+product_id+'" class="input-3" value="0"></td>'+
						'<td><input type="text" id="product_disc_amount_'+product_id+'" class="input-3" value="0"></td>'+
						'<td id="product_unit_price_'+product_id+'">'+product_mrp+'</td>'+
						'<td>665.00</td>'+
						'<td><a href="javascript:;" onclick="remove_sell_item(' + item_row + ');"><i class="fas fa-times"></i></a></td>'+
						'</tr>';
				
				$("#product_sell_record_sec").prepend(html);
				
				//alert(item_prices_length);
				
			}
        }
    });
}

function remove_sell_item(row) {
    $("#product_sell_record_sec").find("tr[data-id='" + row + "']").remove();
    /*var total_qty = $("#product_record_sec tr").length;
    $(".total_qty").html(total_qty);
	final_calculation();*/
}
$(document).on('keyup','.product_qty',function(){
	var stock_type	= 'w';
	var product_id	= $(this).attr('id').split('product_qty_')[1];
	var tbl_row		= $(this).closest('tr').data('id');
	var qty         = $('#product_qty_'+product_id).val();
	var w_stock     = $('#product_w_stock_'+product_id).val();
	var c_stock     = $('#product_c_stock_'+product_id).val();
	var stock		= 0;
	if(stock_type=='w'){
		stock=w_stock;
	}else{
		stock=c_stock;
	}
	if(Number(qty)>Number(stock)){
		toastr.error("Entered Qty should not be greater than Stock");
		return false;
	}else{
		var unit_price          = $("#product_unit_price_"+product_id).html(); 
        var discount_percent	= $("#product_disc_percent_"+product_id).val();
		var total_discount      = (Number(unit_price) * Number(qty)) * Number(discount_percent) / 100;
		
		if(Number(discount_percent) !=0 || discount_percent != ''){
			$("#product_disc_amount_"+product_id).val(total_discount.toFixed(2));
		}
		var totalsellingwgst	= Number(unit_price) * Number(qty);
		var discountedamt   	= Number(totalsellingwgst) - Number(total_discount);
		var total_amount		= Number(discountedamt)
		$("#product_total_amount_"+product_id).html(Number(total_amount).toFixed(2));
		
		
		
		
		
		console.log(total_discount);
        
	}
	
	
});

