@extends('layouts.sidebar_collapse_admin')
<style>
.content-wrapper{
	background: #fff !important;
	height: auto !important;
}
.content-header {
	display: none !important;
}
.tbl_btn{
	cursor:pointer;
}
</style>
@section('admin-content')
<section class="tablePage">
  <div class="rightPannelTop">
    <div class="row">
      <div class="col-lg-4 col-md-3 col-sm-3 col-12">
        <div class="tabBox"> <a href="#" class="active"><span><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/dine-in.png" alt=""></span> Dine  in <i class="fas fa-bell"></i></a> </div>
      </div>
      <div class="col-lg-4 col-md-3 col-sm-3 col-12">
        <div class="tabBox"> <a href="#"><span><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/take-way.png" alt=""></span> Take Away</a> </div>
      </div>
      <div class="col-lg-4 col-md-3 col-sm-3 col-12">
        <div class="tabBox"> <a href="#"><span><img src="https://pos.subho.aqualeafitsol.com/assets/admin/images/online.png" alt=""></span> online</a> </div>
      </div>
    </div>
  </div>
  <div class="tableIndicate d-flex justify-content-center">
    <ul class="d-flex flex-wrap">
      <li><span style="background: #9ab3bd;"></span> Blank Table</li>
      <li><span style="background: #00bff3;"></span> Running Table</li>
      <!--<li><span style="background: #39b54a;"></span> Printed Table</li>
      <li><span style="background: #fdc689;"></span> Paid Table</li>--> 
      <!--<li><span style="background: #f7941d;"></span>Reserved Table</li>-->
    </ul>
  </div>
  <div class="d-flex flex-wrap">
    <div class="leftPannel"> @if(count($data['floor_list'])>0)
      <ul class="nav flex-column" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        @foreach($data['floor_list'] as $key => $row)
        <li><a href="javascript:;" id="v-floor-{{$row->id}}-tab" data-bs-toggle="pill" data-bs-target="#floor-tab-{{$row->id}}" role="tab" aria-controls="floor-tab-{{$row->id}}" aria-selected="true" data-id="{{$row->id}}" class="floor_tab_btn @if ($key === 0) active @endif"><span><img src="{{ url('assets/admin/images/floor.png') }}" alt=""></span> {{$row->title}}</a></li>
        @endforeach
      </ul>
      @endif </div>
    <div class="rightPannel">
      <div class="tab-content" id="v-pills-tabContent"> @foreach($data['floor_list'] as $key => $row)
        <div class="tab-pane fade show @if ($key === 0) active @endif" id="floor-tab-{{$row->id}}" role="tabpanel" aria-labelledby="v-floor-{{$row->id}}-tab">
          <div class="row g-3" id="floor_table_sec_{{$row->id}}">
           @foreach($data['tables'] as $key => $tbl_row)
           @php
           	$tbl_color = 'bgGray';
            if ($tbl_row['status'] == 2) {
            	$tbl_color = 'bgBlue';
            } else if ($tbl_row['status'] == 3) {
            	$tbl_color = 'bgSafron';
            }
           @endphp
           <div class="col-lg-2 col-md-2 col-sm-6 col-12 tbl_btn" data-tblid="{{$tbl_row['table_id']}}" data-floorid="{{$tbl_row['floor_id']}}" id="table_{{$tbl_row['floor_id']}}_{{$tbl_row['table_id']}}"><div class="tableBox {{$tbl_color}}"><div class="tableBoxTop d-flex align-items-center justify-content-between"><h5>{{$tbl_row['table_name']}}</h5><div class="countdownArea d-flex align-items-center"> <i class="far fa-clock"></i><div class="countdown">00:00</div></div></div>@if ($tbl_row['status'] != 1) <div class="tableBoxBtm"><ul><li><span>{{$tbl_row['waiter_name']}}</span></li> <li>Amount: <span>{{$tbl_row['total_amount']}}</span></li></ul></div> @endif</div></div>
           @endforeach
            
            
          </div>
        </div>
        @endforeach 
        <!--<div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">2</div>
        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">3</div>
        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">4</div>--> 
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="waiterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="setWaiterModalLabel">Set Waiter</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" action="" class="needs-validation" id="waiter-table-form" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="table_id" name="table_id" />
          <input type="hidden" id="floor_id" name="floor_id" />
          
          <div class="form-group">
            <label for="message-text" class="col-form-label">Waiter:</label>
            <select class="form-control custom-select form-control-select" id="waiter_id" name="waiter_id" required="required">
              <option value="">Select Waiter</option>
              
              @foreach($data['waiter_list'] as $key => $row)
              
              <option value="{{$row->id}}">{{$row->name}}</option>
              
              @endforeach
            
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection



@section('scripts') 
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> 
<script src="{{ url('assets/admin/js/bar_pos.js') }}"></script> 
<script>
/*  var timer2 = "5:01";
  var interval = setInterval(function() {


    var timer = timer2.split(':');
    //by parsing integer, I avoid all extra string processing
    var minutes = parseInt(timer[0], 10);
    var seconds = parseInt(timer[1], 10);
    --seconds;
    minutes = (seconds < 0) ? --minutes : minutes;
    seconds = (seconds < 0) ? 59 : seconds;
    seconds = (seconds < 10) ? '0' + seconds : seconds;
    //minutes = (minutes < 10) ?  minutes : minutes;
    $('.countdown').html(minutes + ':' + seconds);
    if (minutes < 0) clearInterval(interval);
    //check if both minutes and seconds are 0
    if ((seconds <= 0) && (minutes <= 0)) clearInterval(interval);
    timer2 = minutes + ':' + seconds;
  }, 1000)*/;
</script> 
@endsection 