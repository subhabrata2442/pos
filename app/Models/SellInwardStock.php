<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellInwardStock extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'sell_inward_stock';
	protected $guarded	= [];

    public function supplierDetails(){
        return $this->hasOne(Supplier::class,'id','supplier_id');
    }

}
