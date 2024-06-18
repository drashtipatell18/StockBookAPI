<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleSiderRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sidebar_roles';
    protected $fillable = ['role_id', 'siderbar_id', 'permission'];
}
