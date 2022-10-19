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
use App\Models\BranchStockProducts;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SellInwardStock;
use App\Models\SellStockProducts;
use App\Models\Size;
use App\Models\Subcategory;

use App\Models\Counter;
use App\Models\StockTransferHistory;
use App\Models\StockTransferCounterHistory;
use App\Models\CounterWiseStock;
use App\Models\DailyProductSellHistory;
use App\Models\OpeningStockProducts;
use App\Models\DailyProductPurchaseHistory;



use Carbon\Carbon;
Use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Str;


class CronController extends Controller {
	
	
	
	public function daily_product_purchase_history($branch_id){
		$purchase_date_result 	= InwardStockProducts::where('branch_id',$branch_id)->where('is_new','Y')->orderBy('id', 'asc')->first();
		$purchase_start_date	= isset($purchase_date_result->created_at)?date('Y-m-d',strtotime($purchase_date_result->created_at)):'';
		
		
		if($purchase_start_date!=''){
			$current_date=date('Y-m-d');
			
			$category_result		= InwardStockProducts::select('category_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$sub_category_result 	= InwardStockProducts::select('subcategory_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$size_result 			= InwardStockProducts::select('size_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$product_result 		= InwardStockProducts::select('product_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							$purchase_result = InwardStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as product_qty,product_mrp')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->where('is_new','Y')->get();
							if(count($purchase_result)>0){
								$date_wise_total_ml	  	= isset($purchase_result[0]->total_ml)?$purchase_result[0]->total_ml:0;
								$date_wise_total_qty	= isset($purchase_result[0]->product_qty)?$purchase_result[0]->product_qty:0;
								$product_mrp			= isset($purchase_result[0]->product_mrp)?$purchase_result[0]->product_mrp:0;
									
								$closing_stock 		= $date_wise_total_qty;
								$closing_stock_ml 	= $date_wise_total_ml;
									
								$last_purchase_history_result = DailyProductPurchaseHistory::select('closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$last_purchase_total_ml	  	= isset($last_purchase_history_result->closing_stock_ml)?$last_purchase_history_result->closing_stock_ml:'';
								$last_purchase_total_qty	= isset($last_purchase_history_result->closing_stock)?$last_purchase_history_result->closing_stock:'';
								if($last_purchase_total_qty!=''){
									$closing_stock 		= $last_purchase_total_qty+$date_wise_total_qty;
									$closing_stock_ml 	= $last_purchase_total_ml+$date_wise_total_ml;
								}
								$check_purchase_history_result = DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->get();
								if(count($check_purchase_history_result)>0){
									$total_qty	= $date_wise_total_qty+$check_purchase_history_result[0]->total_qty;
									$total_ml	= $date_wise_total_ml+$check_purchase_history_result[0]->total_ml;
									
									DailyProductPurchaseHistory::where('id',$check_purchase_history_result[0]->id)->update(['total_qty' => $total_qty,'total_ml' => $total_ml,'closing_stock' => $closing_stock,'closing_stock_ml' => $closing_stock_ml]);
										
									InwardStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
								}else{
									$purchase_data=array(
										'branch_id'  		=> $branch_id,
										'category_id'		=> $category_id,
										'subcategory_id'	=> $subcategory_id,
										'product_id'  		=> $product_id,
										'size_id'  			=> $size_id,
										'total_qty'  		=> $date_wise_total_qty,
										'total_ml'  		=> $date_wise_total_ml,
										'closing_stock'  	=> $closing_stock,
										'closing_stock_ml'  => $closing_stock_ml,
										'product_mrp'		=> $product_mrp
									);
									DailyProductPurchaseHistory::create($purchase_data);
									InwardStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
								}
							}
						}
					}
				}
			}
				
			echo 'success';exit;	
		}else{
			echo 'Not data found';exit;
		}	
	}
	
	public function daily_product_sell_history($branch_id){
		$sell_date=date('Y-m-d');
		$total_product_sell_count = SellStockProducts::selectRaw('id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->count();
		//echo '<pre>';print_r($total_product_sell_count);exit;
		if($total_product_sell_count>0){
			$category_result 		= SellStockProducts::select('category_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->get();
			$sub_category_result 	= SellStockProducts::select('subcategory_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->get();
			$size_result 			= SellStockProducts::select('size_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->get();
			$product_result 		= SellStockProducts::select('product_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->get();
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							$dateWise_sell_result = SellStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as total_qty,barcode,product_mrp')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->get();
							
							$total_sell_ml = isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
							if($total_sell_ml>0){
								$openingStockProductResult 	= OpeningStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$start_opening_stock_ml		= isset($openingStockProductResult->total_ml)?$openingStockProductResult->total_ml:'0';
								$start_opening_stock		= isset($openingStockProductResult->product_qty)?$openingStockProductResult->product_qty:'0';
									
								$purchase_history_result 	= DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->get();
									
								$purchase_stock_ml =isset($purchase_history_result[0]->closing_stock_ml)?$purchase_history_result[0]->closing_stock_ml:'0';
								$purchase_stock	   =isset($purchase_history_result[0]->closing_stock)?$purchase_history_result[0]->closing_stock:'0';
									
								$gross_opening_stock_ml	= $start_opening_stock_ml+$purchase_stock_ml;
								$gross_opening_stock	= $start_opening_stock+$purchase_stock;
									
								$prev_datewise_sell_result = DailyProductSellHistory::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$prev_closing_stock	  = isset($prev_datewise_sell_result->closing_stock)?$prev_datewise_sell_result->closing_stock:'';
								$prev_closing_stock_ml= isset($prev_datewise_sell_result->closing_stock_ml)?$prev_datewise_sell_result->closing_stock_ml:'';
								$opening_stock_ml	= $gross_opening_stock_ml;
								$opening_stock 		= $gross_opening_stock;
									
								if($prev_closing_stock_ml!=''){
									$opening_stock_ml 	= $prev_closing_stock_ml;
									$opening_stock 		= $prev_closing_stock;
								}
								$barcode		= isset($dateWise_sell_result[0]->barcode)?$dateWise_sell_result[0]->barcode:'0';
								$product_mrp	= isset($dateWise_sell_result[0]->product_mrp)?$dateWise_sell_result[0]->product_mrp:'0';
								$total_sell		= isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
								$total_qty_sell	= isset($dateWise_sell_result[0]->total_qty)?$dateWise_sell_result[0]->total_qty:'0';
								
								$closing_stock_ml	= $opening_stock_ml-$total_sell;
								$closing_stock		= $opening_stock-$total_qty_sell;
								
								$check_datewise_sell_result = DailyProductSellHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$check_sell_id		  = isset($check_datewise_sell_result->id)?$check_datewise_sell_result->id:'';
								if($check_sell_id!=''){
								}else{
									$size_cost_data=array(
										'branch_id'  		=> $branch_id,
										'category_id'		=> $category_id,
										'subcategory_id'	=> $subcategory_id,
										'product_barcode'	=> $barcode,
										'product_id'  		=> $product_id,
										'size_id'  			=> $size_id,
										'total_ml'  		=> $total_sell,
										'total_qty'  		=> $total_qty_sell,
										'opening_stock'  	=> $opening_stock,
										'closing_stock'  	=> $closing_stock,
										'opening_stock_ml'  => $opening_stock_ml,
										'closing_stock_ml' 	=> $closing_stock_ml,
										'product_mrp'		=> $product_mrp
									);
									DailyProductSellHistory::create($size_cost_data);
								}
							}
						}	
					}
				}
			}
			echo 'success';exit;
		}else{
			echo 'Data not found';exit;
		}
	}
	
	
}
?>

      