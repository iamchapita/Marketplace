<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{

    protected function validateData($request)
    {
        // Extrayendo las llaves del arreglo de campos a validar
        $keys = array_keys($request);

        // Estableciendo los nombres personalizados de los atributos
        $customAttributes = array(
            $keys[0] => 'Nombres',
            $keys[1] => 'Apellidos',
            $keys[2] => 'Correo Electrónico',
            $keys[3] => 'DNI',
            $keys[4] => 'Teléfono',
            $keys[5] => 'Fecha de Nacimiento',
            $keys[6] => 'Contraseña'
        );

        // Estableciendo reglas de cada campo respectivamente
        $rules = array(
            $keys[0] => ['required', 'string', 'max:80'],
            $keys[1] => ['required', 'string', 'max:80'],
            $keys[2] => ['required', 'email', 'unique:users', 'max:50'],
            $keys[3] => ['required', 'string', 'unique:users','regex:([0-9-]{15})'],
            $keys[4] => ['required', 'digits:8', 'unique:users'],
            $keys[5] => ['required', 'date'],
            $keys[6] => ['required', 'string', 'regex:((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,35})']
        );

        // Mensajes personalizados para los errores
        $messages = array(
            'required' => 'El campo :attribute es requerido.',
            'date' => 'El campo :attribute es de tipo fecha.',
            'min' => 'El campo :attribute está fuera de rango.',
            'max' => 'El campo :attribute está fuera de rango.',
            'unique'=> 'El campo :attribute especificado ya siendo utilizado.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'dni.regex' => 'El campo :attribute debe tener 12 caractéres de longitud(Sin Incluir Guiones).',
            'digits' => 'El campo :attribute debe tener 8 digitos.',
            'password.regex' => 'El campo :attribute debe contener: mayúsculas, minúsculas, números y símbolos. Debe ser mayor de 8 Caractéres.'
        );

        // Validando los datos
        // $fields -> Campos del formulario.
        // $rules -> Reglas para validar campos.
        // $messages -> Mensajes personalizados para mostrar en caso de error.
        $validator = Validator::make($request, $rules, $messages);

        // Estableciendo los nombres de los atributos
        $validator->setAttributeNames($customAttributes);

        return $validator;
    }

    public function register(Request $request)
    {

        // Validate request data
        $validator = $this->validateData($request->all());

        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        // Check if validation pass then create user and auth token. Return the auth token
        if ($validator->passes()) {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = User::create([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'dni' => $request->dni,
                    'email' => $request->email,
                    'phoneNumber' => $request->phoneNumber,
                    'birthDate' => $request->birthDate,
                    'password' => Hash::make($request->password)
                ]);
                // $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'user' => $user
                ], 200);
            }
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $authuser = auth()->user();
            return response()->json(['message' => 'Login successful'], 200);
        } else {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged Out'], 200);
    }
}
