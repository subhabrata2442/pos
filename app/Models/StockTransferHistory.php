<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StockTransferHistory extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'stock_transfer_history';
	protected $guarded	= [];
	
	public function size(){
		return $this->hasOne(Size::class,'id', 'size_id');
	}
	
	public function product(){
    	return $this->hasOne(Product::class,'id','product_id');
  }

}
