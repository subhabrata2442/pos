<?php

namespace App\Http\Controllers;

use App\Helper\Media;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Abcdefg;
use App\Models\Material;
use App\Models\Service;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\MasterProducts;
use App\Models\VendorCode;
use App\Models\Supplier;
use App\Models\Warehouse;

use App\Models\SupplierGst;
use App\Models\ProductRelationshipSize;

use App\Models\PurchaseInwardStock;
use App\Models\InwardStockProducts;

use App\Models\BranchStockProducts;
use App\Models\BranchStockProductSellPrice;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class AjaxController extends Controller {
	
	public function ajaxpost(Request $request){
		$action = $request->action;
		$this->{'ajaxpost_' . $action}($request);
	}
	
	public function ajaxpost_get_sell_product_search($request) {
		$search				= $request->search;
		$searchTerm 		= $search;
		$reservedSymbols 	= ['-', '+', '<', '>', '@', '(', ')', '~'];
		$searchTerm 		= str_replace($reservedSymbols, ' ', $searchTerm);
		$searchValues 		= preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
		
		//print_r($searchValues);exit;
		
		$res=MasterProducts::query()->where('product_name', 'LIKE', "%{$search}%")->take(20)->get();
		$result=[];
		
		foreach($res as $row){
			$result[]=array(
				'id'				=> $row->id,
				'product_barcode'	=> $row->product_barcode,
				'product_name'		=> $row->product_name,
				'product_size'		=> $row->size->name,
			);
		}
		
		$return_data['result']	= $result;
		$return_data['status']	= 1;
		
		echo json_encode($return_data);		
	}
	
	public function ajaxpost_get_sell_product_byId($request) {
		$search_id	= $request->product_id;
		$res = MasterProducts::find($search_id);
		
		$branch_id=1;
		$return_data=[];
		$product_result=[];
		if(isset($res->id)){
			if($res->id!=''){
				$brand_slug		= $res->slug;
				$category_id	= $res->category_id;
				$subcategory_id	= $res->subcategory_id;
				$size_id		= $res->size_id;
				$size_title		= $res->size->name;
				$bottle_case	= $res->qty;
				
				$item_result = Product::where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->get();
				$product_id	 = isset($item_result[0]->id)?$item_result[0]->id:'';
				
				$branch_stock_product_result = BranchStockProducts::where('branch_id',$branch_id)->where('product_id',$product_id)->where('size_id',$size_id)->get();
				$branch_stock_product_id	= isset($branch_stock_product_result[0]->id)?$branch_stock_product_result[0]->id:'';
				
				if($branch_stock_product_id!=''){
					$branch_product_stock_sell_price_info=BranchStockProductSellPrice::where('stock_id',$branch_stock_product_id)->get();
					$item_prices=[];
					foreach($branch_product_stock_sell_price_info as $row){
						$item_prices[]=array(
							'price_id'=>$row->id,
							'selling_price'=>$row->selling_price,
							'offer_price'=>$row->offer_price,
							'product_mrp'=>$row->product_mrp,
							'w_qty'=>$row->w_qty,
							'c_qty'=>$row->c_qty,
						);
					}
					
					$product_result=array(
						'product_id'				=> $product_id,
						'branch_stock_product_id'	=> $branch_stock_product_id,
						'product_barcode'			=> $item_result[0]->product_barcode,
						'brand_name'				=> trim($item_result[0]->product_name).' ('.$size_title.')',
						'item_prices'				=> $item_prices,
					);	
					$return_data['status']	= 1;
					$return_data['product_result']	= $product_result;
				}else{
					$return_data['status']	= 0;
				}		
			}else{
				$return_data['status']	= 0;
			}
		}else{
			$return_data['status']	= 0;
		}
		
		echo json_encode($return_data);	
	}
	
	public function ajaxpost_get_product($request) {
		$search	= $request->search;
		$searchTerm =$search;
		$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
		$searchTerm = str_replace($reservedSymbols, ' ', $searchTerm);
		$searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
		
		//print_r($searchValues);exit;
		
		$res=MasterProducts::query()->where('product_name', 'LIKE', "%{$search}%")->take(20)->get();
		//print_r($size_result);exit;

		/*$res = MasterProducts::where(function ($q) use ($searchValues) {
			foreach ($searchValues as $value) {
				$q->orWhere('product_name', 'like', "%{$value}%");
			}
		})->take(20)->get();*/
		
		$result=[];
		
		foreach($res as $row){
			$result[]=array(
				'id'=>$row->id,
				'product_name'=>$row->product_name,
				'product_size'=>$row->size->name,
			);
		}
		
		$return_data['result']	= $result;
		$return_data['status']	= 1;
		
		echo json_encode($return_data);		
	}
	public function ajaxpost_get_product_byId($request) {
		$product_id	= $request->product_id;
		$res = MasterProducts::find($product_id);
		
		$product_result=[];
		if(isset($res->id)){
			if($res->id!=''){
				$brand_slug		= $res->slug;
				$category_id	= $res->category_id;
				$subcategory_id	= $res->subcategory_id;
				$size_id		= $res->size_id;
				$size_title		= $res->size->name;
				$bottle_case	= $res->qty;
				$item_result 	= Product::where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->get();		
				$product_id	 	= isset($item_result[0]->id)?$item_result[0]->id:'';
				if($product_id!=''){
					$item_size_result=ProductRelationshipSize::where('product_id',$product_id)->where('size_id',$size_id)->get();
					$product_id	 		= isset($item_size_result[0]->id)?$item_size_result[0]->id:'';
					$strength	 		= isset($item_size_result[0]->strength)?$item_size_result[0]->strength:0;
					$retailer_margin	= isset($item_size_result[0]->retailer_margin)?$item_size_result[0]->retailer_margin:0;
					$round_off	 		= isset($item_size_result[0]->round_off)?$item_size_result[0]->round_off:0;
					$sp_fee	 			= isset($item_size_result[0]->special_purpose_fee)?$item_size_result[0]->special_purpose_fee:0;
					$product_mrp	 	= isset($item_size_result[0]->product_mrp)?$item_size_result[0]->product_mrp:0;
					
					//$size_result=Size::where('id',$size_id)->get();
					
					//print_r($size_title);exit;
					
					$product_result=array(
						'product_id'		=> $product_id,
						'product_barcode'	=> $item_result[0]->product_barcode,
						'bottle_case'		=> $bottle_case,
						'category'			=> $item_result[0]->category->name,
						'category_id'		=> $category_id,
						'sub_category'		=> $item_result[0]->subcategory->name,
						'subcategory_id'	=> $subcategory_id,
						'brand_name'		=> trim($item_result[0]->product_name),
						'brand_slug'		=> $brand_slug,	 
						'measure'			=> $size_title,
						'size_id'			=> $size_id,
						'batch_no'			=> '',
						'strength'			=> trim($strength),
						'retailer_margin'	=> trim($retailer_margin),
						'round_off'			=> trim($round_off),
						'sp_fee'			=> trim($sp_fee),
						'qty'				=> 1,
						'bl'				=> 0,
						'lpl'				=> 0,
						'product_mrp'		=> trim($product_mrp),
						'unit_cost'			=> trim($product_mrp),
						'total_cost'		=> trim($product_mrp),
					);
				}
			}
		}
		$status=0;
		if(count($product_result)>0){
			$status=1;
		}
		$return_data['result']	= $product_result;
		$return_data['status']	= $status;
		echo json_encode($return_data);	
	}
	
	public function ajaxpost_get_suppliers($request) {
		$search	= $request->search;
		$searchTerm =$search;
		$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
		$searchTerm = str_replace($reservedSymbols, ' ', $searchTerm);
		$searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
		
		//print_r($searchValues);exit;
		
		

		$res = Supplier::where(function ($q) use ($searchValues) {
			foreach ($searchValues as $value) {
				$q->orWhere('company_name', 'like', "%{$value}%");
				$q->orWhere('first_name', 'like', "%{$value}%");
				$q->orWhere('last_name', 'like', "%{$value}%");
			}
		})->get();
		
		$result=[];
		
		
		foreach($res as $row){
			$result[]=array(
				'id'=>$row->id,
				'company_name'	=> $row->company_name,
				'first_name'	=> $row->first_name,
				'last_name'		=> $row->last_name,
			);
			
		}
		
		$return_data['result']	= $result;
		$return_data['status']	= 1;
		
		echo json_encode($return_data);		
	}
	
	public function ajaxpost_get_supplier_byId($request) {
		$company_id	= $request->company_id;
		$res = Supplier::find($company_id);
		$supplierGst=SupplierGst::where('supplier_id',$company_id)->get();
		$return_data['supplier']		= $res;
		$return_data['supplier_gst']	= $supplierGst;
		$return_data['status']			= 1;
		echo json_encode($return_data);	
	}
	
	public function ajaxpost_get_warehouse($request) {
		$search	= $request->search;
		$searchTerm =$search;
		$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
		$searchTerm = str_replace($reservedSymbols, ' ', $searchTerm);
		$searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
		
		//print_r($searchValues);exit;
		
		$res = Warehouse::where(function ($q) use ($searchValues) {
			foreach ($searchValues as $value) {
				$q->orWhere('company_name', 'like', "%{$value}%");
			}
		})->get();
		
		$result=[];
		
		
		foreach($res as $row){
			$result[]=array(
				'id'=>$row->id,
				'company_name'	=> $row->company_name,
			);
		}
		
		$return_data['result']	= $result;
		$return_data['status']	= 1;
		
		//print_r($return_data);exit;
		
		echo json_encode($return_data);		
	}
	
	public function ajaxpost_get_warehouse_byId($request) {
		$company_id	= $request->company_id;
		$res = Warehouse::find($company_id);
		$return_data['warehouse']		= $res;
		$return_data['status']			= 1;
		echo json_encode($return_data);	
	}
	
	
	
	public function ajaxpost_add_new_product($request) {
		$inward_stock	= $request->inward_stock;
		
		$product_invoice_items	= [];
		
		if(isset($inward_stock)){
			if(count($inward_stock)>0){
				for($i=0;count($inward_stock)>$i;$i++){
					$barcode		= $inward_stock[$i]['new_product_barcode'];
					$bottle_case	= $inward_stock[$i]['new_product_bottle_case'];	
					$in_case		= $inward_stock[$i]['p_new_product_in_case'];
					$category		= $inward_stock[$i]['new_product_category'];
					$sub_category	= $inward_stock[$i]['new_product_sub_category'];
					$brand_name		= $inward_stock[$i]['new_brand_name'];
					$batch_no		= $inward_stock[$i]['new_batch_no'];
					$measure		= $inward_stock[$i]['new_measure'];	
					$strength		= $inward_stock[$i]['new_strength'];
					$bl				= $inward_stock[$i]['new_bl'];	
					$lpl			= $inward_stock[$i]['new_lpl'];
					$retailer_margin= $inward_stock[$i]['new_retailer_margin'];
					$round_off		= $inward_stock[$i]['new_round_off'];
					$sp_fee			= $inward_stock[$i]['new_sp_fee'];
					$product_mrp	= $inward_stock[$i]['new_product_mrp'];
					$total_cost		= $inward_stock[$i]['new_product_total_cost'];
					
					$size='';
					if($measure!=''){
						$size=rtrim($measure, '.');
						$size=strtolower($size);
					}
					
					//echo '<pre>';print_r($size);exit;
					
					$category_title=trim($category);
					$category_result=Category::where('name',$category_title)->get();
					if(count($category_result)>0){
						$category_id=isset($category_result[0]->id)?$category_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $category_title,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Category::create($feature_data);
						$category_id=$feature->id;
					}
					
					
					
					$size_result=Size::where('name',$size)->get();
					if(count($size_result)>0){
						$size_id=isset($size_result[0]->id)?$size_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $size,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Size::create($feature_data);
						$size_id=$feature->id;
					}
					
					$type_result=Subcategory::where('name',$sub_category)->get();
					if(count($type_result)>0){
						$subcategory_id=isset($type_result[0]->id)?$type_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $sub_category,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Subcategory::create($feature_data);
						$subcategory_id=$feature->id;
					}
					
					
					
					$brand_slug	= Media::create_slug(trim($brand_name));
					
					$brand_result=Brand::where('slug',$brand_slug)->get();
					if(count($brand_result)>0){
						$brand_id=isset($brand_result[0]->id)?$brand_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $brand_name,
							'slug'			=> $brand_slug,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Brand::create($feature_data);
						$brand_id=$feature->id;
					}
					
					$product_result=Product::where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->get();
					$n=Product::count();
					$product_barcode=str_pad($n + 1, 5, 0, STR_PAD_LEFT);
					
					if($barcode!=''){
						$product_barcode=$barcode;
					}
					
					if(count($product_result)>0){
						$product_id=$product_result[0]->id;
					}else{
						$product = Product::create([
							'product_name' 		=> $brand_name,
							'slug' 				=> $brand_slug,
							'product_barcode'	=> $product_barcode,
							'default_qty' 		=> 1,
							'category_id' 		=> $category_id,
							'brand_id' 			=> $brand_id,
							'subcategory_id' 	=> $subcategory_id
						]);
						$product_id=$product->id;
						//$product_id=3000+$i;
					}
					
					$product_size_result=ProductRelationshipSize::where('product_id',$product_id)->where('size_id',$size_id)->get();
					if(count($product_size_result)>0){
					}else{
						$size_cost_data=array(
							'product_id'  			=> $product_id,
							'size_id'  				=> $size_id,
							'cost_rate'  			=> $product_mrp,
							'product_mrp'  			=> $product_mrp,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $round_off,
							'special_purpose_fee'  	=> $sp_fee,
							'free_discount_percent' => 0,
							'free_discount_amount'  => 0,
							'created_at'			=> date('Y-m-d')
						);
						ProductRelationshipSize::create($size_cost_data);
					}
					
					$total_qty=$bottle_case*$in_case;
					
					$product_invoice_items[]=array(
						'product_id'		=> $product_id,
						'product_barcode'	=> $product_barcode,
						'category'			=> $category_title,
						'category_id'		=> $category_id,
						'sub_category'		=> $sub_category,
						'subcategory_id'	=> $subcategory_id,
						'brand_name'		=> trim($brand_name),
						'brand_slug'		=> $brand_slug,	 
						'measure'			=> $size,
						'size_id'			=> $size_id,
						'batch_no'			=> trim($batch_no),
						'strength'			=> trim($strength),
						'retailer_margin'	=> trim($retailer_margin),
						'round_off'			=> trim($round_off),
						'sp_fee'			=> trim($sp_fee),
						'total_cases'		=> $in_case,
						'in_cases'			=> $bottle_case,
						'qty'				=> $total_qty,
						'bl'				=> $bl,
						'lpl'				=> $lpl,
						'product_mrp'		=> trim($product_mrp),
						'unit_cost'			=> trim($product_mrp),
						'total_cost'		=> trim($total_cost)
					);
					
					
					
					
					
					
					$current_year=date('Y');
					$product_result=MasterProducts::where('product_name',$brand_name)->where('year',$current_year)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->get();
					
					if(count($product_result)>0){
					}else{
						$master_data=array(
							'product_barcode'  		=> $product_barcode,
							'product_name'  		=> $brand_name,
							'slug'					=> $brand_slug,
							'mrp'  					=> $product_mrp,
							'category_id'  			=> $category_id,
							'size_id'  				=> $size_id,
							'brand_id'  			=> $brand_id,
							'subcategory_id'  		=> $subcategory_id,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $round_off,
							'special_purpose_fee'  	=> $sp_fee,
							'qty'  					=> $in_case,
							'year'  				=> $current_year,
							'created_at'			=> date('Y-m-d')
						);
						
						MasterProducts::create($master_data);
					}
					
				}
			}
		}
		
		$return_data['result']	= $product_invoice_items;
		$return_data['success']	= 0;
		echo json_encode($return_data);
		
	}
	
	public function ajaxpost_add_inward_stock($request) {
		$inward_stock	= $request->inward_stock;
		
		//print_r($inward_stock);exit;
		
		$purchaseStockData=array(
			'supplier_id'  		=> $inward_stock['supplier_id'],
			'warehouse_id'  	=> $inward_stock['warehouse_id'],
			'invoice_no'  		=> $inward_stock['invoice_no'],
			'tp_no'  			=> $inward_stock['tp_no'],
			'purchase_date'  	=> $inward_stock['invoice_purchase_date'],
			'inward_date'  		=> $inward_stock['invoice_inward_date'],
			'payment_method'  	=> $inward_stock['payment_method'],
			'payment_date'  	=> $inward_stock['payment_date'],
			'payment_ref_no'  	=> $inward_stock['payment_ref_no'],
			'invoice_stock'  	=> $inward_stock['invoice_stock'],	
			'invoice_stock_type'  	=> $inward_stock['invoice_stock_type'],
			//'tax_type'  		=> isset($inward_stock['stock_inward_tax_type'])?$inward_stock['stock_inward_tax_type']:'',
			//'due_days'  		=> isset($inward_stock['due_days'])?$inward_stock['due_days']:'',
			//'due_date'  		=> isset($inward_stock['due_date'])?$inward_stock['due_date']:'',
			'total_qty'  		=> $inward_stock['total_qty'],
			'gross_amount'  	=> $inward_stock['gross_amount'],
			'tax_amount'  		=> $inward_stock['tax_amount'],
			'sub_total'  		=> $inward_stock['sub_total'],
			'shipping_note'  	=> $inward_stock['shipping_note'],
			'additional_note'  	=> $inward_stock['additional_note'],
			'created_at'		=> date('Y-m-d')	
		);
		
		//print_r($purchaseStockData);exit;
		
		if($inward_stock['invoice_stock']=='warehouse'){
			$purchaseStockData['w_qty']=$inward_stock['total_qty'];
		}else{
			$purchaseStockData['c_qty']=$inward_stock['total_qty'];
		}
		
		//print_r($purchaseStockData);exit;
		
		//print_r($purchaseStockData);exit;
		$purchaseInwardStock	= PurchaseInwardStock::create($purchaseStockData);
		$purchaseInwardStockId	= $purchaseInwardStock->id;
		
		//print_r($purchaseInwardStockId);exit;
		
		//print_r($inward_stock['product_detail']);exit;
		
		//$purchaseInwardStockId=1;
		if(isset($inward_stock['product_detail'])){
			if(count($inward_stock['product_detail'])>0){
				for($i=0;count($inward_stock['product_detail'])>$i;$i++){
					
					$product_id=$inward_stock['product_detail'][$i]['product_id'];
					$product_info=Product::find($inward_stock['product_detail'][$i]['product_id']);
					
					$prev_stock=isset($product_info->stock_qty)?$product_info->stock_qty:0;
					$current_stock=0;
					$current_stock += $prev_stock;
					$current_stock += $inward_stock['product_detail'][$i]['product_qty'];
					$current_stock += $inward_stock['product_detail'][$i]['free_qty'];
					
					$measure=$inward_stock['product_detail'][$i]['measure'];
					$size_arr=explode(' ',$measure);
					$size_result=Size::query()->where('name', 'LIKE', "%{$size_arr[0]}%")->get();
					$size_id=isset($size_result[0]->id)?$size_result[0]->id:0;
					
					$product_mrp		= isset($inward_stock['product_detail'][$i]['product_mrp'])?$inward_stock['product_detail'][$i]['product_mrp']:0;
					$product_offer_mrp	= isset($inward_stock['product_detail'][$i]['offer_price'])?$inward_stock['product_detail'][$i]['offer_price']:0;
					$product_qty		= isset($inward_stock['product_detail'][$i]['product_qty'])?$inward_stock['product_detail'][$i]['product_qty']:0;
					
					
					//print_r($product_mrp);exit;
					
					$branch_id=1;
					$branch_product_stock_info=BranchStockProducts::where('branch_id',$branch_id)->where('product_id',$product_id)->where('size_id',$size_id)->get();
					if(count($branch_product_stock_info)>0){
						$branch_product_stock_sell_price_info=BranchStockProductSellPrice::where('stock_id',$branch_product_stock_info[0]->id)->where('selling_price',$product_mrp)->get();
						
						$sell_price_id=isset($branch_product_stock_sell_price_info[0]->id)?$branch_product_stock_sell_price_info[0]->id:'';
						
						if($sell_price_id!=''){
							$sell_price_w_qty = 0;
							$sell_price_c_qty = 0;
							if($inward_stock['invoice_stock']=='warehouse'){
								$sell_price_w_qty +=isset($branch_product_stock_sell_price_info[0]->w_qty)?$branch_product_stock_sell_price_info[0]->w_qty:0;
								$sell_price_w_qty +=$product_qty;
								BranchStockProductSellPrice::where('id', $sell_price_id)->update(['w_qty' => $sell_price_w_qty]);
							}else{
								$sell_price_c_qty +=isset($branch_product_stock_sell_price_info[0]->c_qty)?$branch_product_stock_sell_price_info[0]->c_qty:'';
								$sell_price_c_qty +=$product_qty;
								BranchStockProductSellPrice::where('id', $sell_price_id)->update(['c_qty' => $sell_price_c_qty]);
							}	
						}else{
							$sell_price_w_qty = 0;
							$sell_price_c_qty = 0;
							if($inward_stock['invoice_stock']=='warehouse'){
								$sell_price_w_qty=$product_qty;
							}else{
								$sell_price_c_qty=$product_qty;
							}
							
							$branchProductStockSellPriceData=array(
								'stock_id'		=> $branch_product_stock_info[0]->id,
								'w_qty'  		=> $sell_price_w_qty,
								'c_qty'  		=> $sell_price_c_qty,
								'selling_price'	=> $product_mrp,
								'offer_price'  	=> $product_offer_mrp,
								'product_mrp'  	=> $product_mrp,
								'created_at'	=> date('Y-m-d')
							);
							BranchStockProductSellPrice::create($branchProductStockSellPriceData);
						}
					}else{
						$branchProductStockData=array(
							'branch_id'		=> $branch_id,
							'product_id'  	=> $product_id,
							'size_id'  		=> $size_id,
							'created_at'	=> date('Y-m-d')	
						);
						$branchStockProducts=BranchStockProducts::create($branchProductStockData);
						$stock_id=$branchStockProducts->id;
						
						$sell_price_w_qty = 0;
						$sell_price_c_qty = 0;
						
						if($inward_stock['invoice_stock']=='warehouse'){
							$sell_price_w_qty=$product_qty;
						}else{
							$sell_price_c_qty=$product_qty;
						}
						
						$branchProductStockSellPriceData=array(
							'stock_id'		=> $stock_id,
							'w_qty'  		=> $sell_price_w_qty,
							'c_qty'  		=> $sell_price_c_qty,
							'selling_price'	=> $product_mrp,
							'offer_price'  	=> $product_offer_mrp,
							'product_mrp'  	=> $product_mrp,
							'created_at'	=> date('Y-m-d')
						);
						BranchStockProductSellPrice::create($branchProductStockSellPriceData);
					}
					
					//Product::where('id', $product_id)->update(['stock_qty' => $current_stock]);
					
					$inward_stock_product=array(
						'inward_stock_id'			=> $purchaseInwardStockId,
						'product_id'  				=> $inward_stock['product_detail'][$i]['product_id'],
						'size_id'  					=> $size_id,
						'case_qty'  				=> $inward_stock['product_detail'][$i]['product_case_qty'],
						'bottle_case'  				=> $inward_stock['product_detail'][$i]['product_bottle_case'],
						'loose_qty'  				=> $inward_stock['product_detail'][$i]['product_loose_qty'],
						'product_qty'  				=> $inward_stock['product_detail'][$i]['product_qty'],
						'free_qty'  				=> $inward_stock['product_detail'][$i]['free_qty'],			
						'strength'  				=> $inward_stock['product_detail'][$i]['strength'],
						'bl'  						=> $inward_stock['product_detail'][$i]['bl'],
						'lpl'  						=> $inward_stock['product_detail'][$i]['lpl'],
						'retailer_margin'  			=> $inward_stock['product_detail'][$i]['retailer_margin'],
						'round_off'  				=> $inward_stock['product_detail'][$i]['round_off'],
						'sp_fee'  					=> $inward_stock['product_detail'][$i]['sp_fee'],
						'batch_no'  				=> $inward_stock['product_detail'][$i]['batch_no'],
						'base_price'  				=> isset($inward_stock['product_detail'][$i]['base_price'])?$inward_stock['product_detail'][$i]['base_price']:0,
						'base_discount_percent'  	=> isset($inward_stock['product_detail'][$i]['base_discount_percent'])?$inward_stock['product_detail'][$i]['base_discount_percent']:0,
						'base_discount_amount'  	=> isset($inward_stock['product_detail'][$i]['base_discount_amount'])?$inward_stock['product_detail'][$i]['base_discount_amount']:0,
						
						'scheme_discount_percent'  	=> isset($inward_stock['product_detail'][$i]['scheme_discount_percent'])?$inward_stock['product_detail'][$i]['scheme_discount_percent']:0,
						
						'scheme_discount_amount'  	=> isset($inward_stock['product_detail'][$i]['scheme_discount_amount'])?$inward_stock['product_detail'][$i]['scheme_discount_amount']:0,
						
						'free_discount_percent'  	=> isset($inward_stock['product_detail'][$i]['free_discount_percent'])?$inward_stock['product_detail'][$i]['free_discount_percent']:0,
						
						'free_discount_amount'  	=> isset($inward_stock['product_detail'][$i]['free_discount_amount'])?$inward_stock['product_detail'][$i]['free_discount_amount']:0,
						
						'free_discount_amount'  	=> isset($inward_stock['product_detail'][$i]['free_discount_amount'])?$inward_stock['product_detail'][$i]['free_discount_amount']:0,
						
						'cost_rate'  				=> isset($inward_stock['product_detail'][$i]['cost_rate'])?$inward_stock['product_detail'][$i]['cost_rate']:0,
						'total_cost_rate'  			=> isset($inward_stock['product_detail'][$i]['total_cost_rate'])?$inward_stock['product_detail'][$i]['total_cost_rate']:0,
						'gst_percent'  				=> isset($inward_stock['product_detail'][$i]['gst_percent'])?$inward_stock['product_detail'][$i]['gst_percent']:0,
						'gst_amount'  				=> isset($inward_stock['product_detail'][$i]['gst_amount'])?$inward_stock['product_detail'][$i]['gst_amount']:0,
						
						'extra_charge'  			=> isset($inward_stock['product_detail'][$i]['extra_charge'])?$inward_stock['product_detail'][$i]['extra_charge']:0,
						'profit_percent'  			=> isset($inward_stock['product_detail'][$i]['profit_percent'])?$inward_stock['product_detail'][$i]['profit_percent']:0,
						'profit_amount'  			=> isset($inward_stock['product_detail'][$i]['profit_amount'])?$inward_stock['product_detail'][$i]['profit_amount']:0,
						'sell_price'  				=> isset($inward_stock['product_detail'][$i]['sell_price'])?$inward_stock['product_detail'][$i]['sell_price']:0,
						
						'selling_gst_percent'  		=> isset($inward_stock['product_detail'][$i]['selling_gst_percent'])?$inward_stock['product_detail'][$i]['selling_gst_percent']:0,
						'selling_gst_amount'  		=> isset($inward_stock['product_detail'][$i]['selling_gst_amount'])?$inward_stock['product_detail'][$i]['selling_gst_amount']:0,
						'offer_price'  				=> $inward_stock['product_detail'][$i]['offer_price'],
						'product_mrp'  				=> $inward_stock['product_detail'][$i]['product_mrp'],
						'wholesale_price'  			=> 0,
						'mfg_date'  				=> '',
						'expiry_date'  				=> '',
						'total_cost'  				=> $inward_stock['product_detail'][$i]['total_cost'],
						'created_at'				=> date('Y-m-d')
					);
					InwardStockProducts::create($inward_stock_product);
					//print_r($inward_stock_product);exit;
				}
			}
		}
		
		$return_data['msg']		= 'Successfully added';
		$return_data['status']	= 1;
		echo json_encode($return_data);
	}
	
	
	
	
	
	public function ajaxpost_set_feature_option($request) {
		$product_type	= $request->product_type;
		$feature_title	= $request->feature_title;
		
		$return_data	= [];
		
		$return_data['msg']		= 'Something went wrong. Please try later!';
		$return_data['status']	= 0;
		if($product_type=='category'){
			$count=Category::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This category already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Category::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='size'){
			$count=Size::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This size already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Size::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='brand'){
			$count=Brand::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This brand already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Brand::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='subcategory'){
			$count=Subcategory::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This subcategory already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Subcategory::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='color'){
			$count=Color::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This color already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Color::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='material'){
			$count=Material::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This Material already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Material::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='abcdefg'){
			$count=Abcdefg::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This abcdefg already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Abcdefg::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='service'){
			$count=Service::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This service already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=Service::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		else if($product_type=='vendor_code'){
			$count=VendorCode::where('name',$feature_title)->count();
			if($count>0){
				$return_data['msg']		= 'This vendor code already exists!';
				$return_data['status']	= 0;
			}else{
				$feature_data=array(
					'name'  		=> $feature_title,
					'created_at'	=> date('Y-m-d')
				);
				$feature=VendorCode::create($feature_data);
				$feature_id=$feature->id;
				
				$return_data['val']		= $feature_id;
				$return_data['title']	= $feature_title;
				$return_data['msg']		= 'Successfully added';
				$return_data['status']	= 1;
				
			}
		}
		
		//print_r($return_data);exit;
		echo json_encode($return_data);
		
		
		
		
	}
	
}
