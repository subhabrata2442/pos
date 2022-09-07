@extends('layouts.admin')
@section('admin-content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <x-alert />
      
      <div class="table-responsive dataTable-design">
        <table id="purchase_list" class="table table-bordered">
          <thead>
            <th>Invoice No.</th>
            <th>TP Pass.</th>
            <th>Inward date</th>
            <th>Purchase date</th>
            <th>Supplier Name</th>
            <th>Total Qty</th>
            <th>Total Cost</th>
            <th>Action</th>
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

	var table = $('#purchase_list').DataTable({
		processing: true,
		serverSide: true,
		searchDelay: 350,
		ajax: "{{ route('admin.report.purchase') }}",
		columns: [
			{
				data: 'invoice_no',
				name: 'invoice_no'
			},	
			{
				data: 'tp_no',
				name: 'tp_no'
			},	
			{
				data: 'inward_date',
				name: 'inward_date'
			},
			{
				data: 'purchase_date',
				name: 'purchase_date',
			},
			{
				data: 'supplier_id',
				name: 'supplier_id'
			},
			{
				data: 'total_qty',
				name: 'total_qty'
			},
			{
				data: 'sub_total',
				name: 'sub_total'
			},
			{
				data: 'action',
				name: 'action',
                orderable: false, searchable: false
			},
			
		]
	});

});
</script> 
@endsection 