<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nama', 'saldo', 'domisili'];
    protected $timestamp = true;
}
