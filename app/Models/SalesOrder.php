<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Book;
use App\Models\Stall;

class SalesOrder extends Model
{
    use HasFactory,SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'salesorders';
    protected $fillable = ['book_id','stall_id','location','sales_price','quantity','total_price'];

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
