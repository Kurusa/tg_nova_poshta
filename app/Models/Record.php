<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

    protected $table = 'record';
    protected $fillable = ['user_id', 'delivery_type', 'phone', 'fio', 'payed_status', 'payed_sum', 'comment', 'delivery_date', 'city', 'sum', 'post', 'delivery_address', 'manager', 'record_status'];
    const UPDATED_AT = null;

}