<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'email', 'password', 'telefono', 'descripcion', 'foto'];

    protected $table = 'users';


    public static function searchByIdUser($idUser) {

        $user = User::find($idUser);

        if($user) {
            return $user;
        } else {
            return null;
        }
    }
}
