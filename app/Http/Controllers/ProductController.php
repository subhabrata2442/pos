<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierCompanyMobile;
use App\Models\SupplierBank;
use App\Models\SupplierGst;
use App\Models\SupplierContactDetails;
use App\Models\User;
use App\Helper\Media;
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
use App\Models\Product;
use App\Models\MasterProducts;
use App\Models\ProductRelationshipSize;
use App\Models\BarProductSizePrice;


use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
	public function product_upload(Request $request){
		$file = $request->file('product_upload_file');
		if($file){
			$filename = $file->getClientOriginalName();
			$extension = $file->getClientOriginalExtension();
			$tempPath = $file->getRealPath();
			$fileSize = $file->getSize();
			
			if($extension!='csv'){
				return redirect()->back()->with('error', 'Something error occurs!');
			}
			$location = 'uploads';
			$file->move($location, $filename);
			$filepath = public_path($location . "/" . $filename);
			
			$file = fopen($filepath, "r");
			$importData_arr = array();
			$i = 0; 
			while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
				$num = count($filedata);
				if ($i == 0) {
					$i++;
					continue;
				}
				for ($c = 0; $c < $num; $c++) {
					$importData_arr[$i][] = $filedata[$c];
				}
				$i++;
			}
			
			//echo '<pre>';print_r($importData_arr);exit;
			
			$j = 0;
			$brand_data=[];
			foreach ($importData_arr as $importData) {
				$category				= $importData[0];
				$type 					= $importData[1];
				$size 					= $importData[3];
				$strength				= $importData[4];
				$retailer_margin 		= $importData[5];
				$unit_price				= $importData[6];
				$special_purpose_fee	= $importData[7];
				$mrp					= $importData[8];
				$unit_qty				= $importData[9];
				
				
				if($category!=''){
					$brand_name		= trim($importData[2]);
					$brand_slug 	= $this->create_slug($brand_name);
					
					$category_title=trim($category);
					$category_result=Category::where('name',$category_title)->where('food_type',1)->get();
					if(count($category_result)>0){
						$category_id=isset($category_result[0]->id)?$category_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $category_title,
							'food_type'  	=> 1,
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
					
					$type_result=Subcategory::where('name',$type)->where('food_type',1)->get();
					if(count($type_result)>0){
						$subcategory_id=isset($type_result[0]->id)?$type_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $type,
							'food_type'  	=> 1,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Subcategory::create($feature_data);
						$subcategory_id=$feature->id;
					}
					
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
					
					if(count($product_result)>0){
						$product_id=$product_result[0]->id;
					}else{
						$product = Product::create([
							'product_name' 		=> $brand_name,
							'slug' 				=> $brand_slug,	
							'product_barcode'	=> $product_barcode,
							'default_qty' 		=> 1,
							//'cost_rate' 		=> $mrp,
							//'offer_price' 	=> $mrp,
							//'product_mrp' 	=> $mrp,
							'category_id' 		=> $category_id,
							'brand_id' 			=> $brand_id,
							'subcategory_id' 	=> $subcategory_id
						]);
						$product_id=$product->id;
					}
					$product_size_result=ProductRelationshipSize::where('product_id',$product_id)->where('size_id',$size_id)->get();
					if(count($product_size_result)>0){
						$size_cost_data=array(
							'cost_rate'  			=> $mrp,
							'product_mrp'  			=> $mrp,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $unit_price,
							'special_purpose_fee'  	=> $special_purpose_fee,
							'free_discount_percent' => 0,
							'free_discount_amount'  => 0,
							//'created_at'			=> date('Y-m-d')
						);
						//ProductRelationshipSize::where('id', $product_size_result[0]->id)->update($size_cost_data);
					}else{
						$size_cost_data=array(
							'product_id'  			=> $product_id,
							'size_id'  				=> $size_id,
							'cost_rate'  			=> $mrp,
							'product_mrp'  			=> $mrp,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $unit_price,
							'special_purpose_fee'  	=> $special_purpose_fee,
							'free_discount_percent' => 0,
							'free_discount_amount'  => 0,
							'created_at'			=> date('Y-m-d')
						);
						ProductRelationshipSize::create($size_cost_data);
					}
					
					$current_year=date('Y');
					$product_result=MasterProducts::where('product_name',$brand_name)->where('year',$current_year)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->where('size_id',$size_id)->get();
					if(count($product_result)>0){
						$master_data=array(
							'product_name'  		=> $brand_name,
							'slug'					=> $brand_slug,
							'mrp'  					=> $mrp,
							'category_id'  			=> $category_id,
							'size_id'  				=> $size_id,
							'brand_id'  			=> $brand_id,
							'subcategory_id'  		=> $subcategory_id,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $unit_price,
							'special_purpose_fee'  	=> $special_purpose_fee,
							'qty'  					=> $unit_qty,
							'updated_at'			=> date('Y-m-d')
						);
						MasterProducts::where('id', $product_result[0]->id)->update($master_data);
					}else{
						$master_data=array(
							'product_name'  		=> $brand_name,
							'slug'					=> $brand_slug,
							'mrp'  					=> $mrp,
							'category_id'  			=> $category_id,
							'size_id'  				=> $size_id,
							'brand_id'  			=> $brand_id,
							'subcategory_id'  		=> $subcategory_id,
							'strength'  			=> $strength,
							'retailer_margin'  		=> $retailer_margin,
							'round_off'  			=> $unit_price,
							'special_purpose_fee'  	=> $special_purpose_fee,
							'qty'  					=> $unit_qty,
							'year'  				=> $current_year,
							'created_at'			=> date('Y-m-d')
						);
						MasterProducts::create($master_data);
					}
					
					//echo $j.'</br>';
					
				}
			$j++;}
			
			//echo '<pre>';print_r($brand_data);exit;
			
			return redirect()->back()->with('success', 'Product created successfully');
		}
		
	}
	
	public function bar_product_price_upload(Request $request){
		$file = $request->file('product_upload_file');
		if($file){
			$filename = $file->getClientOriginalName();
			$extension = $file->getClientOriginalExtension();
			$tempPath = $file->getRealPath();
			$fileSize = $file->getSize();
			
			if($extension!='csv'){
				return redirect()->back()->with('error', 'Something error occurs!');
			}
			$location = 'uploads';
			$file->move($location, $filename);
			$filepath = public_path($location . "/" . $filename);
			
			$file = fopen($filepath, "r");
			$importData_arr = array();
			$i = 0; 
			while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
				$num = count($filedata);
				if ($i == 0) {
					$i++;
					continue;
				}
				for ($c = 0; $c < $num; $c++) {
					$importData_arr[$i][] = $filedata[$c];
				}
				$i++;
			}
			
			
			
			$j = 0;
			$brand_data=[];
			foreach ($importData_arr as $importData) {
				$size=[];
				$barcode			= $importData[0];
				$brand_code			= $importData[1];
				$category			= $importData[2];
				$type 				= $importData[3];
				$sn 				= $importData[4];
				$product_name		= $importData[5];
				$size[30] 			= isset($importData[6])?$importData[6]:'';
				$size[60] 			= isset($importData[7])?$importData[7]:'';
				$size[90] 			= isset($importData[8])?$importData[8]:'';
				$size[180] 			= isset($importData[10])?$importData[9]:'';
				$size[187] 			= isset($importData[11])?$importData[10]:'';
				$size[200] 			= isset($importData[12])?$importData[11]:'';
				$size[250] 			= isset($importData[13])?$importData[12]:'';
				$size[275] 			= isset($importData[14])?$importData[13]:'';
				$size[300] 			= isset($importData[15])?$importData[14]:'';
				$size[330] 			= isset($importData[16])?$importData[15]:'';
				$size[360] 			= isset($importData[17])?$importData[16]:'';
				$size[375] 			= isset($importData[18])?$importData[17]:'';
				$size[500] 			= isset($importData[19])?$importData[18]:'';
				$size[600] 			= isset($importData[20])?$importData[19]:'';
				$size[650] 			= isset($importData[21])?$importData[20]:'';
				$size[700] 			= isset($importData[22])?$importData[21]:'';
				$size[720] 			= isset($importData[23])?$importData[22]:'';
				$size[750] 			= isset($importData[24])?$importData[23]:'';
				$size[1000] 		= isset($importData[25])?$importData[24]:'';
				$size[2000] 		= isset($importData[26])?$importData[25]:'';
				
				if($category!=''){
					$category_title	= trim($category);
					
					
					$category_result=Category::where('name',$category_title)->where('food_type',1)->get();
					if(count($category_result)>0){
						$category_id=isset($category_result[0]->id)?$category_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $category_title,
							'food_type'  	=> 1,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Category::create($feature_data);
						$category_id=$feature->id;
					}
					
					$subcategory_result=Subcategory::where('name',$type)->where('food_type',1)->get();
					if(count($subcategory_result)>0){
						$subcategory_id=isset($subcategory_result[0]->id)?$subcategory_result[0]->id:0;
					}else{
						$feature_data=array(
							'name'  		=> $type,
							'food_type'  	=> 1,
							'created_at'	=> date('Y-m-d')
						);
						$feature=Subcategory::create($feature_data);
						$subcategory_id=$feature->id;
					}
					
					$branch_id		= 1;
					$brand_slug 	= $this->create_slug($product_name);
					$product_result	= Product::select('id')->where('branch_id',$branch_id)->where('slug',$brand_slug)->where('category_id',$category_id)->where('subcategory_id',$subcategory_id)->first();	
					$product_id		= isset($product_result->id)?$product_result->id:'';
					
					//echo '<pre>';print_r($size);exit;
					if($product_id!=''){
						foreach($size as $key=>$val){
							$size_ml	= $key;
							$size_val	= $val;
							if($size_val!=''){
								$size_result=Size::where('ml',$size_ml)->get();
								if(count($size_result)>0){
									$size_id=isset($size_result[0]->id)?$size_result[0]->id:0;
									BarProductSizePrice::where('product_id', $product_id)->where('size_id', $size_id)->delete();
									$product_mrp=$size_val;
									$size_cost_data=array(
										'product_id'  			=> $product_id,
										'size_id'  				=> $size_id,
										'product_mrp'  			=> $product_mrp
									);
									
									//echo '<pre>';print_r($size_cost_data);exit;
									BarProductSizePrice::create($size_cost_data);
								}	
							}
						}
					}					
				}
			$j++;}
			
			//echo '<pre>';print_r($brand_data);exit;
			
			return redirect()->back()->with('success', 'Product created successfully');
		}
		
	}
	
    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'product_name' 	=> 'required',
                    'product_code' 	=> 'required',
                    //'sku_code' 		=> 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
				
				$product_image='';
				
				if ($file = $request->file('upload_photo')) {
					
					//echo '<pre>';print_r($file);exit;
                    $fileData = Media::uploads($file, 'uploads/product');
                    $product_image = $fileData['filePath'];
                }
				
				$n=Product::count();
				$product_barcode=str_pad($n + 1, 5, 0, STR_PAD_LEFT);
				
				
				$size=$request->size;
				$cost_rate=$request->cost_rate;
				$cost_gst_percent=$request->cost_gst_percent;
				$cost_gst_amount=$request->cost_gst_amount;
				$cost_price=$request->cost_price;
				$extra_charge=$request->extra_charge;
				$profit_percent=$request->profit_percent;
				$profit_amount=$request->profit_amount;
				$selling_price=$request->selling_price;
				$sell_gst_percent=$request->sell_gst_percent;
				$sell_gst_amount=$request->sell_gst_amount;
				$offer_price=$request->offer_price;
				$product_mrp=$request->product_mrp;
				$wholesale_price=$request->wholesale_price;
				
                $product = Product::create([
                    'product_name' 	=> $request->product_name,
					'product_barcode'=> $product_barcode,
                    'product_code' 	=> $request->product_code,
                    'hsn_sac_code' 	=> $request->hsn_sac_code,
                    'sku_code' 		=> $request->sku_code,
                    'uqc_id' 		=> $request->uqc_id,
                    'days_before_product_expiry'	=> $request->days_before_product_expiry,
					'alert_product_qty' => $request->alert_product_qty,
                    'default_qty' 		=> $request->default_qty,
                    'product_desc' 		=> $request->product_desc,
                    'product_note' 		=> $request->product_note,
                    'cost_rate' 		=> $request->cost_rate,
                    'cost_gst_percent'	=> $request->cost_gst_percent,	
					'cost_gst_amount' 	=> $request->cost_gst_amount,
                    'cost_price' 		=> $request->cost_price,
                    'extra_charge' 		=> $request->extra_charge,
                    'profit_percent' 	=> $request->profit_percent,
                    'profit_amount' 	=> $profit_amount[0],
                    'selling_price'		=> $selling_price[0],
					'sell_gst_percent' 	=> $sell_gst_percent[0],
                    'sell_gst_amount' 	=> $sell_gst_amount[0],
                    'offer_price' 		=> $offer_price[0],
                    'product_mrp' 		=> $product_mrp[0],
                    'wholesale_price' 	=> $wholesale_price[0],
                    'image'				=> $product_image,	
					'category_id' 		=> $request->category,
                    'size_id' 			=> $size[0],
                    'brand_id' 			=> $request->brand,
                    'subcategory_id' 	=> $request->subcategory,
                    'color_id' 			=> $request->color,
                    'material_id'		=> $request->material,	
					'vendor_code_id' 	=> $request->vendor_code,
                    'abcdefg_id' 		=> $request->abcdefg,
                    'service_id' 		=> $request->service,
                    'supplier_barcode' 	=> $request->supplier_barcode,
                ]);
				
				$product_id=$product->id;
				for($i=0;count($size)>$i;$i++){
					$size_cost_data=array(
						'product_id '  			=> $product_id,
						'size_id'  				=> $size[$i],
						'cost_rate'  			=> $cost_rate[$i],
						'cost_gst_percent'  	=> $cost_gst_percent[$i],
						'cost_gst_amount'  		=> $cost_gst_amount[$i],
						'cost_price '  			=> $cost_price[$i],
						'extra_charge'  		=> $extra_charge[$i],
						'profit_percent'  		=> $profit_percent[$i],
						'profit_amount'  		=> $profit_amount[$i],
						'selling_price'  		=> $selling_price[$i],
						'sell_gst_percent '  	=> $sell_gst_percent[$i],
						'sell_gst_amount'  		=> $sell_gst_amount[$i],
						'offer_price'  			=> $offer_price[$i],
						'product_mrp'  			=> $product_mrp[$i],
						'wholesale_price'  		=> $wholesale_price[$i],
						'free_discount_percent' => 0,
						'free_discount_amount'  => 0,
						'created_at'			=> date('Y-m-d')
					);
					
					//echo '<pre>';print_r($size_cost_data);exit;
					
					ProductRelationshipSize::create($supplier_bank_data);
				}
				
                return redirect()->back()->with('success', 'Product created successfully');
            }
            $data = [];
            $data['heading'] 		= 'Product Add';
            $data['breadcrumb'] 	= ['Product', 'Add'];
            $data['supplier'] 		= Supplier::all();
			$data['measurement'] 	= Measurement::all();
			$data['category'] 		= Category::all();
			$data['size'] 			= Size::all();
			$data['brand'] 			= Brand::all();
			$data['subcategory'] 	= Subcategory::all();
			$data['color'] 			= Color::all();
			$data['material'] 		= Material::all();
			$data['vendorCode'] 	= VendorCode::all();
			$data['abcdefg'] 		= Abcdefg::all();
			$data['service'] 		= Service::all();
			$data['thumb']			= asset('images/placeholder.png');
			
			//print_r($data);exit;
			
			
			
            return view('admin.product.add', compact('data'));
        } catch (\Exception $e) {
            
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	

    public function list(Request $request)
    {
		//$product = Product::orderBy('id', 'desc')->get();
		
		//echo '<pre>';print_r($product[0]->image);
		//echo asset('' . $product[0]->image);exit;
		//$path = Storage::disk('public')->path('uploads/product/165692952618Hours-Bucharest1-superJumbo-v3.jpg');
		//echo Storage::disk('public')->get('uploads/product/165692952618Hours-Bucharest1-superJumbo-v3.jpg');exit;
		
		//echo Storage::disk('public')->get('storage/uploads/product/165692952618Hours-Bucharest1-superJumbo-v3.jpg');exit;
		
		//echo Storage::disk('public')->get('uploads/product/165692952618Hours-Bucharest1-superJumbo-v3.jpg');exit;
		
		//$path = Storage::disk('public')->path('uploads/product/165692952618Hours-Bucharest1-superJumbo-v3.jpg');
		
		//echo '<pre>';print_r($path);exit;
		
		//$product = MasterProducts::orderBy('id', 'desc')->get();
		
		//echo '<pre>';print_r($product[0]->category->name);exit;
		
		//echo '<pre>';print_r($product[0]->category->name);exit;
		
		
        try {
            if ($request->ajax()) {
                $product = MasterProducts::orderBy('id', 'asc')->get();
				
                return DataTables::of($product)
                    ->addColumn('product_name', function ($row) {
                        return $row->product_name;
                    })
                    ->addColumn('category', function ($row) {
                        return isset($row->category->name)?$row->category->name:'';
                    })
					->addColumn('subcategory', function ($row) {
                        return isset($row->subcategory->name)?$row->subcategory->name:'';
                    })
                    ->addColumn('size', function ($row) {
                        return isset($row->size->name)?$row->size->name:'';
                    })
                    ->addColumn('strength', function ($row) {
                        return $row->strength;
                    })
                    ->addColumn('retailer_margin', function ($row) {
                        return $row->retailer_margin;
                    })
                    ->addColumn('round_off', function ($row) {
                        return $row->round_off;
                    })
					->addColumn('special_purpose_fee', function ($row) {
                        return $row->special_purpose_fee;
                    })
                    ->addColumn('mrp', function ($row) {
                        return $row->mrp;
                    })
                    ->addColumn('qty', function ($row) {
                        return $row->qty;
                    })
                    ->make(true);
            }
            $data = [];
            $data['heading'] = 'Product List';
            $data['breadcrumb'] = ['Product', 'List'];
            return view('admin.product.list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function edit($id, Request $request)
    {
        try {
            $product_id = base64_decode($id);
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'product_code' => 'required|unique:products,product_code,' . $product_id,
                    /*'default_purchase_price' => 'required|numeric',
                    'purchase_tax_rate' => 'required|numeric',
                    'min_order_qty' => 'required|numeric',
                    'product_desc' => 'required',
                    'pack_size' => 'required',*/
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
				
				//print_r($product_id);exit;
				
				
                $product = Product::find($product_id)->update([
                    'product_name' 	=> $request->product_name,
                    'product_code' 	=> $request->product_code,
                    'hsn_sac_code' 	=> $request->hsn_sac_code,
                    'sku_code' 		=> $request->sku_code,
                    'uqc_id' 		=> $request->uqc_id,
                    'days_before_product_expiry'	=> $request->days_before_product_expiry,
					'alert_product_qty' => $request->alert_product_qty,
                    'default_qty' 		=> $request->default_qty,
                    'product_desc' 		=> $request->product_desc,
                    'product_note' 		=> $request->product_note,
                    'cost_rate' 		=> $request->cost_rate,
                    'cost_gst_percent'	=> $request->cost_gst_percent,	
					'cost_gst_amount' 	=> $request->cost_gst_amount,
                    'cost_price' 		=> $request->cost_price,
                    'extra_charge' 		=> $request->extra_charge,
                    'profit_percent' 	=> $request->profit_percent,
                    'profit_amount' 	=> $request->profit_amount,
                    'selling_price'		=> $request->selling_price,
					'sell_gst_percent' 	=> $request->sell_gst_percent,
                    'sell_gst_amount' 	=> $request->sell_gst_amount,
                    'offer_price' 		=> $request->offer_price,
                    'product_mrp' 		=> $request->product_mrp,
                    'wholesale_price' 	=> $request->wholesale_price,
                    //'image'				=> $product_image,	
					
					'image_caption' 		=> $request->product_image_caption,
					'category_id' 			=> $request->category,
                    'size_id' 				=> $request->size,
                    'brand_id' 				=> $request->brand,
                    'subcategory_id' 		=> $request->subcategory,
                    'color_id' 				=> $request->color,
                    'material_id'			=> $request->material,	
					'vendor_code_id' 		=> $request->vendor_code,
                    'abcdefg_id' 			=> $request->abcdefg,
                    'service_id' 			=> $request->service,
                    'supplier_barcode' 	=> $request->supplier_barcode,
                ]);
                //ProductSupplier::where('product_id', $product_id)->delete();

                return redirect()->back()->with('success', 'Product updated successfully');
            }
            $data = [];
            $data['heading'] 		= 'Product Edit';
            $data['breadcrumb'] 	= ['Product', 'Edit'];
			$data['thumb']			= asset('images/placeholder.png');
			$data['measurement'] 	= Measurement::all();
			$data['category_list'] 		= Category::all();
			$data['size'] 			= Size::all();
			$data['brand'] 			= Brand::all();
			$data['subcategory'] 	= Subcategory::all();
			$data['color'] 			= Color::all();
			$data['material'] 		= Material::all();
			$data['vendorCode'] 	= VendorCode::all();
			$data['abcdefg'] 		= Abcdefg::all();
			$data['service'] 		= Service::all();
            $data['products'] = Product::find($product_id);
			
            return view('admin.product.edit', compact('data'));
        } catch (\Exception $e) {
           
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
	
	public function create_slug($string){
		$replace = '-';
	   	$string = strtolower($string);
	   //replace / and . with white space
	   	$string = preg_replace("/[\/\.]/", " ", $string);
	   	$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);

	   //remove multiple dashes or whitespaces
	   	$string = preg_replace("/[\s-]+/", " ", $string);
	   
	   //convert whitespaces and underscore to $replace
	  	 $string = preg_replace("/[\s_]/", $replace, $string);

	   //limit the slug size
	  	 $string = substr($string, 0, 100);
	   
	   //slug is generated
	  	 return $string;
	  }

    public function delete($id)
    {
        try {
            $id = base64_decode($id);
            Product::find($id)->delete();
            //ProductSupplier::where('product_id', $id)->delete();
            return redirect()->back()->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
}
