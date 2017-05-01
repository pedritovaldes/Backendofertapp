<?php
namespace App\Http\Controllers;

use App\User;
use App\Anuncio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function deleteUser($id_user, Request $request) {

        $user = User::find($id_user);

        if($user) {

            if($user->delete()) {

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

        $usuarios = User::where('email', $email)->get()->first();

        if(is_null($usuarios))
        {
            $user = new User();
            $user->name = $nombre;
            $user->telefono = $telefono;
            $user->email = $email;
            $user->descripcion = $descripcion;
            //$user->password = '1234';
            $user->foto = null;

            if($user->save())
            {

                $psswd = substr( md5(microtime()), 1, 4);
                $user->password = $psswd;

                if($user->save()) {

                    \Mail::send('emails.hello', array('user' => $user), function($message) use($user)
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

            $user = User::where('email', $email)->where('password', $pass)->get()->first();

            if(is_null($user)) {

                return response()->json(array(
                    'message' => 'Usuario no existente',
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

    public function getAnuncios($user_id, Request $request){

        $anuncios = Anuncio::where('user_id', $user_id)->get();

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
