<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $table = 'requestTable';
    protected $primaryKey = 'id';
    protected $fillable = ['count', 'request'];
    public $timestamps = true;
}
