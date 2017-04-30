<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'titulo', 'user_id', 'sector_profesional', 'localidad', 'provincia', 'precio_maximo',
    'descripcion'];

    protected $table = 'anuncios';
}
