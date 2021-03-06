<?php
namespace App\Http\Controllers;

use App\User;
use App\Anuncio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    protected $opciones = [
        'cost' => 12,
    ];

public function prueba(Request $request) {
  return "hola";
}

    public function deleteUser($id_user, Request $request) {

        $user = User::searchByIdUser($id_user);

        if($user != null) {

            $anuncios = Anuncio::where('user_id', $id_user)->whereNull('deleted_at')->delete();

            if(User::deleteUserById($id_user)) {

                return response()->json(array(
                    'message' => 'Usuario eliminado correctamente',
                    'code' => 200
                ));
            }

        }

        return response()->json(array(
            'message' => 'Usuario no encontrado',
            'code' => 404
        ));

    }

    public function registro(Request $request) {

        $nombre = $request->nombre;
        $email = $request->email;
        $telefono = $request->telefono;
        $descripcion = $request->descripcion;

        $usuarios = User::searchUserByEmail($email);

        if(is_null($usuarios))
        {
            $user = new User();
            $user->name = $nombre;
            $user->telefono = $telefono;
            $user->email = $email;
            $user->descripcion = $descripcion;
            $user->foto = null;

            if($user->save())
            {

                $psswd = substr( md5(microtime()), 1, 4);
                $user->password = password_hash($psswd, PASSWORD_BCRYPT, $this->opciones);

                if($user->save()) {

                    \Mail::send('emails.hello', array('user' => $user, 'pass' => $psswd), function($message) use($user)
                    {
                        $message->from('noresponder@ofertapp', 'No responder');
                        $message->to($user->email, $user->name)->subject('Welcome to OfertAPP');
                    });
                }

                return response()->json(array(
                    'message' => 'Usuario creado correctamente',
                    'code' => 201
                ));
            }

        } else {

            return response()->json(array(
                    'message' => 'Usuario ya existente',
                    'code' => 400
                ));
        }
    }

    public function login(Request $request) {

        $email = $request->email;
        $pass = $request->password;

        if($email != '' && $pass != '') {

            $user = User::searchUserByEmail($email);

            if(is_null($user)) {

                return response()->json(array(
                    'message' => 'Usuario no existente',
                    'code' => 400
                ));

            } else if(!password_verify($pass, $user->password)) {

                return response()->json(array(
                    'message' => 'Contraseña incorrecta',
                    'code' => 400
                ));

            } else {

                $id = $user->id;
                $nombre = $user->name;
                $email = $user->email;
                $telefono = $user->telefono;
                $descripcion = $user->descripcion;
                $password = $user->password;

                return response()->json(array(
                    'user_id'       => $id,
                    'user_nombre'   => $nombre,
                    'user_email'    => $email,
                    'user_telefono' => $telefono,
                    'user_desc'     => $descripcion,
                    'user_pass'     => $password,
                    'message'       => 'Login ok',
                    'code'          => 200
                ));
            }

        } else {

            return response()->json(array(
                'message' => 'Login failed',
                'code' => 400
            ));
        }
    }

    public function createAnuncio(Request $request) {

        $titulo = $request->titulo;
        $user_id = intval($request->user_id);
        $sector_profesional = $request->sector_profesional;
        //$localidad = $request->localidad;
        $provincia = $request->provincia;
        $precio_max = intval($request->precio_maximo);
        $descripcion = $request->descripcion;

        //Obligados título, sector y provincia
        if($titulo != '' && $sector_profesional != '' && $provincia != '') {

            $anuncio = new Anuncio();
            $anuncio->titulo = $titulo;
            $anuncio->user_id = $user_id;
            $anuncio->sector_profesional = $sector_profesional;
            $anuncio->provincia = $provincia;
            //$anuncio->localidad = $localidad;
            $anuncio->descripcion = $descripcion;
            $anuncio->precio_maximo = $precio_max;

            if($anuncio->save()) {

                return response()->json(array(
                    'message' => 'Anuncio creado correctamente',
                    'code' => 201
                ));
            }
        }

        return response()->json(array(
            'message' => 'Bad request',
            'code' => 400
        ));

    }

    public function getAnunciosByUser($user_id, Request $request){

        $anuncios = Anuncio::where('user_id', $user_id)->whereNull('deleted_at')->get();

        if($anuncios && count($anuncios)) {

            return response()->json(array(
                'anuncios' => $anuncios,
                'message'       => 'Get anuncios ok',
                'code'          => 200
            ));

        } else {
            return response()->json(array(
                'anuncios' => $anuncios,
                'message'       => 'Lista anuncios vacía',
                'code'          => 200
            ));
        }
    }

    public function deleteAnuncioById($id_anuncio, Request $request) {

        $anuncio = Anuncio::searchByIdAnuncio($id_anuncio);

        if($anuncio) {

            if($anuncio->delete()) {

                return response()->json(array(
                    'message' => 'Anuncio eliminado correctamente',
                    'code' => 200
                ));
            }

        }

        return response()->json(array(
            'message' => 'Anuncio no encontrado',
            'code' => 404
        ));

    }

    public function updateAnuncioById($id_anuncio, Request $request) {

        $anuncio = Anuncio::find($id_anuncio);

        if($anuncio) {

            $titulo = $request->titulo;
            $sector_profesional = $request->sector_profesional;
            //$localidad = $request->localidad;
            $provincia = $request->provincia;
            $precio_max = intval($request->precio_maximo);
            $descripcion = $request->descripcion;

            //Obligados título, sector y provincia
            //También se validan en el front
            if($titulo != '' && $sector_profesional != '' && $provincia != '') {

                $anuncio->titulo = $titulo;
                $anuncio->sector_profesional = $sector_profesional;
                $anuncio->provincia = $provincia;
                //$anuncio->localidad = $localidad;
                $anuncio->descripcion = $descripcion;
                $anuncio->precio_maximo = $precio_max;

                if($anuncio->save()) {

                    return response()->json(array(
                        'message' => 'Anuncio actualizado correctamente',
                        'code' => 200
                    ));
                }
                else {
                    $msg = 'Ha ocurrido algún problema al actualizar';
                }
            }
            else {
                $msg = 'Campos obligatorios';
            }
        }
        else {
            $msg = 'Anuncio no encontrado';
        }

        return response()->json(array(
            'message' => $msg,
            'code' => 400
        ));
    }

    public function updateUser($id_user, Request $request) {

        $user = User::find(intval($id_user));

        if($user) {

            $nombre = $request->nombre;
            $email = $request->email;
            $telefono = $request->telefono;
            $descripcion = $request->descripcion;

            if($nombre != '' && $email != '' && $telefono != '') {

                $user->name = $nombre;
                $user->email = $email;
                $user->telefono = $telefono;
                $user->descripcion = $descripcion;

                if($request->password != '') {
                    $user->password = password_hash($request->password, PASSWORD_BCRYPT, $this->opciones);
                }


                if($user->save()) {

                    return response()->json(array(
                        'message' => 'Usuario actualizado correctamente',
                        'code' => 200,
                        'user_id'       => $user->id,
                        'user_nombre'   => $user->name,
                        'user_email'    => $user->email,
                        'user_telefono' => $user->telefono,
                        'user_desc'     => $user->descripcion,
                        'user_pass'     => $user->password
                    ));

                }
                else {
                    $msg = 'Ha ocurrido algún problema al actualizar';
                }
            }
            else {
                $msg = 'Campos obligatorios';
            }
        }
        else {
            $msg = 'Usuario no encontrado';
        }

        return response()->json(array(
            'message' => $msg,
            'code' => 400
        ));
    }

    public function getAnuncios($sector, $provincia, $precio, $fecha, Request $request) {

        $fechaToCompare = $fecha.' 00:00:00';

        $anuncios = \DB::table('anuncios')
                        ->join('users', 'anuncios.user_id', '=', 'users.id')
                        ->where('sector_profesional', '=', $sector)
                        ->where('provincia', '=', $provincia)
                        ->where('precio_maximo', '<=', $precio)
                        ->where('anuncios.created_at', '>=', $fechaToCompare)
                        ->whereNull('anuncios.deleted_at')
                        ->select('users.id as idUser', 'users.name as name', 'users.email as email', 'users.telefono as telefono',
                        'anuncios.id as idAnuncio', 'anuncios.titulo as titulo', 'anuncios.sector_profesional as sector_profesional',
                        'anuncios.provincia as provincia', 'anuncios.precio_maximo as precio_maximo', 'anuncios.descripcion as descripcion')
                        ->get();

        if($anuncios && count($anuncios)) {

            return response()->json(array(
                'anuncios' => $anuncios,
                'message'       => 'Get anuncios ok',
                'code'          => 200
            ));

        } else {
            return response()->json(array(
                'anuncios' => $anuncios,
                'message'       => 'Lista anuncios vacía',
                'code'          => 200
            ));
        }
    }
}
