@extends('layouts.admin')
@section('admin-content')
<div class="row">
  <div class="col-12">
    <form method="post" action="{{ route('admin.customer.add') }}" class="needs-validation" novalidate enctype="multipart/form-data">
      @csrf
      <div class="card">
        <div class="row">
          <x-alert />
          <div class="col-md-6">
            <div class="form-group">
              <label for="company_name" class="form-label">Company Name</label>
              <input type="text" class="form-control admin-input" id="company_name" name="company_name" value="{{ $data['company_name'] }}" required autocomplete="off">
              @error('company_name')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="company_licensee" class="form-label">Licensee Id</label>
              <input type="text" class="form-control admin-input" id="company_licensee" name="company_licensee" value="{{ $data['company_licensee'] }}" readonly="readonly" required="required">
              @error('company_licensee')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="company_mobile" class="form-label">Mobile No</label>
              <input type="text" class="form-control admin-input" id="company_mobile" name="company_mobile" value="{{ $data['phone'] }}"  autocomplete="off">
              @error('company_mobile')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="company_email" class="form-label">Email</label>
              <input type="text" class="form-control admin-input" id="company_email" name="company_email" value="{{ $data['admin_email'] }}"  autocomplete="off">
              @error('company_email')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="alert_product_qty" class="form-label">GSTIN</label>
              <input type="text" class="form-control admin-input" id="company_gstin" name="company_gstin" value="{{ old('company_gstin') }}"  autocomplete="off">
              @error('company_gstin')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="company_address" class="form-label">Company Address</label>
              <input type="text" class="form-control admin-input" id="company_address" name="company_address" value="{{ $data['admin_email'] }}" required autocomplete="off">
              @error('company_address')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="company_address" class="form-label">Address 2</label>
              <input type="text" class="form-control admin-input" id="company_address2" name="company_address2" value="{{ $data['admin_email'] }}" required autocomplete="off">
              @error('company_address2')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="company_address" class="form-label">Address 3</label>
              <input type="text" class="form-control admin-input" id="company_address3" name="company_address3" value="{{ $data['admin_email'] }}" required autocomplete="off">
              @error('company_address3')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="customer_country_id" class="form-label">Country</label>
              <select name="customer_country_id" id="customer_country_id" class="form-control form-inputtext">
                <option value="">Select Country</option>
                <option value="1">India</option>
              </select>
              @error('customer_address')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="customer_state_id" class="form-label">State / Region</label>
              <select name="customer_state_id" id="customer_state_id" class="form-control form-inputtext">
                <option value="">Select State</option>
                <option value="1">West Bengal</option>
              </select>
              @error('customer_address')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="customer_city" class="form-label">City / Town</label>
              <input type="text" class="form-control admin-input" id="customer_city" name="customer_city" value="{{ $data['admin_email'] }}" autocomplete="off">
              @error('customer_city')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="customer_pincode" class="form-label">Pin / Zip Code</label>
              <input type="text" class="form-control admin-input" id="customer_pincode" name="customer_pincode" value="{{ old('customer_pincode') }}" autocomplete="off">
              @error('customer_pincode')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="customer_address" class="form-label">Password</label>
              <input type="text" class="form-control admin-input" id="password" name="password" value="{{ old('password') }}" autocomplete="off">
              @error('password')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="customer_area" class="form-label">Re-enter Password</label>
              <input type="text" class="form-control admin-input" id="password_confirm" name="password_confirm" value="{{ old('password_confirm') }}" autocomplete="off">
              @error('password_confirm')
              <div class="error admin-error">{{ $message }}</div>
              @enderror </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="row">
          <div class="row">
            <div class="col-12">
              <button class="commonBtn-btnTag" type="submit">Submit </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
@endsection 