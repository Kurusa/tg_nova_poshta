<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordStatusChange extends Model {

    protected $table = 'record_status_change';
    protected $fillable = ['user_id', 'record_id', 'status'];
    const UPDATED_AT = null;

}