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