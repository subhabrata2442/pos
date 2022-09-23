<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderSupplier;
use App\Helper\Media;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use DB;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
Use Illuminate\Support\Facades\Response;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Abcdefg;
use App\Models\BranchStockProducts;
use App\Models\BranchStockProductSellPrice;
use App\Models\Material;
use App\Models\Service;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\VendorCode;
use App\Models\Measurement;
use App\Models\MasterProducts;
use App\Models\ProductRelationshipSize;
use App\Models\PurchaseInwardStock;
use App\Models\InwardStockProducts;
use App\Models\SellInwardStock;
use App\Models\SellInwardTenderedChangeAmount;
use App\Models\SellStockProducts;
use App\Models\Site_settings;
use App\Models\Common;
use App\Models\Customer;


use App\Models\Warehouse;
use App\Models\TableBookingHistory;
use App\Models\RestaurantFloor;
use App\Models\FloorWiseTable;
use App\Models\Waiter;
use App\Models\TableBookingKoPrintInvoice;
use App\Models\TableBookingKoPrintItems;

use App\Models\BarInwardStock;
use App\Models\BarInwardStockProducts;

use Carbon\Carbon;
use Smalot\PdfParser\Parser;
use Session;

class PosController extends Controller
{
    public function pos_create(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = [];
			
			$branch_id	= 1;
			$stock_type	= Common::get_user_settings($where=['option_name'=>'stock_type'],$branch_id);
			
			$data['stock_type'] 	= isset($stock_type)?$stock_type:'w';
            $data['heading'] 		= 'Add Order';
            $data['breadcrumb'] 	= ['Stock', 'Purchase Order', 'Add'];
            $data['supplier'] 		= Supplier::all();
            $data['product'] 		= Product::all();
			
			
			
			/*$data['purchase_product_result']	= BranchStockProducts::select('master_products.id as product_id','products.slug','products.product_name','products.product_name')
				->leftJoin('products', function($join) {
					$join->on('branch_stock_products.product_id', '=', 'products.id');
				})
				->leftJoin('master_products', function($join) {
					$join->on('products.slug', '=', 'master_products.slug');
				})
				->where('branch_stock_products.branch_id',$branch_id)
				->where('branch_stock_products.stock_type','counter')
				->groupby('product_id')
				->orderby('branch_stock_products.id','DESC')
				->offset(0)->limit(20)
				->get()
				->toArray();
				
				
			echo '<pre>';print_r($data['purchase_product_result']);exit;	*/
			
			
			$data['top_selling_product_result']=[];
			
			$selling_product_result	= SellStockProducts::select('products.slug','products.product_name','products.product_name','sell_stock_products.size_id','sell_stock_products.subcategory_id')->leftJoin('sell_inward_stock', function($join) {
				$join->on('sell_stock_products.inward_stock_id', '=', 'sell_inward_stock.id');
				})
				->leftJoin('products', function($join) {
					$join->on('sell_stock_products.product_id', '=', 'products.id');
				})
				->where('sell_inward_stock.branch_id',$branch_id)
				->groupby('product_id')
				->orderby('sell_stock_products.id','DESC')
				->offset(0)->limit(10)
				->get()
				->toArray();
			//$top_selling_product_ids=[];
			
			foreach($selling_product_result as $row){
				$product_result=MasterProducts::where('slug',$row['slug'])->where('subcategory_id',$row['subcategory_id'])->where('size_id',$row['size_id'])->first();
				//print_r($product_result);exit;
				$product_size=isset($product_result->size->name)?$product_result->size->name:'';
				$data['top_selling_product_result'][]=array(
					'product_id'	=> isset($product_result->id)?$product_result->id:'',
					'product_name'	=> isset($product_result->product_name)?$product_result->product_name:'',
					'product_size'	=> $product_size,
				);
			}
			
			
			//echo '<pre>';print_r($data['top_selling_product_result']);exit;
			
			//print_r('dd');exit;
			
            return view('admin.counter_pos.pos', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	public function create(Request $request){
		
		$branch_id		= 1;
		$supplier_id	= 17;
		$customer_id	= 2;
		
		$validator = Validator::make($request->all(), [
			'total_quantity' => 'required',
			'total_mrp' => 'required'
		]);
		if ($validator->fails()) {
			$return_data['success']	= 0;
			$return_data['msg']		= 'Product should not be empty';
			echo json_encode($return_data);
		}
		
		//print_r($_POST);exit;
		
		
		$invoice_no='';
		$n=SellInwardStock::count();
		$invoice_no .=date('d');
		$invoice_no .='/'.date('Y');
		$invoice_no .='/'.str_pad($n + 1, 4, 0, STR_PAD_LEFT);
		$invoice_no .='|'.date('d/m/Y');
		
		$sellStockData=array(
			'branch_id' 				=> $branch_id,
			'supplier_id' 				=> $supplier_id,
			'customer_id' 				=> $customer_id,
			'invoice_no' 				=> $invoice_no,
			'sell_date' 				=> date('Y-m-d'),
			'sell_time' 				=> date('H:i'),
			'stock_type' 				=> $request->stock_type,
			'total_qty' 				=> $request->total_quantity,
			'gross_amount' 				=> $request->total_mrp,
			'tax_amount' 				=> $request->tax_amount,
			'discount_amount' 			=> $request->total_discount_amount,
			'sub_total' 				=> $request->sub_total,
			'round_off_amount' 			=> $request->round_off ?? 0,
			'gross_total_amount'		=> $request->gross_total_amount ?? 0,
			'special_discount_percent'	=> $request->special_discount_percent,
			'special_discount_amt' 		=> $request->special_discount_amt,
			'pay_amount' 				=> $request->total_payble_amount,
			'tendered_due_amount' 		=> $request->total_payble_amount,
			'tendered_amount' 			=> $request->tendered_amount,
			'tendered_change_amount' 	=> $request->tendered_change_amount,
			'payment_method' 			=> $request->payment_method_type,
			'payment_date' 				=> date('Y-m-d'),
			'created_at'				=> date('Y-m-d')
		);
		
		//print_r($sellStockData);exit;
		
		$sellStock		= SellInwardStock::create($sellStockData);
		$sellStockId	= $sellStock->id;
		//$sellStockId	= 1;
		
		$product_ids			= $request->product_id;
		$product_total_amount	= $request->product_total_amount;
		$product_barcode		= $request->product_barcode;
		$product_name			= $request->product_name;
		$product_qty			= $request->product_qty;
		$product_disc_percent	= $request->product_disc_percent;
		$product_disc_amount	= $request->product_disc_amount;
		$product_unit_price		= $request->product_unit_price;
		$product_price_id		= $request->product_price_id;
		
		
		for($i=0;count($product_ids)>$i;$i++){
			$product_stock_id			= $product_ids[$i];
			$branch_product_stock_info	= BranchStockProducts::where('id',$product_stock_id)->get();
			
			$product_id 		= isset($branch_product_stock_info[0]->product_id)?$branch_product_stock_info[0]->product_id:'';
			$product_size_id 	= isset($branch_product_stock_info[0]->size_id)?$branch_product_stock_info[0]->size_id:'0';
			
			
			
			if($product_id!=''){
				$total_amount	= isset($product_total_amount[$i])?$product_total_amount[$i]:'0';
				$barcode		= isset($product_barcode[$i])?$product_barcode[$i]:'';
				$name			= isset($product_name[$i])?$product_name[$i]:'';
				$qty			= isset($product_qty[$i])?$product_qty[$i]:'';
				$disc_percent	= isset($product_disc_percent[$i])?$product_disc_percent[$i]:0;
				$disc_amount	= isset($product_disc_amount[$i])?$product_disc_amount[$i]:0;
				$unit_price		= isset($product_unit_price[$i])?$product_unit_price[$i]:0;
				$price_id		= isset($product_price_id[$i])?$product_price_id[$i]:0;
				
				$productInfo	= Product::where('id',$product_id)->get();
				$category_id	= isset($productInfo[0]->category_id)?$productInfo[0]->category_id:0;
				$subcategory_id	= isset($productInfo[0]->subcategory_id)?$productInfo[0]->subcategory_id:0;
				
				$productSizeInfo= Size::where('id',$product_size_id)->get();
				$size	= isset($productSizeInfo[0]->name)?$productSizeInfo[0]->name:0;
								
				$branch_product_stock_sell_price_info=BranchStockProductSellPrice::where('id',$price_id)->where('stock_type','counter')->get();
				
				$sell_price_id=isset($branch_product_stock_sell_price_info[0]->id)?$branch_product_stock_sell_price_info[0]->id:'';
				
				$sell_price_w_qty = 0;
				$sell_price_c_qty = 0;
				if($request->stock_type=='s'){
					$sell_price_c_qty +=isset($branch_product_stock_sell_price_info[0]->c_qty)?$branch_product_stock_sell_price_info[0]->c_qty:'';
					$sell_price_c_qty -=$qty;
					BranchStockProductSellPrice::where('id', $sell_price_id)->where('stock_type','counter')->update(['c_qty' => $sell_price_c_qty]);
				}else{
					$sell_price_w_qty +=isset($branch_product_stock_sell_price_info[0]->w_qty)?$branch_product_stock_sell_price_info[0]->w_qty:0;
					$sell_price_w_qty -=$qty;
					BranchStockProductSellPrice::where('id', $sell_price_id)->where('stock_type','counter')->update(['w_qty' => $sell_price_w_qty]);
				}
				
				$sellStockproductData=array(
					'inward_stock_id'	=> $sellStockId,
					'product_id'  		=> $product_id,
					'product_stock_id'  => $product_stock_id,
					'barcode'			=> $barcode,
					'product_name'  	=> $name,
					'price_id'  		=> $price_id,
					'size_id'  			=> $product_size_id,
					'category_id'  		=> $category_id,
					'subcategory_id'  	=> $subcategory_id,
					'size_ml'  			=> $size,
					'product_qty'		=> $qty,
					'discount_percent'  => $disc_percent,
					'discount_amount'  	=> $disc_amount,
					'product_mrp'		=> $unit_price,
					'unit_price'  		=> $unit_price,
					'offer_price'  		=> $unit_price,
					'total_cost'		=> $total_amount,
					//'created_at'		=> date('Y-m-d')
				);
				//print_r($sellStockproductData);exit;
				SellStockProducts::create($sellStockproductData);
			}
		}
		
		$rupee_type 	= $request->rupee_type;
		$rupee_val 		= $request->note;
		$rupee_qty 		= $request->note_qty;
		
		for($r=0;count($rupee_type)>$r;$r++){
			$note_type	= isset($rupee_type[$r])?$rupee_type[$r]:'note';
			$note_val	= isset($rupee_val[$r])?$rupee_val[$r]:0;
			$note_qty	= isset($rupee_qty[$r])?$rupee_qty[$r]:0;
			$total_note_amount	= $note_val*$note_qty;
			
			$tenderedChangeAmount=array(
				'sell_inward_stock_id'	=> $sellStockId,
				'type'  				=> $note_type,
				'rupee_val'  			=> $note_val,
				'qty'					=> $note_qty,
				'amount'  				=> $total_note_amount,
				'created_at'			=> date('Y-m-d')
			);
			//print_r($tenderedChangeAmount);exit;
			SellInwardTenderedChangeAmount::create($tenderedChangeAmount);
		}
		
		
		$return_data['success']	= 1;
		echo json_encode($return_data);
		
		
	}	
}
