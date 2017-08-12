<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anuncio extends Model
{
     use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'titulo', 'user_id', 'sector_profesional', 'localidad', 'provincia', 'precio_maximo',
    'descripcion'];

    protected $table = 'anuncios';

    public static function searchByIdAnuncio($idAnuncio) {

        $anuncio = Anuncio::find($idAnuncio);

        if($anuncio) {
            return $anuncio;
        } else {
            return null;
        }
    }
}
