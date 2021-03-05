<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleMerma extends Model
{
    protected $table='detalle_merma';

    protected $primaryKey='id_detalle_merma';

    public $timestamps=false;

    protected $fillable =[
        'id_merma',
        'id_articulo',
        'cantidad',
        'precio'
    ];

    protected $guarded =[

    ];
}
