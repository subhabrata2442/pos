<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BarProductSizePrice extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'bar_product_size_price';
	protected $guarded	= [];

}
