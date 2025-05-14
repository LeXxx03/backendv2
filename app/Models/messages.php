<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class messages extends Model
{
    //
    protected $primaryKey ='messageid';
    protected $fillable = ['user1id','user2id','status'];
};