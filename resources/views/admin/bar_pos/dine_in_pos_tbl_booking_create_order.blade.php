@extends('layouts.sidebar_collapse_admin')
<style>
.content-wrapper{
	background: #fff !important;
	height: auto !important;
}
.content-header {
	display: none !important;
}
.content-header {
	display: none !important;
}
</style>
@section('admin-content')
<section class="tablePage">
  <div class="filterTable d-flex flex-wrap">
    <div class="filterTableLeft d-flex flex-wrap">
      <div class="ftMenu">
        <div class="ftMenuTop">
          <div class="backBtnWrap"> <a href="{{ route('admin.pos.bar_dine_in_table_booking') }}" class="backBtn"><i class="fas fa-arrow-left"></i> Back</a> </div>
          <div class="catalogsearch-box d-flex justify-content-between">
            <div class="form-check food_type_radio_sec active">
              <input class="form-check-input" type="radio" name="food_type" id="food_type_liquor" value="liquor" checked>
              <label class="form-check-label" for="food_type_liquor">Liquor</label>
            </div>
            <div class="form-check food_type_radio_sec">
              <input class="form-check-input" type="radio" name="food_type" id="food_type_food" value="food">
              <label class="form-check-label" for="food_type_food">Food</label>
            </div>
          </div>
        </div>
        <div class="ftMenuBtm">
          <ul id="food_category_sec">
            @if(count($data['subcategory'])>0)
            @foreach($data['subcategory'] as $key => $row)
            <li><a href="javascript:;" data-id="{{$row->id}}" class="category_btn @if ($key === 0) active @endif"><img src="{{ url('assets/admin/images/food.svg') }}" alt=""> {{$row->name}}</a></li>
            @endforeach
            @endif
          </ul>
        </div>
      </div>
      <div class="ftDetails">
        <div class="relative mb-4 onlineTab">
          <input type="text" name="" id="" placeholder="Search By Name" class="co-searchInput">
          <button class="co-searchBtn"><i class="fas fa-search"></i></button>
        </div>
        <div class="ftDetailsInner">
          <div class="row g-3" id="food_items_section"> </div>
        </div>
      </div>
    </div>
    <div class="filterTableRight">
      <div class="filterTableInner">
        <h4>{{$data['booking_info']->table->table_name}}</h4>
        <ul class="d-flex flex-wrap justify-content-between">
          <li><span><img src="{{ url('assets/admin/images/user.png') }}" alt=""></span> Mr. Subho Saha</li>
          <li><span><img src="{{ url('assets/admin/images/call.png') }}" alt=""></span> 9830012345</li>
          <li><a href="javascript:;"><img src="{{ url('assets/admin/images/plus.png') }}" alt=""></a></li>
        </ul>
        <div class="table-responsive whiteBg ftiTable">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sl</th>
                <th scope="col" class="text-center">Mame</th>
                <th scope="col" class="text-center">Size</th>
                <th scope="col" class="text-center">Rate</th>
                <th width="120" scope="col" class="text-center">Qty</th>
                <th scope="col" class="text-center">Price</th>
                <th scope="col" class="text-center"></th>
              </tr>
            </thead>
            <tbody id="table_cart_items_record_sec">
            </tbody>
          </table>
        </div>
        <div class="table-responsive whiteBg" id="total_quantity_mrp_section" style="display:none;">
          <table class="table">
            <thead>
              <tr>
                <th scope="col" width="65%">QTY : <span id="total_quantity">0</span></th>
                <th scope="col" class="text-right">Total:</th>
                <th scope="col" class="text-right" id="total_mrp">0</th>
                <input type="hidden" name="total_quantity" id="input-total_quantity" value="0">
                <input type="hidden" name="total_mrp" id="input-total_mrp" value="0">
              </tr>
            </thead>
          </table>
        </div>
        <div class="w-100 printBill" id="print_bill_section" style="display:none;">
          <ul class="d-flex mb-0">
            <li class="col"><a href="javascript:;" class="koPrintBtn">KO Print</a></li>
            <li class="col"><a href="javascript:;">Print Bill</a></li>
            <li class="col"><a href="javascript:;" class="payBtn">Pay</a></li>
          </ul>
        </div>
        <form method="post" action="{{ route('admin.pos.print_ko_product') }}" class="needs-validation" id="ko_print-product-form" novalidate enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="floor_id" value="{{$data['booking_info']->floor_id}}">
        <input type="hidden" name="table_id" value="{{$data['booking_info']->table_id}}">
        <input type="hidden" name="waiter_id" value="{{$data['booking_info']->waiter_id}}">
        <input type="hidden" name="table_booking_id" value="{{$data['booking_info']->id}}">
        
        <div id="ko_print_sec">
        <!--<div id="ko_print_product_107" data-id="1"><input type="hidden" name="product_id[]" value="859"><input type="hidden" name="size_price_id[]" value="107"><input type="hidden" name="branch_stock_product_id[]" value="12"><input type="hidden" name="product_type[]" value="liquor"><input type="hidden" name="product_name[]" value="Best Taste"><input type="hidden" name="product_size[]" value="30 ml"><input type="hidden" name="product_qty[]" id="ko_product_qty_107" value="1"></div>-->
        <!--<input type="submit" value="dddd" />-->
        </div>
        </form>  
      </div>
    </div>
  </div>
</section>

<section class="payWrap"> <span class="payWrapCloseBtn paymentModalCloseBtn"><i class="fas fa-times-circle"></i></span>
  <div class="p-5">
    <div class="row">
      <div class="payWrapLeft">
        <div class="pmMenu">
          <ul>
            <li><a href="javascript:;" class="active p-method-tab" data-type="cash"><span><img src="{{ url('assets/admin/images/cash.png') }}" alt=""></span> Cash</a></li>
            <li><a href="javascript:;" class="p-method-tab" data-type="card"><span><img src="{{ url('assets/admin/images/card-1.png') }}" alt=""></span> Card</a></li>
            <li><a href="javascript:;" class="p-method-tab" data-type="gPay"><span><img src="{{ url('assets/admin/images/google-pay.png') }}" alt=""></span> Phonepe / Gpay</a></li>
            <li><a href="javascript:;" class="p-method-tab" data-type="coupon"><span><img src="{{ url('assets/admin/images/coupon.png') }}" alt=""></span> Coupon</a></li>
            <li><a href="javascript:;" class="p-method-tab" data-type="credit_card"><span><img src="{{ url('assets/admin/images/credit-card.png') }}" alt=""></span> Credit</a></li>
            <li><a href="javascript:;" class="p-method-tab" data-type="multiple_pay"><span><img src="{{ url('assets/admin/images/pay-per-click.png') }}" alt=""></span> Multiple Pay</a></li>
          </ul>
        </div>
      </div>
      <div class="payWrapRight">
        <div class="pmDetails">
          <div class="cashOption tab_sec" id="cash_payment_sec">
            <div class="cashOptionTop">
              <ul class="row justify-content-center">
                <li class="col-lg-4 col-md-4 col-sm-6 col-12">
                  <div class="mb-3 cashOptionTopBox">
                    <label for="due_amount_tendering-input" class="form-label">Due Ammout</label>
                    <input type="text" class="form-control tendering-input" id="due_amount_tendering" value="0" readonly="readonly" disabled="disabled"/>
                  </div>
                </li>
                <li class="col-lg-4 col-md-4 col-sm-6 col-12">
                  <div class="mb-3 cashOptionTopBox">
                    <label for="tendered_amount" class="form-label">Tendered</label>
                    <input type="text" class="form-control tendering-input" id="tendered_amount" value="0" onkeypress="return check_character(event);">
                  </div>
                </li>
                <li class="col-lg-4 col-md-4 col-sm-6 col-12">
                  <div class="mb-3 cashOptionTopBox">
                    <label for="tendered_change_amount-input" class="form-label">Change</label>
                    <input type="text" class="form-control tendering-input" id="tendered_change_amount" value="0" readonly="readonly" disabled="disabled"/>
                  </div>
                </li>
              </ul>
            </div>
            <div class="cashOptionBtm text-center">
              <table class="table table-bordered mb-0">
                <tbody>
                  <tr>
                    <td width="20%"><a href="javascript:;" class="tendered_number_btn" data-id="1">1</a></td>
                    <td width="20%"><a href="javascript:;" class="tendered_number_btn" data-id="2">2</a></td>
                    <td width="20%"><a href="javascript:;" class="tendered_number_btn" data-id="3">3</a></td>
                    <td width="20%"><a href="javascript:;" class="tendered_plus_number_btn" data-id="5">+5</a></td>
                    <td width="20%"><a href="javascript:;" class="tendered_plus_number_btn" data-id="100">+100</a></td>
                  </tr>
                  <tr>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="4">4</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="5">5</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="6">6</a></td>
                    <td><a href="javascript:;" class="tendered_plus_number_btn" data-id="10">+10</a></td>
                    <td><a href="javascript:;" class="tendered_plus_number_btn" data-id="500">+500</a></td>
                  </tr>
                  <tr>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="7">7</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="8">8</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="9">9</a></td>
                    <td><a href="javascript:;" class="tendered_plus_number_btn" data-id="20">+20</a></td>
                    <td><a href="javascript:;" class="tendered_plus_number_btn" data-id="2000">+2000</a></td>
                  </tr>
                  <tr>
                    <td><a href="javascript:;" class="tendered_number_reset">C</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="0">0</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id=".">.</a></td>
                    <td><a href="javascript:;" class="tendered_plus_number_btn" data-id="50">+50</a></td>
                    <td><a href="javascript:;" class="tendered_number_btn" data-id="-1"><i class="fas fa-times-circle"></i></a></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-center">
              <ul class="d-flex">
                <li class="col-auto">
                  <button type="button" class="saveBtn-2" id="calculate_cash_payment_btn">Submit</button>
                </li>
                <li class="col-auto"><a href="javascript:;" class="saveBtnBdr paymentModalCloseBtn">Cancel</a></li>
              </ul>
            </div>
          </div>
          <div class="noteType tab_sec" style="display:none" id="rupee_payment_sec">
            <div class="cashOptionTop">
              <ul class="row justify-content-center">
                <li class="col-12">
                  <div class="mb-3 cashOptionTopBox">
                    <label for="rupee_due_amount_tendering" class="form-label">Due Ammout</label>
                    <span style="color: #1c0a6b;" id="rupee_due_amount_tendering">0</span> </div>
                    <input type="hidden" id="rupee_due_amount_tendering-input" value="0">
                  
                </li>
              </ul>
            </div>
            <div class="rupeeTableMdArea">
              <div class="row">
                <div class="col-6">
                  <div class="rupeeTableMd">
                    <table class="table noBdr">
                      <tbody>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/2000.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_2000-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_2000" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_2000">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/500.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_500-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_500" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_500">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/200.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_200-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_200" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_200">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/100.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_100-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_100" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_100">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/50.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_50-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_50" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_50">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/20.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_20-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_20" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_20">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/10.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_10-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_10" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_10">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/5.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_5-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_5" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_5">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/2.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_2-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_2" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_2">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/1.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="rupee_1-input" data-type="note" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="rupee_1" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-rupee_1">0</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-6">
                  <div class="rupeeTableMd forCoin">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/c-10.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="coin_10-input" data-type="coin" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="coin_10" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-coin_10">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/c-5.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="coin_5-input" data-type="coin" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="coin_5" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-coin_5">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/c-2.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="coin_2-input" data-type="coin" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="coin_2" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-coin_2">0</td>
                        </tr>
                        <tr>
                          <td><img src="{{ url('assets/admin/images/c-1.jpg') }}" alt=""></td>
                          <td>x</td>
                          <td><input type="text" id="coin_1-input" data-type="coin" class="input-1 rupee_count_input" onkeypress="return check_character(event);"></td>
                          <input type="hidden" id="coin_1" class="input-1 rupee_count">
                          <td>=</td>
                          <td id="amount_per_note-coin_1">0</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center">
              <ul class="d-flex">
                <li class="col-auto">
                  <button type="button" class="saveBtn-2" onclick="final_payment_submit('cash')">Submit</button>
                </li>
                <li class="col-auto"><a href="javascript:;" class="saveBtnBdr" onclick="backToLink('cash_payment_sec','p_tap')">Back</a></li>
              </ul>
            </div>
          </div>
          <div class="applyCoupon tab_sec" id="coupon_payment_sec" style="display: none;">
            <div class="applyCouponTop">
              <h3>Apply Coupon</h3>
              <div class="relative applyCouponInput">
                <input type="text" placeholder="Please Type Coupon Code" class="input-2"/>
                <a href="#" class="ApplyBtn">Apply</a> </div>
            </div>
            <div class="applyCouponBtm">
              <div class="mb-3 text-center invoiceBalance-2"> <span>Invoice Balance : 160</span> </div>
              <form action="get">
                <div class="mb-3">
                  <input type="text" class="form-control input-2" id="" placeholder="Coupon Name">
                </div>
                <div class="mb-3">
                  <input type="text" class="form-control input-2" id="" placeholder="Customer Coupon">
                </div>
                <div class="mb-3">
                  <ul class="d-flex">
                    <li>
                      <button type="button" class="btn btn-primary">Submit</button>
                    </li>
                    <li><a href="#" class="btn btn-outline-secondary ml-3">Cancel</a></li>
                  </ul>
                </div>
              </form>
            </div>
          </div>
          <div class="applyCoupon tab_sec" id="card_payment_sec" style="display: none;">
            <div class="applyCouponTop">
              <h3>Card Details</h3>
              <form action="get">
                <div class="mb-3">
                  <label for="" class="form-label">Payment Account</label>
                  <select class="form-select">
                    <option>Disabled select</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="" class="form-label">Customer Bank Name</label>
                  <input type="text" class="form-control input-2" id="" placeholder="">
                </div>
                <div class="mb-3">
                  <label for="" class="form-label">Card Payment Amount</label>
                  <input type="text" class="form-control input-2" id="" placeholder="">
                </div>
                <div class="mb-3">
                  <label for="" class="form-label">Card Holder Name</label>
                  <input type="text" class="form-control input-2" id="" placeholder="">
                </div>
                <div class="mb-3">
                  <label for="" class="form-label">Card transaction No</label>
                  <input type="text" class="form-control input-2" id="" placeholder="">
                </div>
                <div class="mb-3">
                  <button type="button" class="saveBtn-2">Finalize Payment</button>
                </div>
              </form>
            </div>
          </div>
          <div class="paymentOption tab_sec" id="gPay_payment_sec" style="display: none;">
            <div class="paymentOptionTop">
              <ul class="row">
                <li><a href="#" class="active"><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/paytm.jpg" alt=""></a></li>
                <li><a href="#"><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/phonepay.jpg" alt=""></a></li>
                <li><a href="#"><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/gpay.jpg" alt=""></a></li>
                <li><a href="#"><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/upi.jpg" alt=""></a></li>
              </ul>
            </div>
            <div class="paymentOptionInputBox">
              <form action="get">
                <div class="mb-3">
                  <input type="text" placeholder="" class="input-2 paymentOptionInput"/>
                </div>
                <div class="d-flex justify-content-center">
                  <ul class="d-flex">
                    <li class="col-auto">
                      <button type="button" class="saveBtn-2">Submit</button>
                    </li>
                    <li class="col-auto"><a href="#" class="saveBtnBdr">Cancel</a></li>
                  </ul>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection



@section('scripts') 
<script>
	var stock_type	= "{{$data['stock_type']}}";
</script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script> 
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> 
<script src="{{ url('assets/admin/js/bar_pos.js') }}"></script> 
@endsection 