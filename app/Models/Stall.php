<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SalesOrder;

class Stall extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'stalls';
    protected $fillable = ['name','location','owner_name'];

    // public function salesOrders()
    // {
    //     return $this->hasMany(SalesOrder::class);
    // }
}
