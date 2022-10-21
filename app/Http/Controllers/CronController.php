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

use App\Models\DailyStockTransferHistory;


use Carbon\Carbon;
Use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Str;


class CronController extends Controller {
	
	
	
	public function daily_product_sell_history_new($branch_id){
		$branch_id=Session::get('branch_id');
		$sell_date=date('Y-m-d');
		$total_product_sell_count = SellStockProducts::selectRaw('id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('is_new','Y')->count();
		//echo '<pre>';print_r($total_product_sell_count);exit;
		if($total_product_sell_count>0){
			$category_result 		= SellStockProducts::select('category_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$sub_category_result 	= SellStockProducts::select('subcategory_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$size_result 			= SellStockProducts::select('size_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$product_result 		= SellStockProducts::select('product_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->where('is_new','Y')->get();
			
			//echo '<pre>';print_r($product_result);exit;
			
			
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							
							$dateWise_sell_result = SellStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as total_qty,barcode,product_mrp')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->where('is_new','Y')->get();
							
							//echo '<pre>';print_r($dateWise_sell_result);exit;
							
							$total_sell_ml = isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
							if($total_sell_ml>0){
								
								$openingStockProductResult 	= OpeningStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$start_opening_stock_ml		= isset($openingStockProductResult->total_ml)?$openingStockProductResult->total_ml:'0';
								$start_opening_stock		= isset($openingStockProductResult->product_qty)?$openingStockProductResult->product_qty:'0';
									
								$purchase_history_result 	= DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								
								//echo '<pre>';print_r($purchase_history_result);exit;
									
								$purchase_stock_ml = isset($purchase_history_result->closing_stock_ml)?$purchase_history_result->closing_stock_ml:'0';
								$purchase_stock	   = isset($purchase_history_result->closing_stock)?$purchase_history_result->closing_stock:'0';
									
								$gross_opening_stock_ml	= $start_opening_stock_ml+$purchase_stock_ml;
								$gross_opening_stock	= $start_opening_stock+$purchase_stock;
								
								//echo '<pre>';print_r($gross_opening_stock_ml);exit;
								
								$prev_sell_date		= date('Y-m-d', strtotime("-1 day", strtotime($sell_date)));
								
								//$prev_sell_date		= $sell_date;
								
								//echo '<pre>';print_r($prev_sell_date);exit;
									
								$prev_datewise_sell_result = DailyProductSellHistory::whereBetween('created_at', [$prev_sell_date." 00:00:00", $prev_sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$prev_closing_stock	  = isset($prev_datewise_sell_result->closing_stock)?$prev_datewise_sell_result->closing_stock:'';
								$prev_closing_stock_ml= isset($prev_datewise_sell_result->closing_stock_ml)?$prev_datewise_sell_result->closing_stock_ml:'';
								
								$prev_opening_stock	  = isset($prev_datewise_sell_result->opening_stock)?$prev_datewise_sell_result->opening_stock:'';
								$prev_opening_stock_ml= isset($prev_datewise_sell_result->opening_stock_ml)?$prev_datewise_sell_result->opening_stock_ml:'';
								
								//echo '<pre>';print_r($prev_datewise_sell_result);exit;
								
								$opening_stock_ml	= $gross_opening_stock_ml;
								$opening_stock 		= $gross_opening_stock;
								
								
								$total_datewise_sell_count = DailyProductSellHistory::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->count();
								
								//echo '<pre>';print_r($total_datewise_sell_count);exit;
									
								if($prev_closing_stock_ml!=''){
									if($total_datewise_sell_count>=1){
										
										$today_purchase_history_result 	= DailyProductPurchaseHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
										
										$today_purchase_stock_ml	= isset($today_purchase_history_result->total_ml)?$today_purchase_history_result->total_ml:'0';
										$today_purchase_stock		= isset($today_purchase_history_result->total_qty)?$today_purchase_history_result->total_qty:'0';
										
										//$current_opening_stock_ml	=(($purchase_stock_ml-$prev_opening_stock_ml)+$start_opening_stock_ml);
										$opening_stock_ml 			= $prev_closing_stock_ml+$today_purchase_stock_ml;
										$opening_stock 				= $prev_closing_stock+$today_purchase_stock;
										
										
										//echo '<pre>';print_r($opening_stock);exit;
										
										
										
										//$current_opening_stock_ml	=(($purchase_stock_ml-$prev_opening_stock_ml)+$start_opening_stock_ml);
										//$opening_stock_ml 			= $prev_closing_stock_ml+$current_opening_stock_ml;
										
										//echo '<pre>';print_r($opening_stock_ml);exit;
										//$opening_stock_ml 	= $prev_closing_stock_ml;
										
										//$current_opening_stock	=(($purchase_stock-$prev_opening_stock)+$start_opening_stock);
										
										
										//$opening_stock 		= $prev_closing_stock;
									}	
								}
								
								//echo '<pre>';print_r($opening_stock_ml);exit;
								
								$barcode		= isset($dateWise_sell_result[0]->barcode)?$dateWise_sell_result[0]->barcode:'0';
								$product_mrp	= isset($dateWise_sell_result[0]->product_mrp)?$dateWise_sell_result[0]->product_mrp:'0';
								$total_sell		= isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
								$total_qty_sell	= isset($dateWise_sell_result[0]->total_qty)?$dateWise_sell_result[0]->total_qty:'0';
								
								$closing_stock_ml	= $opening_stock_ml-$total_sell;
								$closing_stock		= $opening_stock-$total_qty_sell;
								
								$date_wise_total_sell_ml	= $total_sell;
								$date_wise_total_sell_qty	= $total_qty_sell;
								
								
								
								$inwardStockProducts = InwardStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$inward_stock_id=isset($inwardStockProducts->id)?$inwardStockProducts->id:'';
								if($inward_stock_id!=''){
									PurchaseInwardStock::where('id',$inward_stock_id)->update(['is_sell' => 'Y']);
								}
								
								//echo '<pre>';print_r($inward_stock_id);exit;
								
								$check_datewise_sell_result = DailyProductSellHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$check_sell_id		  = isset($check_datewise_sell_result->id)?$check_datewise_sell_result->id:'';
								
								if($check_sell_id!=''){
									$total_qty	= $date_wise_total_sell_qty+$check_datewise_sell_result->total_qty;
									$total_ml	= $date_wise_total_sell_ml+$check_datewise_sell_result->total_ml;
									
									$closing_stock		= $opening_stock-$total_qty;
									$closing_stock_ml	= $opening_stock_ml-$total_ml;
									
									//echo '<pre>';print_r($closing_stock_ml);exit;
									
									
									
									DailyProductSellHistory::where('id',$check_sell_id)->update(['total_ml' => $total_ml,'total_qty' => $total_qty,'opening_stock' => $opening_stock,'closing_stock' => $closing_stock,'opening_stock_ml' => $opening_stock_ml,'closing_stock_ml' => $closing_stock_ml]);
									
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
									
									//echo '<pre>';print_r($total_ml);exit;
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
									//echo '<pre>';print_r($size_cost_data);exit;
									DailyProductSellHistory::create($size_cost_data);
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
								}
							}
						}	
					}
				}
			}
		}else{
		}
	}	
	
	
	
	public function daily_product_purchase_history($branch_id){
		$purchase_date_result 	= InwardStockProducts::where('branch_id',$branch_id)->where('is_new','Y')->orderBy('id', 'asc')->first();
		$purchase_start_date	= isset($purchase_date_result->created_at)?date('Y-m-d',strtotime($purchase_date_result->created_at)):'';
		
		$items=[];
		
		if($purchase_start_date!=''){
			$current_date=date('Y-m-d');
			
			$category_result		= InwardStockProducts::select('category_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$sub_category_result 	= InwardStockProducts::select('subcategory_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$size_result 			= InwardStockProducts::select('size_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			$product_result 		= InwardStockProducts::select('product_id')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->distinct()->get();
			
			//echo '<pre>';print_r($category_result);exit;
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							
							$purchase_result = InwardStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as product_qty,product_mrp')->whereBetween('created_at', [$current_date." 00:00:00", $current_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->where('is_new','Y')->get();
							
							$date_wise_total_ml	  	= isset($purchase_result[0]->total_ml)?$purchase_result[0]->total_ml:0;
							
							//echo '<pre>';print_r($purchase_result);exit;
							//echo $product_id.'-'.$date_wise_total_ml.'</br>';
							
							//$purchase_result=[];
							
							if($date_wise_total_ml>0){
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
								
								//echo '<pre>';print_r($check_purchase_history_result);exit;
								
								if(count($check_purchase_history_result)>0){
									$total_qty	= $date_wise_total_qty+$check_purchase_history_result[0]->total_qty;
									$total_ml	= $date_wise_total_ml+$check_purchase_history_result[0]->total_ml;
									
									$items[]=array(
										'is_new'			=>'update',
										'branch_id'  		=> $branch_id,
										'category_id'		=> $category_id,
										'subcategory_id'	=> $subcategory_id,
										'product_id'  		=> $product_id,
										'size_id'  			=> $size_id,
										'total_qty' 		=> $total_qty,
										'total_ml' 			=> $total_ml,
										'closing_stock' 	=> $closing_stock,
										'closing_stock_ml' 	=> $closing_stock_ml
									);
									
									/*DailyProductPurchaseHistory::where('id',$check_purchase_history_result[0]->id)->update(['total_qty' => $total_qty,'total_ml' => $total_ml,'closing_stock' => $closing_stock,'closing_stock_ml' => $closing_stock_ml]);
										
									InwardStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);*/
								}else{
									/*$purchase_data=array(
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
									InwardStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);*/
									
									
									$items[]=array(
										'is_new'			=>'new',
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
									
								}
							}
						}
					}
				}
			}
			
			echo '<pre>';print_r($items);exit;	
			echo 'success';exit;	
		}else{
			echo 'Not data found';exit;
		}	
	}
	
	public function daily_product_sell_history($branch_id){
		$sell_date_result 	= StockTransferHistory::where('branch_id',$branch_id)->where('is_new','Y')->orderBy('id', 'asc')->first();
		$start_date			= isset($sell_date_result->created_at)?date('Y-m-d',strtotime($sell_date_result->created_at)):'';
		
		if($start_date!=''){
			$current_date=date('Y-m-d');
			$diff 		= strtotime($current_date) - strtotime($start_date);
			$total_day	= round($diff / 86400);
			
			for($i=0;$total_day>=$i;$i++){
				$sell_date	= date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
				//echo $sell_date.'</br>';exit;
				
				$category_result 		= StockTransferHistory::select('category_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
				$sub_category_result 	= StockTransferHistory::select('subcategory_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
				$size_result 			= StockTransferHistory::select('size_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->where('is_new','Y')->get();
				$product_result 		= StockTransferHistory::select('product_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
				//echo '<pre>';print_r($product_result);exit;
				
				foreach($category_result as $cat_row){
					$category_id=$cat_row->category_id;
					foreach($sub_category_result as $sub_cat_row){
						$subcategory_id=$sub_cat_row->subcategory_id;
						foreach($size_result as $size_row){
							$size_id=$size_row->size_id;
							foreach($product_result as $product_row){
								$product_id=$product_row->product_id;
								//echo $category_id.'-'.$subcategory_id.'-'.$size_id.'-'.$product_id;exit;
								
								$dateWise_sell_result = StockTransferHistory::selectRaw('sum(total_ml) as total_ml,sum(c_qty) as total_qty')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->get();
								$total_total_qty = isset($dateWise_sell_result[0]->total_qty)?$dateWise_sell_result[0]->total_qty:'0';
								
								//echo '<pre>';print_r($total_total_qty);exit;
								
								if($total_total_qty>0){
									$openingStockProductResult = OpeningStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
									$start_opening_stock_ml	= isset($openingStockProductResult->total_ml)?$openingStockProductResult->total_ml:'0';
									$start_opening_stock	= isset($openingStockProductResult->product_qty)?$openingStockProductResult->product_qty:'0';
									
									$purchase_history_result 	= DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
									
									$purchase_stock_ml = isset($purchase_history_result->closing_stock_ml)?$purchase_history_result->closing_stock_ml:'0';
									$purchase_stock	   = isset($purchase_history_result->closing_stock)?$purchase_history_result->closing_stock:'0';
									
									$gross_opening_stock_ml	= $start_opening_stock_ml+$purchase_stock_ml;
									$gross_opening_stock	= $start_opening_stock+$purchase_stock;
									
									
									$prev_sell_date		= date('Y-m-d', strtotime("-1 day", strtotime($sell_date)));
									
									$prev_datewise_sell_result = DailyStockTransferHistory::whereBetween('created_at', [$prev_sell_date." 00:00:00", $prev_sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
									$prev_closing_stock	  =isset($prev_datewise_sell_result->closing_stock)?$prev_datewise_sell_result->closing_stock:'';
									$prev_closing_stock_ml=isset($prev_datewise_sell_result->closing_stock_ml)?$prev_datewise_sell_result->closing_stock_ml:'';
								
									$prev_opening_stock	  =isset($prev_datewise_sell_result->opening_stock)?$prev_datewise_sell_result->opening_stock:'';
									$prev_opening_stock_ml=isset($prev_datewise_sell_result->opening_stock_ml)?$prev_datewise_sell_result->opening_stock_ml:'';
									
									$opening_stock_ml	= $gross_opening_stock_ml;
									$opening_stock 		= $gross_opening_stock;
									
									$total_datewise_sell_count = DailyStockTransferHistory::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->count();
									
									if($prev_closing_stock_ml!=''){
										if($total_datewise_sell_count>=1){
											$today_purchase_history_result 	= DailyStockTransferHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
										
										$today_purchase_stock_ml	= isset($today_purchase_history_result->total_ml)?$today_purchase_history_result->total_ml:'0';
										$today_purchase_stock		= isset($today_purchase_history_result->total_qty)?$today_purchase_history_result->total_qty:'0';
										
										//$current_opening_stock_ml	=(($purchase_stock_ml-$prev_opening_stock_ml)+$start_opening_stock_ml);
										$opening_stock_ml 			= $prev_closing_stock_ml+$today_purchase_stock_ml;
										$opening_stock 				= $prev_closing_stock+$today_purchase_stock;
										
										
										echo '<pre>';print_r($opening_stock);exit;
										
										
										
										//$current_opening_stock_ml	=(($purchase_stock_ml-$prev_opening_stock_ml)+$start_opening_stock_ml);
										//$opening_stock_ml 			= $prev_closing_stock_ml+$current_opening_stock_ml;
										
										//echo '<pre>';print_r($opening_stock_ml);exit;
										//$opening_stock_ml 	= $prev_closing_stock_ml;
										
										//$current_opening_stock	=(($purchase_stock-$prev_opening_stock)+$start_opening_stock);
										
										
										//$opening_stock 		= $prev_closing_stock;
									}	
								}
									
									
									
									echo '<pre>';print_r($prev_datewise_sell_result);exit;
									
									
									echo '<pre>';print_r($prev_sell_date);exit;
									
									
									
									echo '<pre>';print_r($purchase_history_result);exit;
									
									
									
								}
								
								
								
								
								echo '<pre>';print_r($start_opening_stock_ml);exit;
								
								
								
								
								
								
								
							}
						}
					}
				}
			}
			
			echo '<pre>';print_r($total_day);exit;
			
			
			
		}
		
		
		
		
		echo '<pre>';print_r($sell_start_date);exit;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$sell_date=date('Y-m-d');
		$total_product_sell_count = StockTransferHistory::selectRaw('id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('is_new','Y')->count();
		echo '<pre>';print_r($sell_date);exit;
		
		
		
		
		if($total_product_sell_count>0){
			$category_result 		= SellStockProducts::select('category_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$sub_category_result 	= SellStockProducts::select('subcategory_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$size_result 			= SellStockProducts::select('size_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$product_result 		= SellStockProducts::select('product_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->where('is_new','Y')->get();
			
			//echo '<pre>';print_r($product_result);exit;
			
			
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							
							$dateWise_sell_result = SellStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as total_qty,barcode,product_mrp')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->where('is_new','Y')->get();
							
							//echo '<pre>';print_r($dateWise_sell_result);exit;
							
							$total_sell_ml = isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
							if($total_sell_ml>0){
								
								$openingStockProductResult 	= OpeningStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$start_opening_stock_ml		= isset($openingStockProductResult->total_ml)?$openingStockProductResult->total_ml:'0';
								$start_opening_stock		= isset($openingStockProductResult->product_qty)?$openingStockProductResult->product_qty:'0';
									
								$purchase_history_result 	= DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								
								//echo '<pre>';print_r($purchase_history_result);exit;
									
								$purchase_stock_ml = isset($purchase_history_result->closing_stock_ml)?$purchase_history_result->closing_stock_ml:'0';
								$purchase_stock	   = isset($purchase_history_result->closing_stock)?$purchase_history_result->closing_stock:'0';
									
								$gross_opening_stock_ml	= $start_opening_stock_ml+$purchase_stock_ml;
								$gross_opening_stock	= $start_opening_stock+$purchase_stock;
								
								//echo '<pre>';print_r($start_opening_stock);exit;
									
								$prev_datewise_sell_result = DailyProductSellHistory::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$prev_closing_stock	  = isset($prev_datewise_sell_result->closing_stock)?$prev_datewise_sell_result->closing_stock:'';
								$prev_closing_stock_ml= isset($prev_datewise_sell_result->closing_stock_ml)?$prev_datewise_sell_result->closing_stock_ml:'';
								
								//echo '<pre>';print_r($prev_datewise_sell_result);exit;
								
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
								
								$date_wise_total_sell_ml	= $total_sell;
								$date_wise_total_sell_qty	= $total_qty_sell;
								
								//echo '<pre>';print_r($closing_stock_ml);exit;
								
								$check_datewise_sell_result = DailyProductSellHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$check_sell_id		  = isset($check_datewise_sell_result->id)?$check_datewise_sell_result->id:'';
								
								if($check_sell_id!=''){
									$total_qty	= $date_wise_total_sell_qty+$check_datewise_sell_result->total_qty;
									$total_ml	= $date_wise_total_sell_ml+$check_datewise_sell_result->total_ml;
									
									DailyProductSellHistory::where('id',$check_sell_id)->update(['total_ml' => $total_ml,'total_qty' => $total_qty,'opening_stock' => $opening_stock,'closing_stock' => $closing_stock,'opening_stock_ml' => $opening_stock_ml,'closing_stock_ml' => $closing_stock_ml]);
									
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
									
									//echo '<pre>';print_r($total_ml);exit;
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
									//echo '<pre>';print_r($size_cost_data);exit;
									DailyProductSellHistory::create($size_cost_data);
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
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
	
	
	
	public function daily_product_sell_history_old($branch_id){
		$sell_date=date('Y-m-d');
		$total_product_sell_count = SellStockProducts::selectRaw('id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('is_new','Y')->count();
		//echo '<pre>';print_r($total_product_sell_count);exit;
		if($total_product_sell_count>0){
			$category_result 		= SellStockProducts::select('category_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$sub_category_result 	= SellStockProducts::select('subcategory_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$size_result 			= SellStockProducts::select('size_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('is_new','Y')->distinct()->get();
			$product_result 		= SellStockProducts::select('product_id')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->distinct()->where('is_new','Y')->get();
			
			//echo '<pre>';print_r($product_result);exit;
			
			
			
			foreach($category_result as $cat_row){
				$category_id=$cat_row->category_id;
				foreach($sub_category_result as $sub_cat_row){
					$subcategory_id=$sub_cat_row->subcategory_id;
					foreach($size_result as $size_row){
						$size_id=$size_row->size_id;
						foreach($product_result as $product_row){
							$product_id=$product_row->product_id;
							
							$dateWise_sell_result = SellStockProducts::selectRaw('sum(total_ml) as total_ml,sum(product_qty) as total_qty,barcode,product_mrp')->whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->where('is_new','Y')->get();
							
							//echo '<pre>';print_r($dateWise_sell_result);exit;
							
							$total_sell_ml = isset($dateWise_sell_result[0]->total_ml)?$dateWise_sell_result[0]->total_ml:'0';
							if($total_sell_ml>0){
								
								$openingStockProductResult 	= OpeningStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$start_opening_stock_ml		= isset($openingStockProductResult->total_ml)?$openingStockProductResult->total_ml:'0';
								$start_opening_stock		= isset($openingStockProductResult->product_qty)?$openingStockProductResult->product_qty:'0';
									
								$purchase_history_result 	= DailyProductPurchaseHistory::select('id', 'total_qty', 'total_ml', 'closing_stock', 'closing_stock_ml')->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								
								//echo '<pre>';print_r($purchase_history_result);exit;
									
								$purchase_stock_ml = isset($purchase_history_result->closing_stock_ml)?$purchase_history_result->closing_stock_ml:'0';
								$purchase_stock	   = isset($purchase_history_result->closing_stock)?$purchase_history_result->closing_stock:'0';
									
								$gross_opening_stock_ml	= $start_opening_stock_ml+$purchase_stock_ml;
								$gross_opening_stock	= $start_opening_stock+$purchase_stock;
								
								//echo '<pre>';print_r($start_opening_stock);exit;
									
								$prev_datewise_sell_result = DailyProductSellHistory::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->orderBy('id', 'DESC')->first();
								$prev_closing_stock	  = isset($prev_datewise_sell_result->closing_stock)?$prev_datewise_sell_result->closing_stock:'';
								$prev_closing_stock_ml= isset($prev_datewise_sell_result->closing_stock_ml)?$prev_datewise_sell_result->closing_stock_ml:'';
								
								//echo '<pre>';print_r($prev_datewise_sell_result);exit;
								
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
								
								$date_wise_total_sell_ml	= $total_sell;
								$date_wise_total_sell_qty	= $total_qty_sell;
								
								//echo '<pre>';print_r($closing_stock_ml);exit;
								
								$check_datewise_sell_result = DailyProductSellHistory::whereBetween('created_at', [$sell_date." 00:00:00", $sell_date." 23:59:59"])->where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->first();
								$check_sell_id		  = isset($check_datewise_sell_result->id)?$check_datewise_sell_result->id:'';
								
								if($check_sell_id!=''){
									$total_qty	= $date_wise_total_sell_qty+$check_datewise_sell_result->total_qty;
									$total_ml	= $date_wise_total_sell_ml+$check_datewise_sell_result->total_ml;
									
									DailyProductSellHistory::where('id',$check_sell_id)->update(['total_ml' => $total_ml,'total_qty' => $total_qty,'opening_stock' => $opening_stock,'closing_stock' => $closing_stock,'opening_stock_ml' => $opening_stock_ml,'closing_stock_ml' => $closing_stock_ml]);
									
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
									
									//echo '<pre>';print_r($total_ml);exit;
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
									//echo '<pre>';print_r($size_cost_data);exit;
									DailyProductSellHistory::create($size_cost_data);
									SellStockProducts::where('branch_id',$branch_id)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->where('product_id',$product_id)->update(['is_new' => 'N']);
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

      