<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaProductos extends Model
{
    use HasFactory;
    protected $table = 'entrega_productos';
    public $timestamps = false;
}
