<!-- Main Sidebar Container -->

<aside class="main-sidebar sidebar-dark-primary elevation-4"> 
  <!-- Brand Logo --> 
  <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center"> <img src="{{ asset('assets/img/fire-logo.png') }}" alt="Logo" class="brand-image img-circle"> <span class="brand-text font-weight-light"> <img class="img-block logo-dark" src="{{ asset('assets/img/text-logo.png') }}" alt=""> </span> </a> 
  
  <!-- Sidebar -->
  <div class="sidebar"> 
    <!-- Sidebar user panel (optional) --> 
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"> <a href="{{ route('admin.dashboard') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.dashboard') active @endif"> <i class="nav-icon fas fa-tachometer-alt"></i>
          <p> Dashboard </p>
          </a> </li>
        @if (Auth::user()->get_role->tag != 'normal_user')
        @if (Auth::user()->get_role->tag === 'admin')
        @endif
        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.customer') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.customer') !== false) parent-active @endif"> <i class="fas fa-user nav-icon"></i>
          <p>Customer <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"> <a href="{{ route('admin.customer.list') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.customer.list') active @endif"> <i class="fas fa-list nav-icon"></i>
              <p>List Customer</p>
              </a> </li>
            <li class="nav-item"> <a href="{{ route('admin.customer.add') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.customer.add') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Add Customer</p>
              </a> </li>
          </ul>
        </li>
        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.supplier') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.supplier') !== false) parent-active @endif"> <i class="fas fa-user nav-icon"></i>
          <p>Supplier <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"> <a href="{{ route('admin.supplier.list') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.supplier.list') active @endif"> <i class="fas fa-list nav-icon"></i>
              <p>List Supplier</p>
              </a> </li>
            <li class="nav-item"> <a href="{{ route('admin.supplier.add') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.supplier.add') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Add Supplier</p>
              </a> </li>
          </ul>
        </li>
        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.product') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.supplier') !== false) parent-active @endif"> <i class="fas fa-user nav-icon"></i>
          <p>Products Profile <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"> <a href="{{ route('admin.product.list') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.product.list') active @endif"> <i class="fas fa-list nav-icon"></i>
              <p>List Products</p>
              </a> </li>
            <li class="nav-item"> <a href="{{ route('admin.product.add') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.product.add') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Add Products</p>
              </a> </li>
          </ul>
        </li>
        
        <!--<li class="nav-item"> <a href="{{ route('admin.dashboard') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.dashboard') active @endif"> <i class="nav-icon fas fa-bar-chart-o"></i>
          <p> Report </p>
          </a> </li>-->
          
          
          <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.purchase') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.purchase') !== false) parent-active @endif"> <i class="fas fa-user nav-icon"></i>
          <p>Purchase <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"> <a href="{{ route('admin.purchase.inward_stock') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.purchase.inward_stock') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Purchase Order</p>
              </a> </li>
            <!--<li class="nav-item"> <a href="{{ route('admin.purchase.material_inward') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.purchase.material_inward') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Material Inward</p>
              </a> </li>-->
            <!--<li class="nav-item"> <a href="{{ route('admin.purchase.supplier_bill') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.purchase.supplier_bill') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Supplier Bill</p>
              </a> </li>-->
            <!--<li class="nav-item"> <a href="{{ route('admin.purchase.debitnote') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.purchase.debitnote') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Debit Note</p>
              </a> </li>-->
          </ul>
        </li>

        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.pos') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.pos') !== false) parent-active @endif"> <i class="fas fa-user nav-icon"></i>
          <p>Pos <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"> <a href="{{ route('admin.pos.pos_create') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.pos.pos_create') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Create Order</p>
              </a> </li>
           
          </ul>
        </li>
          
        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.restaurant') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.restaurant') !== false) parent-active @endif">
          <i class="fas fa-utensils nav-icon"></i>
          <p>Restaurant <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.restaurant.waiter') !== false) menu-open @endif"">
              <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.restaurant.waiter') !== false) parent-active @endif">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                      Waiter
                    <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview" >
                <li class="nav-item"> 
                  <a href="{{ route('admin.restaurant.waiter.list') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.restaurant.waiter.list') active @endif"> <i class="fas fa-list nav-icon"></i>
                  <p>List Waiter</p>
                  </a> 
                </li>
                <li class="nav-item"> 
                  <a href="{{ route('admin.restaurant.waiter.add') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.restaurant.waiter.add') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
                  <p>Add Waiter</p>
                  </a> 
                </li>
              </ul>
            </li>
            <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.restaurant.table') !== false) menu-open @endif"">
              <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.restaurant.table') !== false) parent-active @endif">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                      Manage Room/Table
                    <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview" >
                <li class="nav-item"> 
                  <a href="{{ route('admin.restaurant.table.list') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.restaurant.table.list') active @endif"> <i class="fas fa-list nav-icon"></i>
                  <p>List Room/Table</p>
                  </a> 
                </li>
                <li class="nav-item"> 
                  <a href="{{ route('admin.restaurant.table.add') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.restaurant.table.add') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
                  <p>Add Room/Table</p>
                  </a> 
                </li>
              </ul>
            </li>
          </ul>
        </li>  
          
        
        <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.report') !== false) menu-open @endif"> <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.report') !== false) parent-active @endif"> <i class="fas fa-flag nav-icon"></i>
          <p>Report <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) menu-open @endif"">
              <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) parent-active @endif">
                  <i class="nav-icon fas fa-store"></i>
                  <p>
                      Counter
                      <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview" >
                <li class="nav-item"> <a href="{{ route('admin.report.purchase') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.purchase') active @endif"> 
                  <i class="fas fa-shopping-bag nav-icon"></i>
                  <p>Purchase</p>
                  </a> 
                </li>
                <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) menu-open @endif"">
                  <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) parent-active @endif">
                      <i class="nav-icon fas fa-truck-loading"></i>
                      <p>
                          Sales
                          <i class="right fas fa-angle-left"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview" >
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.item') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.item') active @endif"> <i class="fas fa-list nav-icon"></i>
                      <p>Sales</p>
                      </a> 
                    </li>
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.report.product.wise') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.report.product.wise') active @endif"> <i class="fas fa-list nav-icon"></i>
                      <p>Product Wise Sales</p>
                      </a> 
                    </li>
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.product') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.product') active @endif"> <i class="fas fa-list nav-icon"></i>
                      <p>Sales Product</p>
                      </a> 
                    </li>
                  </ul>
                </li>
                <li class="nav-item"> <a href="{{ route('admin.report.inventory') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.inventory') active @endif"> 
                  <i class="fas fa-warehouse nav-icon"></i>
                  <p>Inventory</p>
                  </a> 
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-glass-cheers"></i>
                  <p>
                      Bar
                      <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview" >
                {{-- <li class="nav-item @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) menu-open @endif"">
                  <a href="#" class="nav-link @if (strpos(Route::currentRouteName(), 'admin.report.sales') !== false) parent-active @endif">
                      <i class="nav-icon fas fa-circle"></i>
                      <p>
                          Sales
                          <i class="right fas fa-angle-left"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview" >
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.item') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.item') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
                      <p>Sales</p>
                      </a> 
                    </li>
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.report.product.wise') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.report.product.wise') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
                      <p>Product Wise Sales</p>
                      </a> 
                    </li>
                    <li class="nav-item"> 
                      <a href="{{ route('admin.report.sales.product') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.sales.product') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
                      <p>Sales Product</p>
                      </a> 
                    </li>
                  </ul>
                </li> --}}
              </ul>
            </li>
           
            <!--<li class="nav-item"> <a href="{{ route('admin.report.inventory') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.inventory') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Inventory</p>
              </a> </li>-->
            <!--<li class="nav-item"> <a href="{{ route('admin.report.reminders') }}" class="nav-link @if (\Route::currentRouteName() == 'admin.report.reminders') active @endif"> <i class="fas fa-plus-circle nav-icon"></i>
              <p>Reminders</p>
              </a> </li>-->
          </ul>
        </li>
        @endif
      </ul>
    </nav>
    <!-- /.sidebar-menu --> 
  </div>
  <!-- /.sidebar --> 
</aside>
