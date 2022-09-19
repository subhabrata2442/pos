<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchStockProducts extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'branch_stock_products';
	protected $guarded	= [];
	
	public function size()
    {
		return $this->hasOne(Size::class,'id', 'size_id'); 
    }

}
