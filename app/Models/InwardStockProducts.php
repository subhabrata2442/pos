<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InwardStockProducts extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'inward_stock_products';
	protected $guarded	= [];

}
