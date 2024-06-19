<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'leaves';
    protected $fillable = ['employee_id','user_id','reason','startdate','enddate','leave_type','totalhours', 'time_from', 'time_to','status','total_leave'];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
