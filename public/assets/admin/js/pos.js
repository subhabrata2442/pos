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
                        var id = response.result[i]['id'];
                        var name = response.result[i]['product_name'] + ' ' + response.result[i]['product_size'];
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

                var product_id = item_detail.branch_stock_product_id;

                var item_prices_length = item_detail.item_prices.length;

                var w_stock = 0;
                var c_stock = 0;
                var product_mrp = 0;
				
				var option_html='';
				
                if (item_prices_length > 1) {
                    w_stock = item_detail.item_prices[0].w_qty;
                    c_stock = item_detail.item_prices[0].c_qty;
                    product_mrp = item_detail.item_prices[0].product_mrp;
					
					for(var p=0;item_detail.item_prices.length>p;p++){
						option_html +='<option value="'+item_detail.item_prices[p]+'">'+item_detail.item_prices[p]+'</option>';
						option_html +='<option value="'+item_detail.item_prices[p]+'">'+item_detail.item_prices[p]+'</option>';
					}	
                } else {
                    w_stock = item_detail.item_prices[0].w_qty;
                    c_stock = item_detail.item_prices[0].c_qty;
                    product_mrp = item_detail.item_prices[0].product_mrp;
					option_html +='<option value="'+product_mrp+'">'+product_mrp+'</option>';
					
                }

                //alert(w_stock);

                var item_row = 0;
                if ($('#product_sell_record_sec tr').length == 0) {
                    item_row++;
                } else {
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
                    '<td>' + barcode + '</td>' +
                    '<td class="proName">' + product_name + '</td>' +
                    '<td id="product_stock_' + product_id + '">W-' + w_stock + '</br>S-' + c_stock + '</td>' +
                    '<td id="product_mrp_' + product_id + '">' + product_mrp + '</td>' +
                    '<td><input type="number" id="product_qty_' + product_id + '" class="input-3 product_qty" value="1"></td>' +
                    '<td><input type="text" id="product_disc_percent_' + product_id + '" class="input-3 product_disc_percent" value="0"></td>' +
                    '<td><input type="text" id="product_disc_amount_' + product_id + '" class="input-3 product_disc_amount" value="0"></td>' +
                    '<td class="relative"><select id="product_unit_price_'+product_id+'" class="product_unit_price">'+option_html+'</select>'+
					'<input type="text" class="product_unit_price_amount input-3" id="product_unit_price_amount_'+product_id+'" value="'+product_mrp+'" readonly="readonly"></td>' +
                    '<td id="product_total_amount_'+product_id+'">'+product_mrp+'</td>' +
                    '<td><a href="javascript:;" onclick="remove_sell_item(' + item_row + ');"><i class="fas fa-times-circle"></i></a></td>' +
                    '</tr>';

                $("#product_sell_record_sec").prepend(html);
				total_cal();

                //alert(item_prices_length);

            }else{
				toastr.error("Stock not available for this Product");
				
			}
        }
    });
}

function remove_sell_item(row) {
    $("#product_sell_record_sec").find("tr[data-id='" + row + "']").remove();
	total_cal();
    /*var total_qty = $("#product_record_sec tr").length;
    $(".total_qty").html(total_qty);
	final_calculation();*/
}

$(document).on('keyup', '.product_qty', function() {
    var product_id = $(this).attr('id').split('product_qty_')[1];
    var tbl_row = $(this).closest('tr').data('id');
    var qty = $('#product_qty_' + product_id).val();
    var w_stock = $('#product_w_stock_' + product_id).val();
    var c_stock = $('#product_c_stock_' + product_id).val();
    var stock = 0;
    if (stock_type == 'w') {
        stock = w_stock;
    } else {
        stock = c_stock;
    }
    if (Number(qty) > Number(stock)) {
        $('#product_qty_' + product_id).val(stock);
        toastr.error("Entered Qty should not be greater than Stock");
        return false;
    } else {
        var unit_price = $("#product_unit_price_amount_" + product_id).val();
        var discount_percent = $("#product_disc_percent_" + product_id).val();
        var total_discount = (Number(unit_price) * Number(qty)) * Number(discount_percent) / 100;

        if (Number(discount_percent) != 0 || discount_percent != '') {
            $("#product_disc_amount_" + product_id).val(total_discount.toFixed(2));
        }
        var total_selling_cost = Number(unit_price) * Number(qty);
        var discount_amount = Number(total_selling_cost) - Number(total_discount);
        var total_amount = Number(discount_amount)
        $("#product_total_amount_" + product_id).html(Number(total_amount).toFixed(2));
    }
	
	total_cal();
});


$(document).on('keyup', '.product_disc_percent', function() {
    var product_id = $(this).attr('id').split('product_disc_percent_')[1];

    var tbl_row = $(this).closest('tr').data('id');
    var qty = $('#product_qty_' + product_id).val();
    var w_stock = $('#product_w_stock_' + product_id).val();
    var c_stock = $('#product_c_stock_' + product_id).val();
    var stock = 0;
    if (stock_type == 'w') {
        stock = w_stock;
    } else {
        stock = c_stock;
    }
    if (Number(qty) > Number(stock)) {
        toastr.error("Entered Qty should not be greater than Stock");
        return false;
    } else {
        var unit_price = $("#product_unit_price_amount_" + product_id).val();
        var discount_percent = $("#product_disc_percent_" + product_id).val();

        if (Number(discount_percent) > 100) {
            toastr.error("Discount Cannot be greater than Product MRP");
            $("#product_disc_percent_" + product_id).val(0);
            discount_cal(product_id);
        } else {
            var total_discount = (Number(unit_price) * Number(qty)) * Number(discount_percent) / 100;

            if (Number(discount_percent) != 0 || discount_percent != '') {
                $("#product_disc_amount_" + product_id).val(total_discount.toFixed(2));
            }
            var total_selling_cost = Number(unit_price) * Number(qty);
            var discount_amount = Number(total_selling_cost) - Number(total_discount);
            var total_amount = Number(discount_amount)
            $("#product_total_amount_" + product_id).html(Number(total_amount).toFixed(2));
        }
    }
	
	total_cal();
});

$(document).on('keyup', '.product_disc_amount', function() {
    var product_id = $(this).attr('id').split('product_disc_amount_')[1];
    var tbl_row = $(this).closest('tr').data('id');
    var qty = $('#product_qty_' + product_id).val();
    var w_stock = $('#product_w_stock_' + product_id).val();
    var c_stock = $('#product_c_stock_' + product_id).val();
    var product_mrp = $('#product_unit_price_' + product_id).val();
    var disc_amount = $('#product_disc_amount_' + product_id).val();
    var unit_price = $("#product_unit_price_amount_" + product_id).val();

    //console.log(product_mrp);

    var stock = 0;
    if (stock_type == 'w') {
        stock = w_stock;
    } else {
        stock = c_stock;
    }
    if (Number(qty) > Number(stock)) {
        toastr.error("Entered Qty should not be greater than Stock");
        return false;
    } else {
        var discount_percent = (Number(disc_amount) * 100) / (Number(unit_price) * Number(qty));

        if (Number(discount_percent) > 100) {
            toastr.error("Discount Cannot be greater than Product MRP");
            $("#product_disc_percent_" + product_id).val(0);
            discount_cal(product_id);
        } else {
            $("#product_disc_percent_" + product_id).val(Number(discount_percent).toFixed(2));
            var total_discount = disc_amount;
            var total_selling_cost = Number(unit_price) * Number(qty);
            var discount_amount = Number(total_selling_cost) - Number(total_discount);
            var total_amount = Number(discount_amount)
            $("#product_total_amount_" + product_id).html(Number(total_amount).toFixed(2));
        }
    }
	
	total_cal();
});

$(document).on('change', '.product_unit_price', function() {
    var product_id = $(this).attr('id').split('product_unit_price_')[1];
    var tbl_row = $(this).closest('tr').data('id');
    var qty = $('#product_qty_' + product_id).val();
    var w_stock = $('#product_w_stock_' + product_id).val();
    var c_stock = $('#product_c_stock_' + product_id).val();
    var stock = 0;
    if (stock_type == 'w') {
        stock = w_stock;
    } else {
        stock = c_stock;
    }

    var product_unit_price = $('#product_unit_price_' + product_id).val();
    $("#product_unit_price_amount_" + product_id).val(Number(product_unit_price));

    if (Number(qty) > Number(stock)) {
        toastr.error("Entered Qty should not be greater than Stock");
        return false;
    } else {
        setTimeout(function() {
            var unit_price = $("#product_unit_price_amount_" + product_id).val();
            var discount_percent = $("#product_disc_percent_" + product_id).val();
            var total_discount = (Number(unit_price) * Number(qty)) * Number(discount_percent) / 100;

            if (Number(discount_percent) != 0 || discount_percent != '') {
                $("#product_disc_amount_" + product_id).val(total_discount.toFixed(2));
            }
            var total_selling_cost = Number(unit_price) * Number(qty);
            var discount_amount = Number(total_selling_cost) - Number(total_discount);
            var total_amount = Number(discount_amount)
            $("#product_total_amount_" + product_id).html(Number(total_amount).toFixed(2));
			total_cal();
        }, 500);
    }
});

function discount_cal(product_id) {
    var qty = $('#product_qty_' + product_id).val();
    var w_stock = $('#product_w_stock_' + product_id).val();
    var c_stock = $('#product_c_stock_' + product_id).val();
    var stock = 0;

    if (stock_type == 'w') {
        stock = w_stock;
    } else {
        stock = c_stock;
    }

    var product_unit_price = $('#product_unit_price_' + product_id).val();
    $("#product_unit_price_amount_" + product_id).val(Number(product_unit_price));

    if (Number(qty) > Number(stock)) {
        toastr.error("Entered Qty should not be greater than Stock");
        return false;
    } else {
        setTimeout(function() {
            var unit_price = $("#product_unit_price_amount_" + product_id).val();
            var discount_percent = $("#product_disc_percent_" + product_id).val();
            var total_discount = (Number(unit_price) * Number(qty)) * Number(discount_percent) / 100;

            if (Number(discount_percent) != 0 || discount_percent != '') {
                $("#product_disc_amount_" + product_id).val(total_discount.toFixed(2));
            }
            var total_selling_cost = Number(unit_price) * Number(qty);
            var discount_amount = Number(total_selling_cost) - Number(total_discount);
            var total_amount = Number(discount_amount)
            $("#product_total_amount_" + product_id).html(Number(total_amount).toFixed(2));
			total_cal();
        }, 500);
    }
}

function total_cal() {
    $("#total_quantity").html('0');
    $("#total_mrp").html('₹0');
    $("#total_discount_amount").html('₹0');
    $("#tax_amount").html('₹0');
    $('#round_off').val(0);
    $("#sub_total_mrp").html('₹0');
    $("#total_payble_amount").html('₹0');


    $('#sub_total_mrp-input').val(0);

    var total_quantity = 0;
    var total_mrp = 0;
    var total_discount_amount = 0;
    var tax_amount = 0;
    var round_off = 0;
    var sub_total_mrp = 0;
    var total_payble_amount = 0;

    setTimeout(function() {

        $("#product_sell_record_sec tr").each(function(index, e) {
            var product_id = $(this).attr('id').split('sell_product_')[1];
            var tbl_row = $(this).data('id');

            var product_qty = $("#product_qty_" + product_id).val();
            if (product_qty == '') {
                product_qty = 0;
            }
            total_quantity += (Number(product_qty));

            var unit_price = $("#product_unit_price_amount_" + product_id).val();
            var total_selling_cost = Number(unit_price) * Number(product_qty);
            total_mrp += (Number(total_selling_cost));

            var disc_amount = $("#product_disc_amount_" + product_id).val();
            total_discount_amount += (Number(disc_amount));

            var total_amount = $("#product_total_amount_" + product_id).html();
            sub_total_mrp += (Number(total_amount));

        });

        $("#total_quantity").html(total_quantity);
        $("#total_mrp").html('₹' + total_mrp);
        $("#total_discount_amount").html('₹' + total_discount_amount);
        $("#sub_total_mrp").html('₹' + sub_total_mrp);
        $("#total_payble_amount").html('₹' + sub_total_mrp);
        $('#sub_total_mrp-input').val(sub_total_mrp);
    }, 200);
}

function checkforroundoff(e, c) {
    var notallow = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '~', '`', '/', '*', '+', '>', '<', '?'];
    var allowedKeyCodesArr = [9, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 8, 37, 39, 109, 189, 46, 110, 190];
    if ($.inArray(e.key, notallow) != -1) {
        e.preventDefault();
    } else if ($.inArray(e.keyCode, allowedKeyCodesArr) === -1) {
        e.preventDefault();
    } else if ($.trim($(c).val()).indexOf('.') > -1 && $.inArray(e.keyCode, [110, 190]) !== -1) {
        e.preventDefault();
    } else {
        return true;
    }
}

$(document).on('keyup', '#round_off', function() {
    if ($("#round_off").val() != '') {
        var roundoff = 0;
        roundoff = $("#round_off").val();
        if ($("#round_off").val() == '-') {
            roundoff = 0;
        }
        $("#total_payble_amount").text((parseFloat($("#sub_total_mrp-input").val()) + parseFloat(roundoff)).toFixed(2));
    } else {
        $("#round_off").val(0);
    }
});

