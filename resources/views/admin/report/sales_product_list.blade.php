@extends('layouts.admin')
@section('admin-content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <x-alert />
	  <a href="{{route('admin.report.sales.product.download')}}" target="_blank">Download</a>
	  <form method="get" id="search-form" class="form-inline" role="form">

		<div class="form-group">
			<label for="date_search">Date</label>
			<input type="date" class="form-control" name="date_search" id="date_search" placeholder="search Date">
		</div>
		

		<button type="submit" class="btn btn-primary">Search</button>
	</form>
      <div class="table-responsive dataTable-design">
		{{-- <input type="data" name="date_filter" class="form-control searchDate" placeholder="Date"> --}}
        <table id="stock_product" class="table table-bordered">
          <thead>
			<th>Product Name</th>
            <th>Barcode</th>
			<th>Measure</th>
			<th>Qty</th>
            <th>MRP</th>
            <th>Total Cost</th>
            <th>Date</th>
           </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts') 
<script type="text/javascript">



/* $(document).on('change','#product_upload_file',function(){
	this.form.submit();
}); */

$(function() {
	var table = $('#stock_product').DataTable({
		dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
            "<'row'<'col-xs-12't>>"+
            "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
		processing: true,
		serverSide: true,
		searchDelay: 350,

		ajax: {
			url : "{{ route('admin.report.sales.product')}}",
			data: function (d) {
                //d.email = $('.searchDate').val(),
                //d.search = $('input[type="search"]').val()
				//d.date = $('input[name=date_search]').val();
            }
		},
		columns: [
			{
				data: 'product_name',
				name: 'product_name'
			},	
			{
				data: 'barcode',
				name: 'barcode'
			},	
			{
				data: 'measure',
				name: 'measure'
			},	
			{
				data: 'product_qty',
				name: 'product_qty'
			},	
			{
				data: 'product_mrp',
				name: 'product_mrp'
			},	
			{
				data: 'total_cost',
				name: 'total_cost'
			},	
			{
				data: 'created_at',
				name: 'created_at'
			},	
			
			
			
		]
	});
	/* $(".searchDate").keyup(function(){
        table.draw();
    }); */

	/* $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    }); */

});


</script> 
@endsection 