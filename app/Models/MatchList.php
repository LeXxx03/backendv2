<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMatch extends Model
{
    protected $fillable = ['user_id', 'match_id', 'status'];
}