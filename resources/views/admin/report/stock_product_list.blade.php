@extends('layouts.admin')
@section('admin-content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <x-alert />
      <div class="table-responsive dataTable-design">
        <table id="stock_product" class="table table-bordered">
          <thead>
            <th>Barcode</th>
            <th>Bottle/case</th>
            <th>Free Qty</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>Brand Name</th>
            <th>Batch No</th>
            <th>Measure</th>
            <th>Strength</th>
            <th>In B.L</th>
            <th>In LPL</th>
            <th>Unit Cost</th>
            <th>Retailer margin</th>
            <th>Round Off</th>
            <th>SP Fee</th>
            <th>Offer Price</th>
            <th>MRP</th>
            <th>Total Cost</th>
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
		processing: true,
		serverSide: true,
		searchDelay: 350,
		ajax: "{{ route('admin.report.stock_product.list',$data['inward_stock_id'])}}",
		columns: [
			{
				data: 'barcode',
				name: 'barcode'
			},	
			{
				data: 'bottle_case',
				name: 'bottle_case'
			},	
			{
				data: 'free_qty',
				name: 'free_qty'
			},	
			{
				data: 'category',
				name: 'category'
			},	
			{
				data: 'sub_category',
				name: 'sub_category'
			},	
			{
				data: 'brand',
				name: 'brand'
			},	
			{
				data: 'batch_no',
				name: 'batch_no'
			},	
			{
				data: 'measure',
				name: 'measure'
			},	
			{
				data: 'strength',
				name: 'strength'
			},	
			{
				data: 'bl',
				name: 'bl'
			},	
			{
				data: 'lpl',
				name: 'lpl'
			},	
			{
				data: 'unit_cost',
				name: 'unit_cost'
			},	
			{
				data: 'retailer_margin',
				name: 'retailer_margin'
			},	
			{
				data: 'round_off',
				name: 'round_off'
			},	
			{
				data: 'sp_fee',
				name: 'sp_fee'
			},	
			{
				data: 'offer_price',
				name: 'offer_price'
			},	
			{
				data: 'product_mrp',
				name: 'product_mrp'
			},	
			{
				data: 'total_cost',
				name: 'total_cost'
			},	
			
			
		]
	});

});
</script> 
@endsection 