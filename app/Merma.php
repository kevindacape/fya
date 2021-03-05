<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    protected $table='merma';

    protected $primaryKey='id_merma';

    public $timestamps=false;

    protected $fillable =[
        'desccripcion',
        'fecha_hora',
        'usuario'
    ];

    protected $guarded =[

    ];
}
