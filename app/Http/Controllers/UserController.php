<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Direction;
use App\Models\Municipality;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
            $keys[2] => ['required', 'email', 'unique:users'],
            $keys[3] => ['required', 'string', 'unique:users'],
            $keys[4] => ['required', 'digits:8', 'unique:users'],
            $keys[5] => ['required', 'date'],
            $keys[6] => ['required', 'string']
        );

        // Mensajes personalizados para los errores
        $messages = array(
            'required' => 'El campo :attribute es requerido.',
            'date' => 'El campo :attribute es de tipo fecha.',
            'min' => 'El campo :attribute está fuera de rango.',
            'max' => 'El campo :attribute está fuera de rango.',
            'unique' => 'El campo :attribute especificado ya siendo utilizado.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'dni' => 'El campo :attribute debe tener 12 caractéres de longitud(Sin Incluir Guiones).',
            'digits' => 'El campo :attribute debe tener 8 digitos.',
            'password' => 'El campo :attribute debe contener: mayúsculas, minúsculas, números y símbolos. Debe ser mayor de 8 Caractéres.'
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

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'id' => $user->id
                ], 200);
            }
        }
    }

    public function login(Request $request)
    {

        if (Auth::attempt($request->only('email', 'password'))) {

            $user = User::where('email', $request['email'])->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'id' => $user->id,
                    'isAdmin' => $user->isAdmin,
                    'isClient' => $user->isClient,
                    'isSeller' => $user->isSeller,
                    'isBanned' => $user->isBanned,
                    'isEnabled' => $user->isEnabled
                ],
                200
            );
        } else {
            return response()->json(['message' => 'Credenciales Incorrectas.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Cierre de Sesión Completo'], 200);
    }

    public function user(Request $request)
    {
        return $request->user()->only('id', 'isAdmin', 'isClient', 'isSeller', 'isBanned', 'isEnabled');
    }

    public function getSellerDetails(Request $request)
    {
        $queryResult = User::join('directions', 'directions.userIdFK', '=', 'users.id')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->select(
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'users.email as userEmail',
                'users.isBanned as userIsBanned',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->find($request->only('id'));

        if (is_null($queryResult)) {
            return response()->json(['message' => 'No se ha encontrado información de usuario.'], 500);
        } else {
            return response()->json($queryResult, 200);
        }
    }

    public function setToSeller(Request $request)
    {
        $user = User::where('id', $request->only('id'))->first();

        if ($user) {

            $user->isBanned = 1;
            $user->save();

            return response()->json(['message' => 'Actualizado Correctamente'], 200);
        } else {
            return response()->json(['message' => 'No se encontró el Usuario'], 500);
        }
    }

    public function getUsersStatistics()
    {

        $usersStatistics = User::select(
            DB::raw('COUNT(*) AS total'),
            DB::raw('SUM(CASE WHEN isBanned = 1 THEN 1 ELSE 0 END) AS countBannedUsers'),
            DB::raw('SUM(CASE WHEN isBanned = 0 THEN 1 ELSE 0 END) AS countNonBannedUsers'),
            DB::raw('SUM(CASE WHEN isSeller = 1 THEN 1 ELSE 0 END) AS countSellerUsers'),
            DB::raw('SUM(CASE WHEN isClient = 1 AND isSeller = 0 THEN 1 ELSE 0 END) AS countClientUsers')
        )->first();


        return response()->json($usersStatistics, 200);
    }

    public function getAllUsers($registersPerPage = null, $searchTerm = null, $page = null)
    {
        $fields = [
            'id',
            DB::raw('CONCAT(users.firstName, " ", users.lastName) AS userFullName'),
            'users.dni as userDNI',
            DB::raw('IF(users.isSeller = 1, "Vendedor", "Cliente") as userType'),
            DB::raw('IF(users.isBanned = 1, "Sí", "No") as userIsBanned'),
            DB::raw('IF(users.isEnabled = 1, "Activo", "Desactivado") as userIsEnabled'),
        ];

        // return response()->json([$registersPerPage, $searchTerm, $page]);

        if (!$page) {
            $users = User::select(...$fields)
                ->where('isAdmin', '=', '0')
                ->where(function ($query) use ($searchTerm) {
                    $query->where(DB::raw('CONCAT(firstName, " ", lastName)'), 'LIKE', '%' . $searchTerm . '%');
                })
                ->paginate(intval($registersPerPage));
        } else {
            $users = User::select(...$fields)
                ->where('isAdmin', '=', '0')
                ->where(function ($query) use ($searchTerm) {
                    $query->where(DB::raw('CONCAT(firstName, " ", lastName)'), 'LIKE', '%' . $searchTerm . '%');
                })
                ->skip(($page - 1) * $registersPerPage)
                ->take($registersPerPage)
                ->get();
        }

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No se encontrarón usuarios.', 500]);
        } else {
            return response()->json($users, 200);
        }
    }


    public function setIsBanned(Request $request)
    {
        $user = User::where('id', $request->only('id'))->first();

        if ($user) {

            Product::where('userIdFK', $request->only('id'))
                ->update(['isAvailable' => intval($request->get('isBanned')) == 1 ? 0 : 1]);

            $user->isBanned = intval($request->get('isBanned'));
            $user->save();

            return response()->json(['message' => 'Actualizado Correctamente'], 200);
        } else {
            return response()->json(['message' => 'No se encontró el Usuario'], 500);
        }
    }


    public function getActiveUsers(Request $request)
    {
        $activeUsers30Days = user::select('firstName')
            ->where('isEnabled', 1)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();
    }

    public function getActiveUsers6Month(Request $request)
    {
        $active6Months = User::where('isEnabled', 1)
            ->whereDate('created_at', '>=', Carbon::now()->subMonths(6))
            ->select('firstName')
            ->get();

        return view('active-users', compact('active6Months'));
    }

    public function getActiveUsers1yeart(Request $request)
    {
        $activeUsers = User::where('isEnabled', 1)
            ->where('created_at', '>=', now()->subYear())
            ->get(['firstName']);
    }
}
