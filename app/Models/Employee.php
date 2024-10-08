<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasFactory, SoftDeletes,HasApiTokens;

    protected $dates = ['deleted_at'];
    protected $table = 'employees';
    protected $fillable = ['role_id','firstname','lastname','dob','email','password', 'address','phoneno','gender','salary','profilepic','joiningdate','aadhar_number','total_leave'];

    // App\Models\Employee.php
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }


}
