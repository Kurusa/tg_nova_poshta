<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model {

    protected $table = 'good';
    protected $fillable = ['record_id', 'code', 'title', 'amount', 'unit', 'char', 'status'];
    const UPDATED_AT = null;

}