<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ImportProductHistory extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = ['date', 'status', 'products_imported', 'file', 'message'];

}
