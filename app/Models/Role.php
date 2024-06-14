<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'roles';
    protected $fillable = ['role_name'];

    // public function permissions()
    // {
    //     return $this->hasMany(Permission::class, 'role_id');
    // }
}
