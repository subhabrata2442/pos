<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SupplierBank extends Model
{
    use HasFactory;
    //use SoftDeletes;
	
	protected $table = 'supplier_bank';
	protected $guarded	= [];

}
