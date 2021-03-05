<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table='detalle_venta';

    protected $primaryKey='id_detalle_venta';

    public $timestamps=false;

    protected $fillable =[
        'id_venta',
        'id_articulo',
        'cantidad',
        'precio_venta',
        'descuento'
    ];

    protected $guarded =[

    ];
}
