<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SideBar extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sidebars';
    protected $fillable = ['name', 'route', 'display_name'];
}
