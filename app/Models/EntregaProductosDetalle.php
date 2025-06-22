<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaProductosDetalle extends Model
{
    use HasFactory;
    protected $table = 'entrega_productos_detalle';
    public $timestamps = false;
}
