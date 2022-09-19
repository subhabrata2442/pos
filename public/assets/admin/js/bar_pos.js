/*TABLE BOOKING*/
$(document).ready(function() {
    $(".payBtn").on('click', function(e) {
        $('.payWrapLeft').show();
        $('.payWrapRight').removeClass('tabMenuHideWrapRight');

        var total_quantity = $('#input-total_quantity').val();
        var total_payble_amount = $('#input-total_mrp').val();
        $('#due_amount_tendering').val(total_payble_amount);
        $('#tendered_amount').val('');
        $('#tendered_change_amount').val('');
        if (total_quantity > 0) {
            $(".payWrap").toggleClass('active');
        } else {
            toastr.error("Add minimum one product!");
        }
    });

    $(".paymentModalCloseBtn").on('click', function(e) {
        $(".payWrap").removeClass('active');
    });
});


$('input[type=radio][name=food_type]').change(function() {
    $('.food_type_radio_sec').removeClass('active');
    $(this).parent().addClass('active');
    var food_type = this.value;

    $.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            food_type: food_type,
            action: 'get_food_type_wise_category',
            _token: prop.csrf_token
        },
        dataType: 'json',
        beforeSend: function() {
            $('#ajax_loader').fadeIn();
        },
        complete: function() {
            $('#ajax_loader').fadeOut();
        },
        success: function(response) {
            var len = response.result.length;
            $("#food_category_sec").empty();
			$("#food_items_section").empty();
			
            var html = '';
            var tbl_detail = '';
			var food_img	= prop.url+'/assets/admin/images/food.svg';
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    tbl_detail = response.result[i];
                    cat_name = '';
                    if (tbl_detail.name != null || tbl_detail.name != undefined) {
                        cat_name = tbl_detail.name;
                    }

                    html = '<li><a href="javascript:;" data-id="' + tbl_detail.subcategory_id + '" class="category_btn"><img src="'+food_img+'" alt="">' + cat_name + '</a></li>';

                    $("#food_category_sec").prepend(html);
                }
            } else {
                toastr.error("Table not available!");
            }
        }
    });
});

$(document).on('click', '.category_btn', function() {
    var cat_id = $(this).data('id');
	var food_type = $('input[name="food_type"]:checked').val();
    $.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            cat_id: cat_id,
			food_type: food_type,
            action: 'get_category_wise_food_items',
            _token: prop.csrf_token
        },
        dataType: 'json',
        beforeSend: function() {
            $('#ajax_loader').fadeIn();
        },
        complete: function() {
            $('#ajax_loader').fadeOut();
        },
        success: function(response) {
            var len = response.result.length;
            $("#food_items_section").empty();
			
            var html 		= '';
            var tbl_detail 	= '';
			var food_img	= prop.url+'/assets/admin/images/food-1.png';
            
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    tbl_detail = response.result[i];
                    product_name = '';
                    if (tbl_detail.product_name != null || tbl_detail.product_name != undefined) {
                        product_name = tbl_detail.product_name;
                    }
					
                    html = '<div class="ftBox-col">' +
                        '<div class="ftBox">' +
                        '<a href="javascript:;" data-id="' + tbl_detail.product_id + '" class="item_btn"> <img src="'+food_img+'" alt=""> <span>'+product_name+'</span> </a>' +
                        '</div>' +
                        '</div>';

                    $("#food_items_section").prepend(html);
                }
            } else {
                toastr.error("Food items not available!");
            }
        }
    });
});

$(document).on('click', '.item_btn', function() {
    var product_id = $(this).data('id');
    var food_type = $('input[name="food_type"]:checked').val();
    $.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            product_id: product_id,
            food_type: food_type,
            action: 'get_bar_product_byId',
            _token: prop.csrf_token
        },
        dataType: 'json',
        beforeSend: function() {
            $('#ajax_loader').fadeIn();
        },
        complete: function() {
            $('#ajax_loader').fadeOut();
        },
        success: function(response) {
            if (response.product_result != '') {
                var html = '';
                var item_detail = response.product_result;
                var product_name = '';
                if (item_detail.brand_name != null || item_detail.brand_name != undefined) {
                    product_name = item_detail.brand_name;
                }
                var product_size = '';
                if (item_detail.size != null || item_detail.size != undefined) {
                    product_size = item_detail.size;
                }


                var product_id = item_detail.branch_stock_product_id;

                var item_prices_length = item_detail.item_prices.length;
                var w_stock = item_detail.item_prices[0].w_qty;
                var c_stock = item_detail.item_prices[0].c_qty;
                var product_mrp = item_detail.item_prices[0].product_mrp;
                var product_price_id = item_detail.item_prices[0].price_id;

                var item_row = 0;
                if ($('#table_cart_items_record_sec tr').length == 0) {
                    item_row++;
                } else {
                    var max = 0;
                    $("#table_cart_items_record_sec tr").each(function() {
                        var value = parseInt($(this).data('id'));
                        max = (value > max) ? value : max;
                    });
                    item_row = max + 1;
                }

                if (stock_type == 'w') {
                    stock = w_stock;
                } else {
                    stock = c_stock;
                }

                if (stock > 0) {
                    var same_item = 0;
                    $("#table_cart_items_record_sec tr").each(function() {
                        var row_product_id = $(this).attr('id').split('_')[2];
                        if (row_product_id == product_id) {
                            same_item = 1;

                            var qty = $('#product_qty_' + product_id).val();
                            var item_qty = Number(qty) + 1;

                            if (Number(item_qty) > Number(stock)) {
                                $('#product_qty_' + product_id).val(stock);
                                toastr.error("Entered Qty should not be greater than Stock");
                                return false;
                            } else {
                                $('#product_qty_' + product_id).val(item_qty);
                                var unit_price = $("#product_mrp_" + product_id).val();
                                var total_selling_cost = Number(unit_price) * Number(item_qty);
                                var total_amount = Number(total_selling_cost)
                                $("#product_total_price_" + product_id).html(Number(total_amount).toFixed(2));
                                $("#product_total_amount_" + product_id).val(Number(total_amount).toFixed(2));
                            }

                            total_cal()
                        }
                    });

                    if (same_item == 0) {
                        html += '<tr id="sell_product_' + product_id + '" data-id="' + item_row + '">' +
                            '<input type="hidden" name="product_id[]" value="' + product_id + '">' +
                            '<input type="hidden" id="product_w_stock_' + product_id + '" value="' + w_stock + '">' +
                            '<input type="hidden" id="product_c_stock_' + product_id + '" value="' + c_stock + '">' +
                            '<input type="hidden" name="product_mrp[]" id="product_mrp_' + product_id + '" value="' + product_mrp + '">' +
                            '<input type="hidden" name="product_total_amount[]" id="product_total_amount_' + product_id + '" value="' + product_mrp + '">' +
                            '<input type="hidden" name="product_price_id[]" id="product_price_id_' + product_id + '" value="' + product_price_id + '">' +
                            '<input type="hidden" name="product_name[]" id="product_name_' + product_id + '" value="' + product_name + '">' +
                            '<td>' + item_row + '</td>' +
                            '<td class="text-center">' + product_name + '</td>' +
                            '<td class="text-center">' + product_size + '</td>' +
                            '<td class="text-center">' + product_mrp + '</td>' +
                            '<td class="text-center"><div>' +
                            '<div class="priceControl d-flex">' +
                            '<button class="controls2" value="-">-</button>' +
                            '<input type="number" class="qtyInput2 product_qty" name="product_qty[]" id="product_qty_' + product_id + '" value="1" data-max-lim="20">' +
                            '<button class="controls2" value="+">+</button>' +
                            '</div>' +
                            '</div></td>' +
                            '<td class="text-center" id="product_total_price_' + product_id + '">' + product_mrp + '</td>' +
							'<td><a href="javascript:;" onclick="remove_item(' + item_row + ');"><i class="fas fa-times-circle"></i></a></td>' +
                            '</tr>';

                        $("#table_cart_items_record_sec").append(html);
                        total_cal()

                    }

                } else {
                    toastr.error("Stock not available for this Product");
                }
            } else {
                toastr.error("Food items not available!");
            }

        }
    });
});

$(document).on('click', '.priceControl .controls2', function() {
	var product_id 	= $(this).siblings('.product_qty').attr('id').split('product_qty_')[1];
	var qty 		= $('#product_qty_'+product_id).val();
	var maxlim 		= $('#product_qty_'+product_id).attr('data-max-lim');
	
	if (($(this).val() == '+') && (parseInt(maxlim) > qty)) {
		qty++;
	} else if ($(this).val() == '-' && qty > 1) {
		qty--;
	}
	$('#product_qty_'+product_id).val(qty);
	
	
	 var unit_price = $("#product_mrp_" + product_id).val(); 
	 var total_selling_cost = Number(unit_price) * Number(qty);
     var total_amount = Number(total_selling_cost)
     $("#product_total_price_" + product_id).html(Number(total_amount).toFixed(2));
	 $("#product_total_amount_" + product_id).val(Number(total_amount).toFixed(2));
	
	total_cal()
});


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
        var unit_price = $("#product_mrp_" + product_id).val(); 
        var total_selling_cost = Number(unit_price) * Number(qty);
        var total_amount = Number(total_selling_cost)
        $("#product_total_price_" + product_id).html(Number(total_amount).toFixed(2));
		$("#product_total_amount_" + product_id).val(Number(total_amount).toFixed(2));

    }

    total_cal();
});

function remove_item(row) {
    $("#table_cart_items_record_sec").find("tr[data-id='" + row + "']").remove();
    total_cal();
}

function total_cal() {
    setTimeout(function() {
        $("#total_quantity").html('0');
        $("#total_mrp").html('₹0');
        var total_quantity = 0;
        var total_mrp = 0;
		
		$('#input-total_quantity').val(0);
		$('#input-total_mrp').val(0);

        $("#table_cart_items_record_sec tr").each(function(index, e) {
            var product_id = $(this).attr('id').split('sell_product_')[1];
            var tbl_row = $(this).data('id');

            var product_qty = $("#product_qty_" + product_id).val();
            if (product_qty == '') {
                product_qty = 0;
            }
            total_quantity += (Number(product_qty));

            var unit_price = $("#product_mrp_" + product_id).val();
            var total_selling_cost = Number(unit_price) * Number(product_qty);
            total_mrp += (Number(total_selling_cost));
        });


        if (total_quantity > 0) {
            $("#total_quantity_mrp_section").show();
            $("#print_bill_section").show();

            $("#total_quantity").html(total_quantity);
            $("#total_mrp").html('₹' + total_mrp);
			
			$('#input-total_quantity').val(total_quantity);
			$('#input-total_mrp').val(total_mrp);
        } else {
            $("#total_quantity_mrp_section").hide();
            $("#print_bill_section").hide();
        }

    }, 200);
}

/*END TABLE BOOKING*/



$(document).on('click', '.floor_tab_btn', function() {
    var floor_id = $(this).data('id');
    $.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            floor_id: floor_id,
            action: 'get_table',
            _token: prop.csrf_token
        },
        dataType: 'json',
        beforeSend: function() {
            $('#ajax_loader').fadeIn();
        },
        complete: function() {
            $('#ajax_loader').fadeOut();
        },
        success: function(response) {
            var len = response.result.length;
            $("#floor_table_sec_" + floor_id).empty();
            var html = '';
            var tbl_detail = '';
            var total_amount = 0;
            var waiter_name = '';
            var tbl_status = 1;
            var tbl_color = 'bgGray';
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    tbl_detail = response.result[i];
                    waiter_name = '';
                    if (tbl_detail.waiter_name != null || tbl_detail.waiter_name != undefined) {
                        waiter_name = tbl_detail.waiter_name;
                    }
                    total_amount = 0;
                    if (tbl_detail.total_amount != null || tbl_detail.total_amount != undefined) {
                        total_amount = tbl_detail.total_amount;
                    }
                    tbl_color = 'bgGray';
                    if (tbl_detail.status == 2) {
                        tbl_color = 'bgBlue';
                    } else if (tbl_detail.status == 3) {
                        tbl_color = 'bgSafron';
                    }
                    html = '<div class="col-lg-2 col-md-2 col-sm-6 col-12 tbl_btn" data-tblid="' + tbl_detail.table_id + '" data-floorid="' + floor_id + '" id="table_' + floor_id + '_' + tbl_detail.table_id + '">' +
                        '<div class="tableBox ' + tbl_color + '">' +
                        '<div class="tableBoxTop d-flex align-items-center justify-content-between">' +
                        '<h5>' + tbl_detail.table_name + '</h5>' +
                        '<div class="countdownArea d-flex align-items-center"> <i class="far fa-clock"></i>' +
                        '<div class="countdown">00:00</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="tableBoxBtm">' +
                        '<ul>' +
                        '<li><span>' + waiter_name + '</span></li>' +
                        ' <li>Amount: <span>' + total_amount + '</span></li>' +
                        '</ul>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $("#floor_table_sec_" + floor_id).prepend(html);
                }
            } else {
                toastr.error("Table not available!");
            }
        }
    });
});

$(document).on('click', '.tbl_btn', function() {
	var floor_id	= $(this).data('floorid');
	var tbl_id		= $(this).data('tblid');
	
	$('#table_id').val('');
	$('#floor_id').val('');
	
	$.ajax({
        url: prop.ajaxurl,
        type: 'post',
        data: {
            floor_id: floor_id,
			tbl_id: tbl_id,
            action: 'get_table_availability',
            _token: prop.csrf_token
        },
        dataType: 'json',
        beforeSend: function() {
            $('#ajax_loader').fadeIn();
        },
        complete: function() {
            $('#ajax_loader').fadeOut();
        },
        success: function(response) {
			var status = response.status;
			if(status==1){
				$('#waiterModal').modal('show');
				$('#table_id').val(tbl_id);
				$('#floor_id').val(floor_id);
				$('#setWaiterModalLabel').text('Set Waiter For '+response.table_name);
			}else if(status==2){
				var booking_url = prop.url +'/'+ response.booking_url;
				window.location.href = booking_url;
			}else{
				toastr.error("Table not available!");
			}
		}
    });
});
$(document).ready(function() {
    $("#waiter-table-form").validate({
        rules: {
			waiter_id: "required",
		},
        messages: {},
        errorElement: "em",
        errorPlacement: function(error, element) {},
        highlight: function(element, errorClass, validClass) {},
        unhighlight: function(element, errorClass, validClass) {},
        submitHandler: function(form) {
			var floor_id	= $('#floor_id').val();
			var table_id	= $('#table_id').val();
			var waiter_id	= $('#waiter_id').val();
			
			$.ajax({
				url: prop.ajaxurl,
				type: 'post',
				data: {
					floor_id: floor_id,
					waiter_id: waiter_id,
					tbl_id: table_id,
					action: 'set_table_booking',
					_token: prop.csrf_token
				},
				dataType: 'json',
				beforeSend: function() {
					$('#ajax_loader').fadeIn();
				},
				complete: function() {
					$('#ajax_loader').fadeOut();
				},
				success: function(response) {
					var booking_url = prop.url +'/'+ response.booking_url;
					if(booking_url!=''){
						window.location.href = booking_url;
					}else{
						toastr.error("Table not available!");
					}
				}
			});
		}
    });
});