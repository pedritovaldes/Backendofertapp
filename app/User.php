<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

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

    public static function deleteUserById($idUser) {

        $user = User::find($idUser);

        if($user) {
          $user->delete();
          return true;
        } else {
          return false;
        }
    }

    public static function searchUserByEmail($email) {

        $user = User::where('email', $email)->whereNull('deleted_at')->get()->first();

        if($user) {
          return $user;
        } else {
          return null;
        }
    }
}
