@extends('layouts.sidebar_collapse_admin')
@section('admin-content')
<style>
.error {
    display: none !important;
}
.red_border {
    border: 1px solid #e50b0b;
}
</style>
<div class="row">
<form method="post" action="" class="needs-validation" id="supplier-inward_stock-form" novalidate enctype="multipart/form-data">
  <div class="col-12 mb-3">
    <div class="commonBox"> 
      <!--<div class="arrowUpDown2"> <span class="arrowDown"><i class="fas fa-arrow-alt-circle-down"></i></span> </div>-->
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
          <div class="supplierWrap">
            <div class="invArea">
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Supplier</li>
                <li class="invAreaVal">
                  <input type="text" name="supplier" id="supplier" class="form-control input-1" required="required" readonly="readonly" value="{{$data['supplier']->company_name ?? ''}}">
                </li>
              </ul>
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Transport Pass No.</li>
                <li class="invAreaVal">
                  <input type="text" name="tp_no" id="tp_no" class="form-control input-1" required="required">
                </li>
              </ul>
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Invoice Number</li>
                <li class="invAreaVal">
                  <input type="text" name="invoice_no" id="invoice_no" class="form-control input-1" required="required">
                </li>
              </ul>
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Purchase Date</li>
                <li class="invAreaVal">
                  <input type="date" name="purchase_date" id="purchase_date" class="form-control input-1">
                </li>
              </ul>
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Inward Date</li>
                <li class="invAreaVal">
                  <input type="date" name="inward_date" id="inward_date" class=" form-control input-1" required="required">
                </li>
              </ul>
              <ul class="d-flex align-items-center">
                <li class="invAreaInf">Warehouse</li>
                <li class="invAreaVal">
                  <div class="mb-3 form-group relative formBox">
                    <div class="upplierBox d-flex">
                      <input type="text" name="warehouse" id="warehouse" class="form-control input-1" placeholder="Warehouse" required="required">
                      <ul id="warehouse_search_result">
                      </ul>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
          <div class="supplierDetails" id="supplier_details_sec">
            <h4>Warehouse Details :</h4>
            <p> Contact : <br>
              Email : <br>
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
          <div class="noteArea"> 
            <div class="noteAreaInner">
              <textarea name="shipping_note" id="shipping_note" cols="30" rows="10" placeholder="Shipping Note" style="height: 51px;"></textarea>
            </div>
            <div class="noteAreaInner">
              <textarea name="additional_note" id="additional_note" cols="30" rows="10" placeholder="Additional Note"></textarea>
            </div>
            <div class="noteAreaInner">
              <select class="form-control custom-select form-control-select" id="invoice_stock" required="required">
                <option value="">Select Stock</option>
                <option value="counter">Counter Stock</option>
                <option value="bar">Bar Stock</option>
              </select>
            </div>
            <div class="noteAreaInner">
              <select class="form-control custom-select form-control-select" id="invoice_stock_type" required="required">
                <option value="">Select Stock Type</option>
                <option value="warehouse">Warehouse</option>
                <option value="counter">counter</option>
              </select>
            </div>
            
            <!--<div class="noteAreaInner"><a href="javascript:;" class="downloadTemplate"><i class="fas fa-download"></i> Download Inward Template</a></div>-->
            <div class="noteAreaInner">
              <ul class="d-flex flex-wrap justify-content-between">
                <li><a href="javascript:;" class="btn btn-info" id="upload_invoice"><i class="fas fa-upload"></i> Upload Products</a></li>
                <li>
                  <button type="submit" class="btn btn-primary">Submit <i class="fas fa-paper-plane"></i></button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<form method="post" action="" class="needs-validation" id="supplier-inward_stock-product-form" novalidate enctype="multipart/form-data">
  <input type="hidden" name="supplier_id" id="supplier_id" value="{{$data['supplier']->id ?? ''}}">
  <input type="hidden" name="warehouse_id" id="warehouse_id">
  <input type="hidden" name="invoice_no" id="input-supplier_invoice_no" />
  <input type="hidden" name="invoice_purchase_date" id="input-supplier_invoice_purchase_date" />
  <input type="hidden" name="invoice_inward_date" id="input-supplier_invoice_inward_date" />
  <input type="hidden" name="qty_total" id="input-supplier_qty_total" />
  <input type="hidden" name="gross_amount" id="input-supplier_gross_amount" />
  <input type="hidden" name="tax_amount" id="input-supplier_tax_amount" />
  <input type="hidden" name="sub_total" id="input-supplier_sub_total" />
  <input type="hidden" name="shipping_note" id="input-supplier_shipping_note" />
  <input type="hidden" name="additional_note" id="input-supplier_additional_note" />
  <input type="hidden" name="invoice_stock" id="input-invoice_stock" />
  <input type="hidden" name="invoice_stock_type" id="input-invoice_stock_type" />
  <div class="col-12 mb-3">
    <div class="commonBox purcheseDetails">
      <div class="arrowUpDown open_supplier_form"> <span class="arrowUp"><i class="fas fa-arrow-alt-circle-up"></i></span> </div>
      <div class="table-responsive">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td>Supplier Name <span class="d-block" id="supplier_company_name"></span></td>
              <td>Purchase Date<span class="d-block" id="supplier_invoice_purchase_date"></span></td>
              <td>Inward Date <span class="d-block" id="supplier_invoice_inward_date"></span></td>
              <td><div class="row noteAreaInner">
                  <div class="col-6">
                    <select class="form-control custom-select form-control-select" id="payment_method" name="payment_method">
                      <option value="">Select payment method</option>
                      <option value="ocr">Credit/Debit Card</option>
                      <option value="cheque">Cheque</option>
                      <option value="net_banking">Net Banking</option>
                      <option value="cash">Cash</option>
                    </select>
                  </div>
                  <div class="col-6">
                    <input type="date" name="payment_date" id="payment_date" class="form-control input-1">
                  </div>
                </div>
                <div class="noteAreaInner m-0">
                  <input type="text" name="payment_ref_no" id="payment_ref_no" class="form-control input-1" placeholder="Ref No">
                </div></td>
            </tr>
            <tr>
              <td>Invoice Number<span class="d-block" id="supplier_invoice_no"></span></td>
              <td>Transport Pass No.<span class="d-block" id="supplier_transport_pass_no"></span></td>
              <td>Quantity : <span id="qty_total">0</span></td>
              <td>Total Price : <span id="sub_total"></span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="commonBox">
      <div class="enterBarcode mb-3">
        <div class="row">
          <div class="col">
            <div class="form-group relative enterBarcodeSrc m-0">
              <input type="text" name="product_search" id="product_search" class="form-control input-1" autocomplete="off" placeholder="Enter Barcode/Product Code/Product Name">
              <button type="button" class="searchBtn"><i class="fas fa-search"></i></button>
              <ul id="product_search_result">
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="inwordTableWrap">
        <div class="table-responsive forTableHeight">
          <table class="inwordTable table-striped table-hover table mb-0">
            <thead>
              <tr>
                <th><i class="fas fa-times"></i></th>
                <th>Barcode</th>
                <th>Case Qty</th>
                <th>Bottle/case</th>
                <th>Loose Qty</th>
                <th>Total Qty</th>
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
              </tr>
            </thead>
            <tbody id="product_record_sec">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="inwardStockBtm" id="inwardStockSubmitBtmSec" style="display:none">
    <div class="commonBox">
      <div class="form-group relative formBox m-0">
        <button type="button" class="saveBtn" id="inwardStockSubmitBtm">Save <i class="fas fa-paper-plane ml-2"></i></button>
      </div>
    </div>
  </div>
</form>
<div class="modal fade" id="paymentDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="changeAddressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body"> ... </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bd-example-modal-lg" id="newProductItemsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog" style="max-width:95%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Product Add</h5>
      </div>
      <div class="modal-body">
        <div class="table-responsive forTableHeight">
          <table class="inwordTable table-striped table-hover table mb-0">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Bottle/case</th>
                <th>In case</th>
                <th>Total Qty</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Brand Name</th>
                <th>Batch No</th>
                <th>Measure</th>
                <th>Strength</th>
                <th>In B.L</th>
                <th>In LPL</th>
                <th>Retailer margin</th>
                <th>Round Off</th>
                <th>SP Fee</th>
                <th>MRP</th>
                <th>Total Amount</th>
              </tr>
            </thead>
            <tbody id="new_product_record_sec">
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="saveBtn" id="addProductSubmitBtm">Submit <i class="fas fa-paper-plane ml-2"></i></button>
      </div>
    </div>
  </div>
</div>
<div style="display:none;">
  <form method="post" action="{{ route('admin.purchase.invoice_upload') }}" class="needs-validation" id="invoice_upload-form" novalidate enctype="multipart/form-data">
    @csrf
    <input name="upload_invoice_pdf" id="upload_invoice_pdf" style="display:none" type="file">
  </form>
</div>
@endsection

@section('scripts') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script> 
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> 
<script src="{{ url('assets/admin/js/cHVyY2hhc2VfaW53YXJkX3N0b2Nr.js') }}"></script> 
<script src="{{ url('assets/admin/js/invisible_debut.js') }}"></script> 
<script>

/*$(document).ready(function() {
	
});*/

$(document).on('click', '#upload_invoice', function(e) {
	$('#upload_invoice_pdf').click();
});

$(document).on('change','#upload_invoice_pdf',function(){
	$("#invoice_upload-form").submit()
});

</script> 
@endsection 