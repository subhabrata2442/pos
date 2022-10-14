@extends('layouts.admin')
@section('admin-content')
<style>
	.content-header{
		display: none !important;
	}
	.custom-table table tbody tr:nth-child(odd){
		background: #f5f5f5;
	}
	.custom-table table tbody tr:nth-child(even){
		background: #f5e9e3;
	}
	.auto_search_result {
		list-style: none;
		padding: 0px;
		width: 100%;
		position: absolute;
		margin: 0;
		max-height: 300px;
		overflow-y: auto;
		z-index: 99999;
		top: 100%;
		background: #cfcff0;
	}
	.auto_search_result li {
		background: lavender;
		padding: 7px 15px;
		margin-bottom: 1px;
		font-size: 13px;
	}
	.auto_search_result li:hover {
		cursor: pointer;
		background: cadetblue;
		color: white;
	}
	.reset-btn{
		background-color: #d3681b;
	}
</style>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> 
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="srcBtnWrap">
  <div class="card">
    <div class="row align-items-center justify-content-between">
      <div class="col-auto">
        <h4>Stock Transfer</h4>
      </div>
    </div>
  </div>
</div>
<div class="card toggleCard"> </div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <x-alert />
      <div class="table-responsive custom-table">
        <table id="" class="table table-bordered text-nowrap">
          <thead>
          <th scope="col">Invoice No</th>
            <th scope="col">Date</th>
            <th scope="col">Barcode</th>
            <th scope="col">Product Name</th>
            <th scope="col">Qty</th>
            <th scope="col">Size</th>
              </thead>
              <tbody>
			@forelse ($data['products'] as $product)
			<tr>
				<th>{{$product->invoice_no}}</th>
                <th>{{$product->created_at}}</th>
                <td>{{$product->product_barcode}}</td>
                <td>{{$product->product->product_name}}</td>
                <td>{{$product->c_qty}}</td>
                <td>{{$product->size->name}}</td>
				
			</tr>
			@empty
				<tr ><td colspan="11"> No data found </td></tr>
			@endforelse
			
          </tbody>
        </table>
        {{ $data['products']->appends($_GET)->links() }} </div>
    </div>
  </div>
</div>
@endsection

@section('scripts') 
@if( Request::has('datefilter')) 
<script>
	$(".toggleCard").css("display", "block");
	</script> 
@endif 
<script type="text/javascript">

$(function() {
	
	/* $('#download_report').on("click",function(){
		var start_date = $('input[name=start_date]').val();
		//alert(start_date);
		var end_date = $('input[name=end_date]').val();
		var url = "{{route('admin.report.sales.product.download')}}";
		$(this).attr('href',url+'?start_date='+start_date+'&end_date='+end_date);
	}) */

	$('#download_report').on("click",function(){
		var report_type = $('#report_type').val();
		var start_date = $('input[name=start_date]').val();
		var end_date = $('input[name=end_date]').val();
		if(report_type == ''){
			Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select report type!',
            })
		}else if(start_date == '' && end_date== ''){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select date!',
            })
        }else{
			//console.log('sdfd');
			if(report_type == 'item_wise_sales_report'){
				var url = "{{route('admin.report.sales.product.item_wise')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date;
			}else if(report_type == 'month_wise_report'){
				var url = "{{route('admin.report.product.month_wise')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date;
			}else if(report_type == 'text_download'){
				var url = "{{route('admin.report.sales.product.download')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date;
				//$(this).attr('href',url+'?start_date='+start_date+'&end_date='+end_date);
			}
			window.open(href);
		    
        }
        
		
	})
	//Start date range picker
	/* var start = moment().subtract(29, 'days');
    var end = moment();
	 */

   /*  function cb(start, end) {
        //$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#reportrange').val(start.format('D-MM-YYYY') + ' - ' + end.format('D-MM-YYYY'));
		$('#start_date').val(start.format('YYYY-MM-DD'));
		$('#end_date').val(end.format('YYYY-MM-DD'));
    } */

    $('#reportrange').daterangepicker({
        //startDate: start,
        //endDate: end,
		autoUpdateInput: false,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    //cb(start, end);

	$('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
      	$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	  	$('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
		$('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
  	});
	//End Date range picker
	$('.searchDropBtn').on("click",function(){
		$(".toggleCard").slideToggle();
	})
	
	//Cusomer List
	$("#search_customer").keyup(function() {
		var search = $(this).val();
		if (search != "") {
            $.ajax({
                url: '{{route('admin.ajax.customer-list')}}',
                type: 'get',
                data: {
                    search: search,
                    _token: prop.csrf_token
                },
                dataType: 'json',
                success: function(response) {
                    var len = response.result.length;
                    $("#search_customer_list").empty();
                    for (var i = 0; i < len; i++) {
                        var id = response.result[i]['id'];
                        var name = response.result[i]['customer_fname'] + ' ' + response.result[i]['customer_last_name'];
                        $("#search_customer_list").append("<li value='" + id + "'>" + name + "</li>");
                    }
                    // binding click event to li
                    $("#search_customer_list li").bind("click", function() {
                        $('.loader_section').show();
						var cname = $(this).text();
    					var cid = $(this).val();
						$('#search_customer').val(cname);
						$('#customer_id').val(cid);
						$("#search_customer_list").empty();
                    });
                }
            });
        }
	});
	//Invoice List
	$("#search_sale_invoice").keyup(function() {
		var search = $(this).val();
		if (search != "") {
            $.ajax({
                url: '{{route('admin.ajax.sale-invoice-list')}}',
                type: 'get',
                data: {
                    search: search,
                    _token: prop.csrf_token
                },
                dataType: 'json',
                success: function(response) {
                    var len = response.result.length;
                    $("#search_sale_invoice_list").empty();
                    for (var i = 0; i < len; i++) {
                        var id = response.result[i]['id'];
                        var name = response.result[i]['invoice_no'];
                        $("#search_sale_invoice_list").append("<li value='" + id + "'>" + name + "</li>");
                    }
                    // binding click event to li
                    $("#search_sale_invoice_list li").bind("click", function() {
                        $('.loader_section').show();
						var cname = $(this).text();
    					var cid = $(this).val();
						$('#search_sale_invoice').val(cname);
						$('#invoice_id').val(cid);
						$("#search_sale_invoice_list").empty();
                    });
                }
            });
        }
	});

	//Product List
	$("#search_product").keyup(function() {
		var search = $(this).val();
		if (search != "") {
            $.ajax({
                url: '{{route('admin.ajax.sale-product')}}',
                type: 'get',
                data: {
                    search: search,
                    _token: prop.csrf_token
                },
                dataType: 'json',
                success: function(response) {
                    var len = response.result.length;
                    $("#search_product_list").empty();
                    for (var i = 0; i < len; i++) {
                        var id = response.result[i]['id'];
                        var name = response.result[i]['product_name']+ ' ' + response.result[i]['product_barcode'];
                        $("#search_product_list").append("<li value='" + id + "'>" + name + "</li>");
                    }
                    // binding click event to li
                    $("#search_product_list li").bind("click", function() {
                        $('.loader_section').show();
						var cname = $(this).text();
						console.log(cname);
    					var cid = $(this).val();
						$('#search_product').val(cname);
						$('#product_id').val(cid);
						$("#search_product_list").empty();
                    });
                }
            });
        }
	});
	//reset form
	$("#reset").click(function() {
		$('#filter').trigger("reset");
		window.location = window.location.href.split("?")[0];
	});
});

</script> 
@endsection 