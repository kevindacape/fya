<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table='articulo';

    protected $primaryKey='id_articulo';

    public $timestamps=false;

    protected $fillable =[
        'id_categoria',
        'codigo',
        'codigo',
        'stock',
        'descripcion',
        'imagen',
        'estado'
    ];

    protected $guarded =[

    ];
}
