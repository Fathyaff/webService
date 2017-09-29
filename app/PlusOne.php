<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlusOne extends Model
{
    //
    protected $table = 'plusOneTable';
    protected $primaryKey = 'id';
    protected $fillable = ['plusoneret'];
    public $timestamps = true;
}
