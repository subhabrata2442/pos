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

use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Abcdefg;
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

use App\Models\Warehouse;

use Smalot\PdfParser\Parser;

class PurchaseOrderController extends Controller
{
	
	public function pos_type()
    {
		$data['heading'] 		= 'Purchase Order';
		return view('admin.purchase_order.type', compact('data'));
	}
	
	public function invoice_upload_test(Request $request){
		$file = $request->file('upload_invoice_pdf');
		/*$request->validate([
			'file' => 'required|mimes:pdf',
        ]);*/
		$fileName = $file->getClientOriginalName();
		
		error_reporting(0);
		//include '../pdf_parser/vendor/autoload.php';
			
		$pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
		
		$metaData = $pdf->getDetails();
		
		$product_result=[];
		$new_product_result=[];
		
		$tp_no='';
		$tp_no_row_id='';
		$data = $pdf->getPages()[0]->getDataTm();
		
		//echo '<pre>';print_r($data);exit;
		
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			
			if (stripos($th_head, "Transport Pass No") !== false) {
				$tp_no_row_id	= $i;
				break;
			}
		}
		if(isset($data[$tp_no_row_id][1])){
			if($data[$tp_no_row_id][1]!=''){
				$tp_no_arr	= explode(':',$data[$tp_no_row_id][1]);
				$tp_no		= isset($tp_no_arr[1])?$tp_no_arr[1]:'';	
			}
		}
		
		$invoice_date_row_id='';
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			if (stripos($th_head, "Date") !== false) {
				$invoice_date_row_id	= $i;
				break;
			}
		}
		
		$invoice_date='';
		if(isset($data[$invoice_date_row_id][1])){
			if($data[$invoice_date_row_id][1]!=''){
				$invoice_date_arr	= explode(':',$data[$invoice_date_row_id][1]);
				$invoice_date_arr	= isset($invoice_date_arr[1])?trim($invoice_date_arr[1]):'';
				$invoice_date_arr2	= explode(' ',$invoice_date_arr);
				
				$invoice_date_arr3	= explode('/',$invoice_date_arr2[0]);
				$invoice_date		= $invoice_date_arr3[0].'-'.$invoice_date_arr3[1].'-'.$invoice_date_arr3[2];	
				$invoice_date		= date('Y-m-d',strtotime($invoice_date));
			}
		}
		
		
		//print_r($invoice_date);exit;
		
		
		
		
		if(isset($metaData['Pages'])){
			if($metaData['Pages']>0){
				for($p=10;$metaData['Pages']>$p;$p++){
					$data = $pdf->getPages()[$p]->getDataTm();
					
					$start_product_row_id	= '';
					$remove_row_ids=[];
					$product_cat_ids=[];
					$product_size_ids=[];
					$product_inCases_ids=[];
					
					for($i=0; count($data)>$i; $i++){
						$th_head	= str_replace(' ','',$data[$i][1]);
						if($th_head==''){
							$remove_row_ids[]	= $i;
						}
					}
					
					for($i=0; count($remove_row_ids)>$i; $i++){
						unset($data[$remove_row_ids[$i]]);
					}
					
					$brand_liquor_data=[];
					foreach($data as $key=>$val){
						$index_val	= trim($val[1]);
						$brand_liquor_data[]=$index_val;
					}
					
					
					//echo '<pre>';print_r($brand_liquor_data);exit;
					
					
					for($i=0; count($brand_liquor_data)>$i; $i++){
						$th_head	= str_replace(' ','',$brand_liquor_data[$i]);
						if (preg_match('/\BrandName\b/', $th_head)) {
							$start_product_row_id	= ($i+23);
							break;
						}
					}
					
					//echo '<pre>';print_r($start_product_row_id);exit;
					
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= str_replace(' ','',$brand_liquor_data[$i]);
						//$th_head	= trim($brand_liquor_data[$i]);
						
						if (preg_match('/\IMFL\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						
						/*if (preg_match('/\OS\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\Country Liquor\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\OSBI\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\CS\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}*/
					}
					
					//echo '<pre>';print_r($product_cat_ids);exit;
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= $brand_liquor_data[$i];
						if (stripos($th_head, "Ml.") !== false) {
							$product_size_ids[]	= $i;
						}
					}
					
					//echo '<pre>';print_r($product_size_ids);exit;
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= $brand_liquor_data[$i];
						if (stripos($th_head, "- 0") !== false) {
							$product_inCases_ids[]	= $i;
						}
					}
					
					//echo '<pre>';print_r($product_inCases_ids);exit;
					
					for($i=0; count($product_cat_ids)>$i; $i++){
						$index_1=$product_cat_ids[$i];
						$index_2=($index_1+1);
						$index_3=$product_size_ids[$i];
						$index_4=($product_inCases_ids[$i]+1);
						$index_5=($product_inCases_ids[$i]+2);
						$index_6=($product_inCases_ids[$i]+3);
						$index_7=($product_inCases_ids[$i]+4);
						
						//echo '<pre>';print_r($index_4);exit;
						
						$brand_name_length=($product_size_ids[$i]-$index_2);
						
						$brand_name='';
						for($j=1; $brand_name_length>$j; $j++){
							$p_index=$index_2+$j;
							$brand_name .= trim($brand_liquor_data[$p_index]).' ';
						}
						//$brand_name_arr = explode('[',$brand_name);
						//$brand_name		= isset($brand_name_arr[0])?trim($brand_name_arr[0]):'';
						
						//echo '<pre>';print_r($brand_name);exit;
						
						$current_year=date('Y');
						
						$batch_no_length=($product_inCases_ids[$i]-1);
						
						if (stripos($brand_liquor_data[$batch_no_length], $current_year) !== false) {
							$batch_index_count=2;
						}else{
							$batch_index_count=3;
						}
						
						//$batch_index_count=2;
						
						$batch_length=($product_inCases_ids[$i]-$batch_index_count);
						
						$batch_no='';
						for($b=0; $batch_index_count>$b; $b++){
							$b_index=$batch_length+$b;
							$batch_no .= trim($brand_liquor_data[$b_index]).' ';
						}
						
						$brand_slug	= Media::create_slug(trim($brand_name));
						$category_title=trim($brand_liquor_data[$index_1]);
						$sub_category_title=trim($brand_liquor_data[$index_2]);
						
						
						
						
						
							$total_cost	= trim($brand_liquor_data[$index_7]);
							$total_qty	= trim($brand_liquor_data[$index_4]);
							$unit_mrp	= $total_cost/$total_qty;
							
							$new_product_result[]=array(
								'category'			=> $category_title,
								'sub_category'		=> $sub_category_title,
								'brand_name'		=> trim($brand_name),	 
								'measure'			=> $size_title,
								'batch_no'			=> trim($batch_no),
								'strength'			=> '',
								'retailer_margin'	=> '',
								'round_off'			=> '',
								'sp_fee'			=> '',
								'qty'				=> trim($brand_liquor_data[$index_4]),
								'bl'				=> trim($brand_liquor_data[$index_5]),
								'lpl'				=> trim($brand_liquor_data[$index_6]),
								'product_mrp'		=> trim($unit_mrp),
								'unit_cost'			=> trim($unit_mrp),
								'total_cost'		=> trim($brand_liquor_data[$index_7])
							);
						
					}	
				}
			}
		}
		
		echo '<pre>';
		print_r($product_result);
		print_r($new_product_result);
		exit;
		
		if(count($product_result)>0){
			$return_data['result']			= $product_result;
			$return_data['new_result']		= $new_product_result;
			$return_data['tp_no']			= $tp_no;
			$return_data['invoice_date']	= $invoice_date;
			
			$return_data['success']	= 1;
		}else{
			$return_data['success']	= 0;
		}
		
		echo json_encode($return_data);
		
		//echo '<pre>';print_r($return_data);exit;		
	}
	
	public function invoice_upload(Request $request){
		$file = $request->file('upload_invoice_pdf');
		/*$request->validate([
			'file' => 'required|mimes:pdf',
        ]);*/
		$fileName = $file->getClientOriginalName();
		
		error_reporting(0);
		//include '../pdf_parser/vendor/autoload.php';
			
		$pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
		
		$metaData = $pdf->getDetails();
		
		$product_result=[];
		$new_product_result=[];
		$invoice_product_result=[];
		
		$product_ids=[];
		$product_slugs=[];
		
		$tp_no='';
		$tp_no_row_id='';
		$data = $pdf->getPages()[0]->getDataTm();
		
		//echo '<pre>';print_r($data);exit;
		
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			
			if (stripos($th_head, "Transport Pass No") !== false) {
				$tp_no_row_id	= $i;
				break;
			}
		}
		if(isset($data[$tp_no_row_id][1])){
			if($data[$tp_no_row_id][1]!=''){
				$tp_no_arr	= explode(':',$data[$tp_no_row_id][1]);
				$tp_no		= isset($tp_no_arr[1])?$tp_no_arr[1]:'';	
			}
		}
		
		if($tp_no!=''){
			$check_tp_no=PurchaseInwardStock::query()->where('tp_no', 'LIKE', "%{$tp_no}%")->count();
			
			if($check_tp_no>0){
				$return_data['msg']		= 'This invoice is already uploaded!';
				$return_data['success']	= 0;
				echo json_encode($return_data);exit;
			}
		}
		
		$invoice_date_row_id='';
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			if (stripos($th_head, "Date") !== false) {
				$invoice_date_row_id	= $i;
				break;
			}
		}
		
		
		
		$invoice_date='';
		if(isset($data[$invoice_date_row_id][1])){
			if($data[$invoice_date_row_id][1]!=''){
				$invoice_date_arr	= explode(':',$data[$invoice_date_row_id][1]);
				$invoice_date_arr	= isset($invoice_date_arr[1])?trim($invoice_date_arr[1]):'';
				$invoice_date_arr2	= explode(' ',$invoice_date_arr);
				
				$invoice_date_arr3	= explode('/',$invoice_date_arr2[0]);
				$invoice_date		= $invoice_date_arr3[0].'-'.$invoice_date_arr3[1].'-'.$invoice_date_arr3[2];	
				$invoice_date		= date('Y-m-d',strtotime($invoice_date));
			}
		}
		
		//print_r($invoice_date);exit;
		$invoice_no_row=[];
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			if (stripos($th_head, "& Date") !== false) {
				$invoice_no_row[]	= $i;
			}
		}
		$invoice_no='';
		if(count($invoice_no_row)>0){
			$invoice_no_row_id=isset($invoice_no_row[1])?$invoice_no_row[1]:'';
			if($invoice_no_row_id!=''){
				$invoice_no_length=($invoice_no_row_id-3);
				for($j=0; 3>$j; $j++){
					$invoice_index=$invoice_no_length+$j;
					$invoice_no .= trim($data[$invoice_index][1]);
				}
			}
		}
		
		//print_r($invoice_date);exit;
		$warehouse_row_id='';
		for($i=0; count($data)>$i; $i++){
			$th_head	= trim($data[$i][1]);
			if (stripos($th_head, "Bevco") !== false) {
				$warehouse_row_id	= $i;
				break;
			}
		}
		
		$warehouse_info=[];
		$warehouse_name='';
		if($warehouse_row_id!=''){
			for($j=0; 5>$j; $j++){
				$warehouse_index=$warehouse_row_id+$j;
				$warehouse_name .= ' '.trim($data[$warehouse_index][1]);
				$warehouse_name = str_replace( ',', '', $warehouse_name);
			}	
		}
		
		if($warehouse_name!=''){
			$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
			$searchTerm = str_replace($reservedSymbols, ' ', $warehouse_name);
			$searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
			
			$warehouse_res = Warehouse::where(function ($q) use ($searchValues) {
				foreach ($searchValues as $value) {
					$q->orWhere('company_name', 'like', "%{$value}%");
				}
				})->take(1)->get();
				
			foreach($warehouse_res as $row){
				$warehouse_info=array(
					'id'=>$row->id,
					'company_name'=>$row->company_name,
					'email'=>$row->email,
					'phone_no'=>$row->phone_no,
					'city'=>$row->city,
					'pin'=>$row->pin,
					'address'=>$row->address,
					'area'=>$row->area,
				);
			}				
		}
		
		//print_r($warehouse_info);exit;
		
		
		
		
		if(isset($metaData['Pages'])){
			if($metaData['Pages']>0){
				for($p=0;$metaData['Pages']>$p;$p++){
					$data = $pdf->getPages()[$p]->getDataTm();
					
					$start_product_row_id	= '';
					$remove_row_ids=[];
					$product_cat_ids=[];
					$product_size_ids=[];
					$product_inCases_ids=[];
					
					for($i=0; count($data)>$i; $i++){
						$th_head	= str_replace(' ','',$data[$i][1]);
						if($th_head==''){
							$remove_row_ids[]	= $i;
						}
					}
					
					for($i=0; count($remove_row_ids)>$i; $i++){
						unset($data[$remove_row_ids[$i]]);
					}
					
					$brand_liquor_data=[];
					foreach($data as $key=>$val){
						$index_val	= trim($val[1]);
						$brand_liquor_data[]=$index_val;
					}
					
					
					//echo '<pre>';print_r($brand_liquor_data);exit;
					
					
					for($i=0; count($brand_liquor_data)>$i; $i++){
						$th_head	= str_replace(' ','',$brand_liquor_data[$i]);
						if (preg_match('/\BrandName\b/', $th_head)) {
							$start_product_row_id	= ($i+23);
							break;
						}
					}
					
					//$th_head	= str_replace(' ','',$brand_liquor_data[$i]);
					
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= str_replace(' ','',$brand_liquor_data[$i]);
						//echo '<pre>';print_r($th_head);exit;
						if($th_head=='IMFL'){
							$product_cat_ids[]	= $i;
						}
						if($th_head=='OSBI'){
							$product_cat_ids[]	= $i;
						}
						if($th_head=='CS'){
							$product_cat_ids[]	= $i;
						}
						/*if (preg_match('/\OS\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\Country Liquor\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\OSBI\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}
						if (preg_match('/\CS\b/', $th_head)) {
							$product_cat_ids[]	= $i;
						}*/
					}
					
					//echo '<pre>';print_r($product_cat_ids);exit;
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= $brand_liquor_data[$i];
						if (stripos($th_head, "Ml.") !== false) {
							$product_size_ids[]	= $i;
						}
					}
					
					//echo '<pre>';print_r($product_size_ids);exit;
					
					for($i=$start_product_row_id; count($brand_liquor_data)>$i; $i++){
						$th_head	= $brand_liquor_data[$i];
						
						for($n=0;100>$n;$n++){
							$match_in_case="- ".$n;
							if (stripos($th_head, $match_in_case) !== false) {
								if(!in_array($i, $product_inCases_ids)){
									$product_inCases_ids[]	= $i;
								}
							}
						}
						
						
						/*if (stripos($th_head, "- 1") !== false) {
							$product_inCases_ids[]	= $i;
						}
						if (stripos($th_head, "- 3") !== false) {
							$product_inCases_ids[]	= $i;
						}
						if (stripos($th_head, "- 7") !== false) {
							$product_inCases_ids[]	= $i;
						}
						if (stripos($th_head, "- 11") !== false) {
							$product_inCases_ids[]	= $i;
						}
						if (stripos($th_head, "- 41") !== false) {
							$product_inCases_ids[]	= $i;
						}*/
					}
					/*if(count($product_inCases_ids)>0){
						$product_inCases_ids = array_unique($product_inCases_ids);
						
						for()
					}*/
					
					//echo '<pre>';print_r($product_inCases_ids);exit;
					
					for($i=0; count($product_cat_ids)>$i; $i++){
						$index_1=$product_cat_ids[$i];
						$index_2=($index_1+1);
						$index_3=$product_size_ids[$i];
						$index_4=($product_inCases_ids[$i]+1);
						$index_5=($product_inCases_ids[$i]+2);
						$index_6=($product_inCases_ids[$i]+3);
						$index_7=($product_inCases_ids[$i]+4);
						
						/*if($i==1){
							break;
						}*/
						
						//echo '<pre>';print_r($index_4);exit;
						
						$brand_name_length=($product_size_ids[$i]-$index_2);
						
						$brand_name='';
						for($j=1; $brand_name_length>$j; $j++){
							$p_index=$index_2+$j;
							$brand_name .= trim($brand_liquor_data[$p_index]).' ';
							$brand_name	= str_replace("[Pet Bottle]", "", $brand_name);
							$brand_name	= str_replace("[Can]", "", $brand_name);
						}
						$category_title2='';
						if (stripos($brand_name, "Spirit") !== false) {
							$brand_name	= str_replace("Spirit", "", $brand_name);
							$category_title2='Spirit';
						}
						//print_r($brand_name);exit;
						//category_title
						
						//$brand_name_arr = explode('[',$brand_name);
						//$brand_name		= isset($brand_name_arr[0])?trim($brand_name_arr[0]):'';
						//echo str_replace("[Pet Bottle]", "", $brand_name);
						//echo '<pre>';print_r($brand_name);exit;
						
						$current_year=date('Y');
						
						$batch_no_length=($product_inCases_ids[$i]-1);
						
						if (stripos($brand_liquor_data[$batch_no_length], $current_year) !== false) {
							$batch_index_count=2;
						}else{
							$batch_index_count=3;
						}
						
						//$batch_index_count=2;
						
						$batch_length=($product_inCases_ids[$i]-$batch_index_count);
						
						$batch_no='';
						for($b=0; $batch_index_count>$b; $b++){
							$b_index=$batch_length+$b;
							$batch_no .= trim($brand_liquor_data[$b_index]).' ';
						}
						$brand_slug	= Media::create_slug(trim($brand_name));
						
						$in_cases_info	= trim($brand_liquor_data[$product_inCases_ids[$i]]);
						//echo $product_inCases_ids[$i].'</br>';
						$in_cases_arr	= explode('-',$in_cases_info);
						$total_cases	= isset($in_cases_arr[0])?trim($in_cases_arr[0]):0;
						$loose_qty		= isset($in_cases_arr[1])?trim($in_cases_arr[1]):0;
						//print_r($in_cases);exit;
						
						
						
						$category_title =trim($brand_liquor_data[$index_1]);
						$category_result=Category::where('name',$category_title)->get();
						$category_id=isset($category_result[0]->id)?$category_result[0]->id:0;
						
						$sub_category_title=trim($brand_liquor_data[$index_2].' '.$category_title2);	
						$sub_category_result=Subcategory::where('name',$sub_category_title)->get();
						$subcategory_id=isset($sub_category_result[0]->id)?$sub_category_result[0]->id:0;
						//print_r($subcategory_id);exit;
						
						$size_title=trim($brand_liquor_data[$index_3]);
						$size_id=0;
						if($size_title!=''){
							$size_arr=explode(' ',$size_title);
							$size_result=Size::query()->where('name', 'LIKE', "%{$size_arr[0]}%")->get();
							$size_id=isset($size_result[0]->id)?$size_result[0]->id:0;
						}
						
						$product_slug	= Media::create_slug(trim($brand_slug.' '.$category_title.' '.$sub_category_title.' '.$size_title.' '.$batch_no));
						if(!in_array($product_slug, $product_slugs)){
							$product_slugs[]=$product_slug;
							$item_result = Product::where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->get();
							$product_id	 = isset($item_result[0]->id)?$item_result[0]->id:'';
							if($product_id!=''){
								
								$item_bottle_case_qty_nfo = MasterProducts::where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->first();
								$item_bottle_case_qty=isset($item_bottle_case_qty_nfo->qty)?$item_bottle_case_qty_nfo->qty:'0';
								
								//echo 'slug='.$brand_slug.' category_id='.$category_id.' subcategory_id='.$subcategory_id.' product_id='.$product_id.'</br>';
								
								
								//echo 'slug='.$brand_slug.' category_id='.$category_id.' subcategory_id='.$subcategory_id.' product_id='.$product_id.'</br>';
								$item_size_result=ProductRelationshipSize::where('product_id',$product_id)->where('size_id',$size_id)->get();
								$strength	 		= isset($item_size_result[0]->strength)?$item_size_result[0]->strength:0;
								$retailer_margin	= isset($item_size_result[0]->retailer_margin)?$item_size_result[0]->retailer_margin:0;
								$round_off	 		= isset($item_size_result[0]->round_off)?$item_size_result[0]->round_off:0;
								$sp_fee	 			= isset($item_size_result[0]->special_purpose_fee)?$item_size_result[0]->special_purpose_fee:0;
								$product_mrp	 	= isset($item_size_result[0]->product_mrp)?$item_size_result[0]->product_mrp:0;
								
								$total_cost	= trim($brand_liquor_data[$index_7]);
								$total_qty	= trim($brand_liquor_data[$index_4]);
								$unit_mrp	= $total_cost/$total_qty;
								$in_cases	= $total_qty/$total_cases;
								
								if($in_cases!=''){
									$in_cases=round(trim($in_cases));
								}
								
								$invoice_product_result[]=array(
									'product_id'		=> $product_id,
									'product_barcode'	=> $item_result[0]->product_barcode,
									'category'			=> $category_title,
									'category_id'		=> $category_id,
									'sub_category'		=> $sub_category_title,
									'subcategory_id'	=> $subcategory_id,
									'brand_name'		=> trim($brand_name),
									'brand_slug'		=> $brand_slug,	 
									'measure'			=> $size_title,
									'size_id'			=> $size_id,
									'batch_no'			=> trim($batch_no),
									'strength'			=> trim($strength),
									'retailer_margin'	=> trim($retailer_margin),
									'round_off'			=> trim($round_off),
									'sp_fee'			=> trim($sp_fee),
									'bottle_case'		=> $item_bottle_case_qty,
									'total_cases'		=> $total_cases,
									'loose_qty'			=> $loose_qty,
									'in_cases'			=> $in_cases,
									'qty'				=> (($total_cases*$item_bottle_case_qty)+$loose_qty),//trim($brand_liquor_data[$index_4]),
									'bl'				=> trim($brand_liquor_data[$index_5]),
									'lpl'				=> trim($brand_liquor_data[$index_6]),
									'product_mrp'		=> trim($product_mrp),
									'unit_cost'			=> trim($unit_mrp),
									'total_cost'		=> trim($brand_liquor_data[$index_7])
								);
							}else{
								$product_slugs[]=$product_slug;
								$total_cost	= trim($brand_liquor_data[$index_7]);
								$total_qty	= trim($brand_liquor_data[$index_4]);
								$unit_mrp	= $total_cost/$total_qty;
								$in_cases	= $total_qty/$total_cases;
								
								$new_product_result[]=array(
									'category'			=> $category_title,
									'sub_category'		=> $sub_category_title,
									'brand_name'		=> trim($brand_name),	 
									'measure'			=> $size_title,
									'batch_no'			=> trim($batch_no),
									'strength'			=> '',
									'retailer_margin'	=> '',
									'round_off'			=> '',
									'sp_fee'			=> '',
									'total_cases'		=> $total_cases,
									'in_cases'			=> trim($in_cases),
									'qty'				=> trim($brand_liquor_data[$index_4]),
									'bl'				=> trim($brand_liquor_data[$index_5]),
									'lpl'				=> trim($brand_liquor_data[$index_6]),
									'product_mrp'		=> trim($unit_mrp),
									'unit_cost'			=> trim($unit_mrp),
									'total_cost'		=> trim($brand_liquor_data[$index_7])
								);
							}
						}
					}	
				}
			}
		}
		
		/*echo '<pre>';
		print_r($product_slugs);
		print_r($invoice_product_result);
		print_r($new_product_result);
		exit;*/
		
		
		
		if(count($invoice_product_result)>0 || count($new_product_result)>0){
			
			$return_data['result']			= $invoice_product_result;
			
			$return_data['new_result']		= $new_product_result;
			$return_data['warehouse']		= $warehouse_info;
			$return_data['tp_no']			= $tp_no;
			$return_data['invoice_no']		= $invoice_no;
			$return_data['invoice_date']	= $invoice_date;
			
			
			
			$return_data['success']	= 1;
		}else{
			$return_data['msg']		= 'This invoice is already uploaded!';
			$return_data['success']	= 0;
		}
		//echo '<pre>';print_r($return_data);exit;
		
		echo json_encode($return_data);
		
		//echo '<pre>';print_r($return_data);exit;		
	}
	
    public function pos_create(Request $request)
    {
		
        DB::beginTransaction();
        try {
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
                $purchase = PurchaseOrder::create([
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
                $purchase_product = [];
                $sub_total = $request->delivery_charge ?? 0;
                foreach ($request->product_id as $key => $value) {
                    $purchase_product[$key] = [
                        'purchase_order_id' => $purchase->id,
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
                PurchaseOrder::find($purchase->id)->update([
                    'sub_total' => $sub_total,
                    'order_no' =>  'PO-' . rand(1111111, 9999999) . $purchase->id,
                ]);
                DB::commit();
                return redirect()->route('admin.stock.purchase-order.edit', [base64_encode($purchase->id)])->with('success', 'Purchase order placed successfully');
            }
            $data = [];
			
            $data['heading'] 		= 'Add Order';
            $data['breadcrumb'] 	= ['Stock', 'Purchase Order', 'Add'];
            $data['supplier'] 		= Supplier::all();
            $data['product'] 		= Product::all();
			
            return view('admin.purchase_order.pos', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	public function create_order(Request $request)
    {
		
        DB::beginTransaction();
        try {
            $data = [];
			$spplier_code='bevco-17';
			
            $data['heading'] 		= 'Purchase Order';
            $data['breadcrumb'] 	= ['Purchase Order', 'Add'];
            $data['supplier'] 		= Supplier::where('sup_code',$spplier_code)->first();
            $data['product'] 		= Product::all();
			
			//echo '<pre>';print_r($data['supplier']->company_name);exit;
			
			
            return view('admin.purchase_order.add', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

	//added by palash
	public function updateInwardStock(Request $request,$inward_stock_id){
		//echo $inward_stock_id;die;
		DB::beginTransaction();
        try {
			$data = [];
			$spplier_code='bevco-17';
			
            $data['heading'] 		= 'Purchase Order';
            $data['breadcrumb'] 	= ['Purchase Order', 'Edit'];
            $data['supplier'] 		= Supplier::where('sup_code',$spplier_code)->first();
            $data['product'] 		= Product::all();
            $data['inward_stock_id'] = $inward_stock_id;
            $data['inward_stock_type'] = 'edit';
			
            return view('admin.purchase_order.add', compact('data'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
	}

	public function ajaxPurchaseById(Request $request){

		//dd($request->all());
		$purchase_inward_stock = PurchaseInwardStock::where('id',base64_decode($request->id))->first();
		$return_data['tp_no']			= $purchase_inward_stock->tp_no;
        $return_data['invoice_no']		= $purchase_inward_stock->invoice_no;
        $return_data['invoice_date']	= date('Y-m-d',strtotime($purchase_inward_stock->purchase_date));
		$return_data['warehouse']		= $purchase_inward_stock->warehouse;
		//$return_data['stock_products']	= $invoice_product_result;
		$invoice_product_result = [];
		if(count($purchase_inward_stock->inwardStockProducts)>0){
			foreach($purchase_inward_stock->inwardStockProducts as $inwardStockProducts){
				$invoice_product_result[]=[
					'product_id'		=> $inwardStockProducts->product_id,
					'product_barcode'	=> $inwardStockProducts->product->product_barcode,
					'category'			=> $inwardStockProducts->product->category->name,
					'category_id'		=> $inwardStockProducts->product->category->id,
					'sub_category'		=> $inwardStockProducts->product->subcategory->name,
					'subcategory_id'	=> $inwardStockProducts->product->subcategory->id,
					'brand_name'		=> $inwardStockProducts->product->brand->name,
					'brand_slug'		=> $inwardStockProducts->product->brand->slug,	 
					'measure'			=> $inwardStockProducts->size->name,
					'size_id'			=> $inwardStockProducts->size_id,
					'batch_no'			=> $inwardStockProducts->batch_no,
					'strength'			=> $inwardStockProducts->strength,
					'retailer_margin'	=> $inwardStockProducts->retailer_margin,
					'round_off'			=> $inwardStockProducts->round_off,
					'sp_fee'			=> $inwardStockProducts->sp_fee,
					'bottle_case'		=> $inwardStockProducts->bottle_case,
					'total_cases'		=> $inwardStockProducts->case_qty,
					'loose_qty'			=> $inwardStockProducts->loose_qty,
					'in_cases'			=> '',
					'qty'				=> $inwardStockProducts->product_qty,
					'bl'				=> $inwardStockProducts->bl,
					'lpl'				=> $inwardStockProducts->lpl,
					'product_mrp'		=> $inwardStockProducts->product_mrp,
					'unit_cost'			=> '',
					'total_cost'		=> $inwardStockProducts->total_cost,
				];
			}
		}

		$return_data['stock_products']	= $invoice_product_result;
        $return_data['success']	= 1;
		//return response()->json(['status'=>true,'massage'=>'User details saved Successfully']);
		echo json_encode($return_data);
	}
}
