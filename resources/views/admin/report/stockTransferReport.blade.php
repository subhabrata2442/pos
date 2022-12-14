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
        <h4>{{$data['heading']}}</h4>
      </div>
      <div class="col d-flex invoiceAmout justify-content-center">
        <!--<ul class="d-flex">
          <li>Total Qty : <span>{{$data['total_qty']}}</span></li>
          <li>Total Amount : <span>{{number_format($data['total_cost'],2)}}</span></li>
        </ul>-->
      </div>
      <div class="col-auto"> <a href="javascript:;" class="searchDropBtn">Advance Search <i class="fas fa-chevron-circle-down"></i></a> </div>
    </div>
  </div>
</div>
<div class="card toggleCard">
  <form action="" method="get" id="filter">
    <div class="row">
      <div class="col-lg-2 col-md-2 col-sm-12 col-12">
        <div class="form-group">
          <label for="date_search" class="mr-3">Date Filter</label>
          <input type="text" class="form-control" name="datefilter" id="reportrange" placeholder="Select Date" autocomplete="off" value="{{request()->input('datefilter')}}">
          <input type="hidden" name="start_date" id="start_date" value="{{request()->input('start_date')}}">
          <input type="hidden" name="end_date" id="end_date" value="{{request()->input('end_date')}}">
        </div>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-12 col-12">
        <div class="form-group">
          <label for="customer_last_name" class="form-label">Product Name / Barcode</label>
          <div class="position-relative">
            <input type="text" class="form-control" id="search_product" name="product" value="{{request()->input('product')}}" autocomplete="off">
            <ul id="search_product_list" class="auto_search_result">
            </ul>
          </div>
          <input type="hidden" id="product_id" name="product_id" value="{{request()->input('product_id')}}">
        </div>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-12 col-12">
        <div class="form-group">
          <label for="" class="form-label">Select Category</label>
          <select class="form-control custom-select form-control-select" id="" name="category">
            <option value="">Select Category</option>
            
            
						@forelse ($data['categories'] as $category)
							
            
            <option value="{{$category->id}}" {{request()->input('category') == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
            
            
						@empty
							
						@endforelse
					
          
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-12 col-12">
        <div class="form-group">
          <label for="" class="form-label">Select Subcategory</label>
          <select class="form-control custom-select form-control-select" id="" name="sub_category">
            <option value="">Select Subcategory</option>
            
            
						@forelse ($data['sub_categories'] as $sub_category)
							
            
            <option value="{{$sub_category->id}}" {{request()->input('sub_category') == $sub_category->id ? 'selected' : ''}}>{{$sub_category->name}}</option>
            
            
						@empty
							
						@endforelse
					
          
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-12 col-12">
        <div class="form-group">
          <label for="" class="form-label">Select Size</label>
          <select class="form-control custom-select form-control-select" id="" name="size">
            <option value="">Select Size</option>
            
            
						@forelse ($data['sizes'] as $size)
							
            
            <option value="{{$size->id}}" {{request()->input('size') == $size->id ? 'selected' : ''}}>{{$size->name}}</option>
            
            
						@empty
							
						@endforelse
					
          
          </select>
        </div>
      </div>
      <div class="col-12">
        <ul class="saveSrcArea d-flex align-items-center justify-content-center mb-2">
          <li> <a href="javascript:?" class="saveBtn-2 reset-btn" id="reset">Reset</i></a> </li>
          <li>
            <button class="saveBtn-2" type="submit">Search <i class="fas fa-arrow-circle-right"></i></button>
          </li>
          <li class="d-flex align-items-center">
            <div>
              <select class="form-control custom-select form-control-select" id="report_type">
                <option value=""> Select Report Type</option>
                <option value="item_wise_sales_report"> Item Wise sales report</option>
                <option value="month_wise_report"> Month Wise report</option>
                <option value="brand_wise_report"> Brand Wise report</option>
                <!--<option value="e_report"> E-Report</option>-->
              </select>
            </div>
            <div>
              <button type="button" id="download_report" class="srcBtnWrapGo"><i class="fas fa-download"></i></button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </form>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <x-alert />
      <div class="table-responsive custom-table">
        <table id="" class="table table-bordered text-nowrap">
          <thead>
          <th scope="col">Invoice No</th>
            <th scope="col">Barcode</th>
            <th scope="col">Category</th>
            <th scope="col">Sub Category</th>
            <th scope="col">Product Name</th>
            <th scope="col">Size</th>
            <th scope="col">Qty</th>
            <th scope="col">Date</th>
              </thead>
          <tbody>
          
          @forelse ($data['products'] as $product)
          <tr>
            <th>{{$product->invoice_no}}</th>
            <td>{{$product->product_barcode}}</td>
            <td>{{$product->category->name}}</td>
            <td>{{$product->subcategory->name}}</td>
            <td>{{$product->product->product_name}}</td>
            <td>{{$product->size->name}}</td>
            <td>{{$product->c_qty}}</td>
            
            <th>{{$product->created_at}}</th>
          </tr>
          @empty
          <tr >
            <td colspan="11"> No data found </td>
          </tr>
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
		var report_type 	= $('#report_type').val();
		var start_date 		= $('input[name=start_date]').val();
		var end_date 		= $('input[name=end_date]').val();
		var category 		= $('select[name=category]').val();
		var subcategory_id 	= $('select[name=sub_category]').val();
		
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
				var url = "{{route('admin.report.sales.product.item_wise_stock-transfer')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date+'&category='+category+'&subcategory_id='+subcategory_id;
			}else if(report_type == 'e_report'){
				var url = "{{route('admin.report.sales.product.stock_transfer_e_report')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date+'&category='+category+'&subcategory_id='+subcategory_id;
			}else if(report_type == 'month_wise_report'){
				var url = "{{route('admin.report.product.month_wise_stock_transfer')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date+'&category='+category+'&subcategory_id='+subcategory_id;
			}else if(report_type == 'text_download'){
				var url = "{{route('admin.report.sales.product.download')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date+'&category='+category+'&subcategory_id='+subcategory_id;
				//$(this).attr('href',url+'?start_date='+start_date+'&end_date='+end_date);
			}else if(report_type == 'brand_wise_report'){
				var product_id = $('input[name=product_id]').val();
				
				if(product_id == ''){
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Please select Product!',
					});
					return false;	
				}
				
				var url = "{{route('admin.report.sales.product.brand_wise_stock-transfer')}}";
				var href = url+'?start_date='+start_date+'&end_date='+end_date+'&product_id='+product_id+'&category='+category+'&subcategory_id='+subcategory_id;
				
				
			
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