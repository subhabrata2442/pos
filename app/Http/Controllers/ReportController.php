<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderSupplier;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseProduct;
use App\Models\Supplier;
use App\Models\PurchaseInwardStock;
use App\Models\InwardStockProducts;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use DB;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Helper\Media;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SellInwardStock;
use App\Models\SellStockProducts;
use App\Models\Size;
use App\Models\Subcategory;
use Carbon\Carbon;
Use Illuminate\Support\Facades\Response;
Use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
		
        DB::beginTransaction();
        try {
            $data = [];
			
            $data['heading'] 		= 'Add Order';
            $data['breadcrumb'] 	= ['Stock', 'Purchase Order', 'Add'];
            $data['supplier'] 		= Supplier::all();
            $data['product'] 		= Product::all();
			
            return view('admin.report.sales_report', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	public function invoice_report(Request $request)
    {
        try {
            if ($request->ajax()) {
                $purchaseOrder = PurchaseOrder::where('status', '0')->orderBy('id', 'desc')->get();
                return DataTables::of($purchaseOrder)
                    ->addColumn('sup_code', function ($row) {
                        return $row->supplier_dtl->sup_code;
                    })
                    ->addColumn('order_no', function ($row) {
                        return '<a class="td-anchor" href="' . route('admin.stock.purchase-order.edit', [base64_encode($row->id)]) . '">' . $row->order_no . '</a>';
                    })
                    ->addColumn('sup_name', function ($row) {
                        return $row->supplier_dtl->sup_name;
                    })
                    ->addColumn('order_date', function ($row) {
                        return date('d-m-Y', strtotime($row->order_date));
                    })
                    ->addColumn('delivery_date', function ($row) {
                        return date('d-m-Y', strtotime($row->delivery_date));
                    })
                    ->addColumn('delivery_name', function ($row) {
                        return $row->delivery_name;
                    })
                    ->addColumn('city', function ($row) {
                        return $row->city;
                    })
                    ->addColumn('state', function ($row) {
                        return $row->state;
                    })
                    ->addColumn('post_code', function ($row) {
                        return $row->post_code;
                    })
                    ->addColumn('status', function ($row) {
                        $status = '';
                        if ($row->status == 3) {
                            $status = '<span class="badge badge-info">Receipt</span>';
                        }
                        if ($row->status == 2) {
                            $status = '<span class="badge badge-prinary">Placed</span>';
                        }
                        if ($row->status == 1) {
                            $status = '<span class="badge badge-success">completed</span>';
                        }
                        if ($row->status == 0) {
                            $status = '<span class="badge badge-warning">Pending</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        $dropdown = '<a class="dropdown-item" href="' . route('admin.stock.purchase-order.edit', [base64_encode($row->id)]) . '">Edit</a>
                        <a class="dropdown-item delete-item" href="#" id="delete_product" data-url="' . route('admin.stock.purchase-order.delete', [base64_encode($row->id)]) . '">Delete</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.view', [base64_encode($row->id)]) . '" >View</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.complete', [base64_encode($row->id)]) . '" >Complete Order</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.printInvoice', [base64_encode($row->id)]) . '" >Download Invoice</a>
                        ';

                        $btn = '<div class="dropdown">
                                    <div class="actionList " id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        ' . $dropdown . '
                                    </div>
                                </div>
                                ';

                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'order_no'])
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Purchase Order List';
            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'List'];
			
            return view('admin.report.sales_invoice_report', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	
    public function list(Request $request)
    {
        try {
            if ($request->ajax()) {
                $purchaseOrder = PurchaseOrder::where('status', '0')->orderBy('id', 'desc')->get();
                return DataTables::of($purchaseOrder)
                    ->addColumn('sup_code', function ($row) {
                        return $row->supplier_dtl->sup_code;
                    })
                    ->addColumn('order_no', function ($row) {
                        return '<a class="td-anchor" href="' . route('admin.stock.purchase-order.edit', [base64_encode($row->id)]) . '">' . $row->order_no . '</a>';
                    })
                    ->addColumn('sup_name', function ($row) {
                        return $row->supplier_dtl->sup_name;
                    })
                    ->addColumn('order_date', function ($row) {
                        return date('d-m-Y', strtotime($row->order_date));
                    })
                    ->addColumn('delivery_date', function ($row) {
                        return date('d-m-Y', strtotime($row->delivery_date));
                    })
                    ->addColumn('delivery_name', function ($row) {
                        return $row->delivery_name;
                    })
                    ->addColumn('city', function ($row) {
                        return $row->city;
                    })
                    ->addColumn('state', function ($row) {
                        return $row->state;
                    })
                    ->addColumn('post_code', function ($row) {
                        return $row->post_code;
                    })
                    ->addColumn('status', function ($row) {
                        $status = '';
                        if ($row->status == 3) {
                            $status = '<span class="badge badge-info">Receipt</span>';
                        }
                        if ($row->status == 2) {
                            $status = '<span class="badge badge-prinary">Placed</span>';
                        }
                        if ($row->status == 1) {
                            $status = '<span class="badge badge-success">completed</span>';
                        }
                        if ($row->status == 0) {
                            $status = '<span class="badge badge-warning">Pending</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        $dropdown = '<a class="dropdown-item" href="' . route('admin.stock.purchase-order.edit', [base64_encode($row->id)]) . '">Edit</a>
                        <a class="dropdown-item delete-item" href="#" id="delete_product" data-url="' . route('admin.stock.purchase-order.delete', [base64_encode($row->id)]) . '">Delete</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.view', [base64_encode($row->id)]) . '" >View</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.complete', [base64_encode($row->id)]) . '" >Complete Order</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.printInvoice', [base64_encode($row->id)]) . '" >Download Invoice</a>
                        ';

                        $btn = '<div class="dropdown">
                                    <div class="actionList " id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        ' . $dropdown . '
                                    </div>
                                </div>
                                ';

                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'order_no'])
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Purchase Order List';
            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'List'];
            return view('admin.purchase_order.list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function complete_order_list(Request $request)
    {
        try {
            if ($request->ajax()) {
                $purchaseOrder = PurchaseOrder::where('status', '1')->orderBy('id', 'desc')->get();
                return DataTables::of($purchaseOrder)
                    ->addColumn('sup_code', function ($row) {
                        return $row->supplier_dtl->sup_code;
                    })
                    ->addColumn('order_no', function ($row) {
                        return $row->order_no;
                    })
                    ->addColumn('sup_name', function ($row) {
                        return $row->supplier_dtl->sup_name;
                    })
                    ->addColumn('order_date', function ($row) {
                        return date('d-m-Y', strtotime($row->order_date));
                    })
                    ->addColumn('delivery_date', function ($row) {
                        return date('d-m-Y', strtotime($row->delivery_date));
                    })
                    ->addColumn('delivery_name', function ($row) {
                        return $row->delivery_name;
                    })
                    ->addColumn('city', function ($row) {
                        return $row->city;
                    })
                    ->addColumn('state', function ($row) {
                        return $row->state;
                    })
                    ->addColumn('post_code', function ($row) {
                        return $row->post_code;
                    })
                    ->addColumn('status', function ($row) {
                        $status = '';
                        if ($row->status == 3) {
                            $status = '<span class="badge badge-info">Receipt</span>';
                        }
                        if ($row->status == 2) {
                            $status = '<span class="badge badge-prinary">Placed</span>';
                        }
                        if ($row->status == 1) {
                            $status = '<span class="badge badge-success">completed</span>';
                        }
                        if ($row->status == 0) {
                            $status = '<span class="badge badge-warning">Pending</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        // $dropdown = '<a class="dropdown-item" href="' . route('admin.stock.purchase-order.edit', [base64_encode($row->id)]) . '">Edit</a>
                        // <a class="dropdown-item delete-item" href="#" id="delete_product" data-url="' . route('admin.stock.purchase-order.delete', [base64_encode($row->id)]) . '">Delete</a>';
                        $dropdown = '<a class="dropdown-item" href="' . route('admin.stock.purchase-order.view', [base64_encode($row->id)]) . '" >View</a>
                        <a class="dropdown-item" href="' . route('admin.stock.purchase-order.printInvoice', [base64_encode($row->id)]) . '" >Download Invoice</a>
                        ';

                        $btn = '<div class="dropdown">
                                    <div class="actionList " id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        ' . $dropdown . '
                                    </div>
                                </div>
                                ';

                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Complete Purchase Order List';
            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'Complete Order'];
            return view('admin.purchase_order.complete_order_list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $id = base64_decode($id);
            // dd($id);
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'supplier_code' => 'required',
                    // 'supplier_invoice_date' => 'required|date',
                    'delivery_name' => 'required',
                    'address_line_1' => 'required',
                    'address_line_2' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'post_code' => 'required',
                    'order_date' => 'required|date',
                    'delivery_date' => 'required|date',
                    // 'discount' => 'required',
                    'product_price' => 'required|array|min:1',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $purchase = PurchaseOrder::find($id)->update([
                    'supplier_id' => $request->supplier_code,
                    'supplier_ref' => $request->supplier_ref,
                    // 'supplier_invoice_date' => $request->supplier_invoice_date,
                    'delivery_name' => $request->delivery_name,
                    'address_one' => $request->address_line_1,
                    'address_two' => $request->address_line_2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'post_code' => $request->post_code,
                    'order_date' => $request->order_date,
                    'delivery_date' => $request->delivery_date,
                    'delivery_charge' => $request->delivery_charge ?? 0,
                    // 'discount' => $request->discount,
                ]);
                PurchaseProduct::where('purchase_order_id', $id)->delete();
                $purchase_product = [];
                $sub_total = $request->delivery_charge ?? 0;
                foreach ($request->product_id as $key => $value) {
                    $purchase_product[$key] = [
                        'purchase_order_id' => $id,
                        'product_id' => $value,
                        'price' => $request->product_price[$key],
                        'qty' => $request->product_qty[$key],
                        'discount' => $request->product_discount[$key],
                        'tax_rate' => $request->product_purchase_tax_rate[$key],
                        'total' => $request->product_sub_total[$key],
                        'comments' => $request->product_comment[$key],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $sub_total += $request->product_sub_total[$key];
                }

                // dd($supplier_product);
                PurchaseProduct::insert($purchase_product);

                PurchaseOrder::find($id)->update([
                    'sub_total' => $sub_total,
                ]);
                DB::commit();
                return redirect()->back()->with('success', 'Purchase order updated successfully');
            }
            $data = [];

            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'Edit'];
            $data['supplier'] = Supplier::all();
            $data['product'] = Product::all();
            $data['purchase_order'] = PurchaseOrder::with('supplier_dtl', 'purchase_product', 'purchase_product.product_dtl')->find($id);
            $data['heading'] = $data['purchase_order']->order_no;
            return view('admin.purchase_order.edit', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function delete(Request $request)
    {
    }
    public function view(Request $request, $id)
    {
        try {
            $id = base64_decode($id);
            $data = [];
            // $data['heading'] = 'View Purchase Order';
            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'View'];
            $data['purchase_order'] = PurchaseOrder::with('supplier_dtl', 'purchase_product', 'purchase_product.product_dtl')->find($id);
            $data['heading'] = $data['purchase_order']->order_no;
            return view('admin.purchase_order.view', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }


    public function get_product_supplier_dtl(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $product_id = $request->product_id;

        $product_dtl = Product::find($product_id);
        $prod_supplier = ProductSupplier::where(['supplier_id' => $supplier_id, 'product_id' => $product_id])->first();
        // dd($prod_supplier, $product_dtl, $supplier_id, $product_id);
        $response = [];

        $response['product_code'] = $product_dtl->product_code;
        $response['product_desc'] = $product_dtl->product_desc;
        $response['availibility'] = $product_dtl->available_qty;
        $response['purchase_tax_rate'] = $product_dtl->purchase_tax_rate;

        if (!empty($prod_supplier)) {
            $response['qty'] = $prod_supplier->supp_min_order_qty;
            $response['price'] = $prod_supplier->supp_purchase_price;
            $response['supplier_product_code'] = $prod_supplier->supp_product_code;
        } else {
            $response['qty'] = $prod_supplier->min_order_qty;
            $response['price'] = $prod_supplier->default_purchase_price;
            $response['supplier_product_code'] = $prod_supplier->product_code;
        }
        return response(['success' => true, 'data' => $response]);
    }

    public function print_invoice(Request $request, $id)
    {
        try {
            $id = base64_decode($id);
            $data = [];
            $data['purchase_order'] = PurchaseOrder::with('supplier_dtl', 'purchase_product', 'purchase_product.product_dtl')->find($id);

            $pdf = PDF::loadView('admin.purchase_order.invoice', compact('data'));

            return $pdf->download('invoice.pdf');
            // return view('admin.purchase_order.invoice', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function complete_order(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $id = base64_decode($id);
            PurchaseOrder::find($id)->update([
                'status' => 1
            ]);
            $purchase_product = PurchaseProduct::where('purchase_order_id', $id)->get();
            foreach ($purchase_product as $key => $value) {
                $product = Product::find($value->product_id);
                $stock = $product->available_qty + $value->qty;
                $product->available_qty = $stock;
                $product->save();
            }
            DB::commit();
            return redirect()->route('admin.stock.purchase-order.CompleteOrderList')->with('success', 'Order completed successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function send_email(Request $request)
    {
        DB::beginTransaction();
        try {
            $to_email = explode(',', $request->to_email)[0];
            $cc_email = explode(',', $request->cc_email);
            $subject = $request->subject;
            $message = $request->message;
            $send_copy = $request->send_copy;

            $data = [];
            $data['subject'] = $subject;
            $data['message'] = $message;
            $data['send_copy'] = $send_copy;

            $data['purchase_order'] = PurchaseOrder::with('supplier_dtl', 'purchase_product', 'purchase_product.product_dtl')->find(1);

            $pdf = PDF::loadView('admin.purchase_order.invoice', compact('data'));
            Storage::put('uploads/purchase_order/invoice-' . date('Y-m-d H:i:s') . '.pdf', $pdf->output());
            // return $pdf->save(public_path() . '/myfile.pdf');

            Mail::to($to_email)->cc($cc_email)->send(new PurchaseOrderSupplier($data));

            DB::commit();
            return response(['success' => true]);
            // return redirect()->route('admin.stock.purchase-order.CompleteOrderList')->with('success', 'Order completed successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

     //purchase report added by palash
     public function purchase(Request $request){
        try {
            if ($request->ajax()) {
                $purchase = PurchaseInwardStock::orderBy('id', 'desc')->get();
                return DataTables::of($purchase)
                    ->addColumn('invoice_no', function ($row) {
                       return '<a class="td-anchor" href="'.route('admin.report.stock_product.list', [base64_encode($row->id)]) .'" target="_blank">' . $row->invoice_no . '</a>';
                   
                    })
                    ->addColumn('inward_date', function ($row) {
                        return date('d-m-Y', strtotime($row->inward_date));
                    })
                    ->addColumn('tp_no', function ($row) {
                        return $row->tp_no;
                    })
                    ->addColumn('purchase_date', function ($row) {
                        return date('d-m-Y', strtotime($row->purchase_date));
                    })
                    ->addColumn('supplier_id', function ($row) {
                        return $row->supplier->company_name;
                    })
                    ->addColumn('total_qty', function ($row) {
                        return $row->total_qty;
                    })
                    ->addColumn('sub_total', function ($row) {
                        return number_format($row->sub_total,2);
                    })

                    ->addColumn('action', function ($row) {
                        $dropdown = '<a class="dropdown-item" href="'.route('admin.purchase.inward_stock.update', [base64_encode($row->id)]) .'">Edit</a>
                        <a class="dropdown-item delete-item" id="delete_inward_stock" href="#" data-url="' . route('admin.purchase.inward-stock.delete', [base64_encode($row->id)]) . '">Delete</a>';

                        $btn = '<div class="dropdown">
                                    <div class="actionList " id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        ' . $dropdown . '
                                    </div>
                                </div>
                                ';

                        return $btn;
                    })
                   
                    ->rawColumns(['invoice_no','action'])
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Purchase Order List';
            $data['breadcrumb'] = ['Stock', 'Purchase Order', 'List'];
			
            return view('admin.report.purchase', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function stockProductList(Request $request,$inward_stock_id){
        
        try {
            if ($request->ajax()) {
                //echo base64_decode($inward_stock_id);die;
                $purchase = InwardStockProducts::where('inward_stock_id',base64_decode($inward_stock_id))->orderBy('id', 'desc')->get();
                return DataTables::of($purchase)

                    ->addColumn('barcode', function ($row) {
                        return $row->product->product_barcode;
                    })
                    ->addColumn('case_qty', function ($row) {
                        return $row->case_qty;
                    })
                    ->addColumn('bottle_case', function ($row) {
                        return $row->bottle_case;
                    })
                    ->addColumn('loose_qty', function ($row) {
                        return $row->loose_qty;
                    })
                    ->addColumn('product_qty', function ($row) {
                        return $row->product_qty;
                    })
                    ->addColumn('free_qty', function ($row) {
                        return $row->free_qty;
                    })
                    /* ->addColumn('created_at', function ($row) {
                        return date('d-m-Y', strtotime($row->created_at));
                    }) */
                    ->addColumn('category', function ($row) {
                        return $row->product->category->name;
                    })
                    ->addColumn('sub_category', function ($row) {
                        return $row->product->subcategory->name;
                    })
                    ->addColumn('brand', function ($row) {
                        return $row->product->brand->name;
                    })
                    ->addColumn('batch_no', function ($row) {
                        return $row->batch_no;
                    })
                    ->addColumn('measure', function ($row) {
                        return $row->size->name;
                    })
                    ->addColumn('strength', function ($row) {
                        return $row->strength;
                    })
                    ->addColumn('bl', function ($row) {
                        return $row->bl;
                    })
                    ->addColumn('lpl', function ($row) {
                        return $row->lpl;
                    })
                    ->addColumn('unit_cost', function ($row) {
                        return '';
                    })
                    ->addColumn('retailer_margin', function ($row) {
                        return $row->retailer_margin;
                    })
                    ->addColumn('round_off', function ($row) {
                        return $row->round_off;
                    })
                    ->addColumn('sp_fee', function ($row) {
                        return $row->sp_fee;
                    })
                    ->addColumn('offer_price', function ($row) {
                        return $row->offer_price;
                    })
                    ->addColumn('product_mrp', function ($row) {
                        return $row->product_mrp;
                    })
                    ->addColumn('total_cost', function ($row) {
                        return number_format($row->total_cost,2) ;
                    })
                    ->rawColumns([])
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Stock Product List';
            $data['breadcrumb'] = ['Stock', 'Product', 'List'];
            $data['inward_stock_id'] = $inward_stock_id;
			//echo  $inward_stock_id;die;
            return view('admin.report.stock_product_list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function invoicePdf(){
        
        $data=[];
        $data['shop_details'] = [
            'name' => 'INDULGENCE',
            'address1'=>'Padarpan Abasan, Block A , Beside Madhyamgram Municipality',
            'address2' => 'Madhyamgram Kolkata - 700129',
            'phone'=>'(+91)93301 41544',
        ];
        $data['customer_details'] = [
            'name'=> 'Farhana Sultana',
            'mobile'=> '7003923969',
            'address'=> 'India',
        ];
        $data['invoice_details'] = [
            'invoice_no'=> 'INV407/22-23',
            'invoice_date'=> '16-08-2022',
            'gstin'=> '19CTIPS9692B1ZZ',
            'place'=> 'West Bengal',
            'branch'=> 'Baguihati',
            'cashier_name'=> 'Mrs Roy Suchandra',
        ];
        $data['items'] = [
            [
                'particulars'=> 'MB LIQ MATTE 12 AS',
                'qty'=> '1',
                'mrp'=> '349',
                'offer_price'=> '349',
                'disc_price'=> '34.9',
                'final_price'=> '314.1',
            ],
            [
                'particulars'=> 'MB LIQ MATTE 15 AS',
                'qty'=> '1',
                'mrp'=> '349',
                'offer_price'=> '349',
                'disc_price'=> '34.9',
                'final_price'=> '314.1',
            ]
        ];
        $data['total'] =[
            'total_qty'=> '2',
            'total_disc'=> '849',
            'total_price'=> '9880',
        ]; 
        
        $data['gst'] =[
            'gst_val' =>'18',
            'taxable_amt'=> '8373.21',
            'cgst_rate'=> '9',
            'cgst_amt'=> '753.59',
            'sgst_rate'=> '9',
            'sgst_amt'=> '753.59',
            'total_amt'=> '9880.39',
        ]; 
        $data['total_amt_in_word'] = ucwords(Media::getIndianCurrency($data['total']['total_price']));
        $data['payment_method'] = 'Cash';
        $pdf = PDF::loadView('admin.pdf.invoice', $data);
        return $pdf->download('invoice.pdf');
    }

    public function salesProduct(Request $request){
        /* if(isset($request->id)){
            echo base64_decode($request->id);die;
        } */
        try {
            if ($request->ajax()) {
                //echo base64_decode($inward_stock_id);die;
                $products = SellStockProducts::where('product_id','!=','');
                
                if(!empty($request->get('start_date')) && !empty($request->get('end_date'))){
                    if($request->get('start_date') == $request->get('end_date')){
                        $products->whereDate('created_at', $request->get('start_date'));
                    }else{
                        $products->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
                    }    
                }
                if(isset($request->id) && !empty($request->id)){
                    $sell_inward_stock_id = base64_decode($request->id);
                    $products->where('inward_stock_id',$sell_inward_stock_id);
                }
                //echo  $products;die;
                $products->orderBy('id', 'desc')->get();
                
                //echo "<pre>";print_r($request->all());die;
                return DataTables::of($products)

                /* ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('date_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains(date('d-m-Y', strtotime($row['created_at'])), date('d-m-Y', strtotime($request->get('date_search')))) ? true : false;
                        });
                    }

                    if (!empty($request->get('search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['created_at']), Str::lower($request->get('search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['name']), Str::lower($request->get('search')))) {
                                return true;
                            }

                            return false;
                        });
                    }

                }) */

                    ->addColumn('product_name', function ($row) {
                        return $row->product_name;
                    })
                    ->addColumn('barcode', function ($row) {
                        return $row->barcode;
                    })
                    ->addColumn('created_at', function ($row) {
                        return date('d-m-Y', strtotime($row->created_at));
                    })
                  
                    ->addColumn('measure', function ($row) {
                        return $row->size->name;
                    })
                    
                    ->addColumn('product_qty', function ($row) {
                        return $row->product_qty;
                    })
                    ->addColumn('product_mrp', function ($row) {
                        return $row->product_mrp;
                    })
                    ->addColumn('total_cost', function ($row) {
                        return number_format($row->total_cost,2) ;
                    })
                    ->rawColumns([])
                    ->make(true);
            }
            
            $data = [];
            $data['heading'] = 'Sales Product List';
            $data['breadcrumb'] = ['Sales', 'Product', 'List'];
            $data['item_id'] =$request->get('id') ? $request->get('id') : '';
            return view('admin.report.sales_product_list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function salesProductDownload(Request $request){
        //dd($request->all());
        if($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != ''){
            if($request->start_date == $request->end_date){
                $sales_products = SellStockProducts::whereDate('created_at', $request->start_date)->get();
            }else{
                $sales_products = SellStockProducts::whereBetween('created_at', [$request->start_date, $request->end_date])->get();
            }
            
        }else{
            $sales_products = SellStockProducts::all();
        }
        $content = "";
        foreach ($sales_products as $product) {
        $content .= '01/2007/0003|'.date('d-m-Y h:i', strtotime($product->created_at)).'|'.$product->barcode .'|'.substr($product->size->name,0,-4).'|'.$product->product_mrp.'|'.$product->product_qty;
        $content .= "\n";
        }

        // file name that will be used in the download
        $fdate = ($request->has('date') && $request->date != '') ? $request->date : Carbon::now();
        $fileName = $fdate."-Filter-Sell.txt";

        // use headers in order to generate the download
        $headers = [
        'Content-type' => 'text/plain', 
        'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        //'Content-Length' => sizeof($content)
        ];

        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, $headers);
    }
    public function salesItems(Request $request){
        try {
            if ($request->ajax()) {
                $sales = SellInwardStock::where('invoice_no','!=','');
                if(!empty($request->get('start_date')) && !empty($request->get('end_date'))){
                    if($request->get('start_date') == $request->get('end_date')){
                        $sales->whereDate('sell_date', $request->get('start_date'));
                    }else{
                        $sales->whereBetween('sell_date', [$request->get('start_date'), $request->get('end_date')]);
                    }    
                }

                $sales->orderBy('id', 'desc')->get();
                
                
                //echo "<pre>";print_r($request->all());die;
                return DataTables::of($sales)
                    ->addColumn('invoice_no', function ($row) {
                        return '<a class="td-anchor" href="'.route('admin.report.sales.product', [ 'id'=>base64_encode($row->id)]) .'" target="_blank">' . $row->invoice_no . '</a>';
                    
                    })
                    ->addColumn('supplier', function ($row) {
                        return $row->supplierDetails->company_name;
                    })
                    ->addColumn('total_qty', function ($row) {
                        return $row->total_qty;
                    })
                    ->addColumn('gross_amount', function ($row) {
                        return $row->gross_amount;
                    })
                    ->addColumn('discount_amount', function ($row) {
                        return $row->discount_amount;
                    })
                    ->addColumn('sub_total', function ($row) {
                        return $row->sub_total;
                    })
                    ->addColumn('pay_amount', function ($row) {
                        return $row->pay_amount;
                    })
                    ->addColumn('payment_method', function ($row) {
                        return $row->payment_method;
                    })
                    ->addColumn('sell_date', function ($row) {
                        return date('d-m-Y', strtotime($row->sell_date));
                    })
                  
                    /* ->addColumn('total_cost', function ($row) {
                        return number_format($row->total_cost,2) ;
                    }) */
                    ->rawColumns(['invoice_no'])
                    ->make(true);
            }
            
            $data = [];
            $data['heading'] = 'Sales List';
            $data['breadcrumb'] = ['Sales', '', 'List'];

            return view('admin.report.sales_item_list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
   
    public function itemWiseSaleReportPdf(Request $request){
       $satrt_date = $request->start_date.' 00:00:00';
       $end_date = $request->end_date.' 23:59:00';
       //echo $satrt_date;die;
        $items = [];
        $group_cat_products = SellStockProducts::with('category')->groupBy(['category_id'])->whereBetween('created_at', [$satrt_date, $end_date])->get();
        foreach($group_cat_products as $group_cat_product){
            //$items['category'][] =  $group_cat_product->category->name;
            $group_sub_cat_products = SellStockProducts::
                        where('category_id',$group_cat_product->category->id)
                        ->whereBetween('created_at', [$satrt_date, $end_date])
                        ->groupBy(['subcategory_id'])
                        ->get();
            foreach($group_sub_cat_products as $group_sub_cat_product){
                //$items['category'][$group_cat_product->category->name][] = $group_sub_cat_product->subCategory->name;

                $sales_products = SellStockProducts::where('subcategory_id',$group_sub_cat_product->subCategory->id)
                    ->whereBetween('created_at', [$satrt_date, $end_date])
                    ->groupBy(['product_id','size_id'])
                    ->selectRaw('product_name,product_mrp,sum(product_qty) as total_bottles,sum(total_cost) as total_ammount,sum(size_ml) as total_ml')
                    ->get();
                $items[$group_cat_product->category->name][$group_sub_cat_product->subCategory->name]    = $sales_products;
            }           
        }
        $payment_type_ammount = SellInwardStock::whereBetween('payment_date', [$satrt_date, $end_date])
                        ->groupBy(['payment_method'])
                        ->selectRaw('payment_method,sum(pay_amount) as total_payment')
                        ->get();
        
        $data = [];
        $data['items'] = $items;
        $data['shop_name'] = 'BAZIMAT';
        $data['shop_address'] = 'West Chowbaga Kolkata - 700105 West Bengal';
        $data['from_date'] = Carbon::create($request->start_date)->format('d-M-Y');
        $data['to_date'] = Carbon::create($request->end_date)->format('d-M-Y');
        $data['payment_type_ammount'] = $payment_type_ammount;

        $pdf = PDF::loadView('admin.pdf.item-wise-sales-report', $data);
         //echo "<pre>";print_r($items);die;
		return $pdf->stream(now().'-invoice.pdf');
       
    }

    public function productWiseSaleReport(Request $request){
        try {
            //echo $request->get('start_date');die;
            //dd($request);
            $data = [];
            $queryProduct = SellStockProducts::query();
            //Add sorting
            $queryProduct->orderBy('id', 'desc');
            if(!empty($request->get('start_date')) && !empty($request->get('end_date'))){
                if($request->get('start_date') == $request->get('end_date')){
                    $queryProduct->whereDate('created_at', $request->get('start_date'));
                }else{
                    $queryProduct->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
                }    
            }
            if(!is_null($request['customer_id'])) {
                $queryProduct->whereHas('sellInwardStock',function($q) use ($request){
                    return $q->where('customer_id',$request['customer_id']);
                });
            }
            if(!is_null($request['invoice_id'])) {
                $queryProduct->whereHas('sellInwardStock',function($q) use ($request){
                    return $q->where('id',$request['invoice_id']);
                });
            }
            if(!is_null($request['product_id'])) {
                $queryProduct->where('product_id',$request['product_id']);
            }
            if(!is_null($request['category'])) {
                $queryProduct->where('category_id',$request['category']);
            }
            if(!is_null($request['sub_category'])) {
                $queryProduct->where('subcategory_id',$request['sub_category']);
            }
            if(!is_null($request['size'])) {
                $queryProduct->where('size_id',$request['size']);
            }
            $total_qty =  $queryProduct->sum('product_qty');
            $total_cost =  $queryProduct->sum('total_cost');
            $all_product = $queryProduct->get();
            $products = $queryProduct->paginate(10);
            $data['heading']    = 'Sales List';
            $data['breadcrumb'] = ['Sales', '', 'List'];
            $data['products']   = $products;
            $data['total_invoice']  = count(array_unique(array_column($all_product->toArray(), 'inward_stock_id')));
            $data['total_qty']  = $total_qty;
            $data['total_cost'] = $total_cost;
            $data['categories'] = Category::all();
            $data['sizes']      = Size::all();
            $data['sub_categories']      = Subcategory::all();
            //$data['request'] = $request;
            return view('admin.report.product_wise_sales_report', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function getCustomerByKeyup(Request $request){
        $search = $request->search;
        $result = Customer::select('customer_fname','customer_last_name','id')->whereRaw("concat(customer_fname, ' ', customer_last_name) like '%" .$search. "%' ")->take(20)->get();
        $return_data['result']	= $result;
		$return_data['status']	= 1;
		echo json_encode($return_data);	
    }
    public function getSaleInvoiceByKeyup(Request $request){
        $search = $request->search;
        $result = SellInwardStock::select('id','invoice_no')->where('invoice_no', 'LIKE', "%{$search}%")->take(20)->get();
        $return_data['result']	= $result;
		$return_data['status']	= 1;
		echo json_encode($return_data);	
    }
    public function getProductByKeyup(Request $request){
        $search = $request->search;
        $result = Product::select('id','product_barcode','product_name')
                            ->where('product_name', 'LIKE', "%{$search}%")
                            ->orWhere('product_barcode', 'LIKE', "%{$search}%")
                            ->take(20)->get();
        $return_data['result']	= $result;
		$return_data['status']	= 1;
		echo json_encode($return_data);	
    }
}
