<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'employees';
    protected $fillable = ['user_id','firstname','lastname','dob','email', 'address','phoneno','gender','salary','profilepic','joiningdate','total_leave'];

    // App\Models\Employee.php
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
