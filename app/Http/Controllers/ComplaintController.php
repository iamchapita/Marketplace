<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{

    protected function base64Encode($path, $imagesToObtain)
    {

        if (!Storage::disk('public')->exists($path)) {
            return false;
        } else {
            $encodedFiles = [];
            $files = Storage::disk('public')->files($path);

            if ($imagesToObtain != 0) {
                $count = intval($imagesToObtain);
            } else {
                $count = count($files);
            }

            foreach ($files as $key => $file) {

                if ($key < $count) {
                    // Se obtiene el contenido del archivo
                    $content = Storage::disk('public')->get($file);

                    // Se obtiene el nombre del archivo
                    $name = explode('/', $file);
                    $name = $name[count($name) - 1];
                    $type = explode('.', $name);
                    $type = $type[count($type) - 1];

                    // Obteniendo el arreglo del nombre y el contenido del archivo
                    $fileReponse = array(
                        'type' => $type,
                        'name' => $name,
                        'base64Image' => base64_encode($content)
                    );

                    array_push($encodedFiles, $fileReponse);
                }
            }
            return $encodedFiles;
        }
    }

    protected function base64Decode($request, $mode = 'store', $path = '')
    {

        if ($mode == 'store') {
            $path = 'complaints/' . Str::random(15);

            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
        } else {
            Storage::disk('public')->deleteDirectory($path);
            Storage::disk('public')->makeDirectory($path);
        }

        // Obteniendo el array de archivos enviados
        $images = $request->only('evidences')['evidences'];

        if ($images) {

            for ($i = 0; $i <  count(array_keys($images)); $i++) {

                // Obteniendo el nombre del archivo y la imagen en base64
                $imageName = $images[$i]['name'];
                $base64_image = $images[$i]['base64Image'];
                // Se almacena el archivo en la ruta
                Storage::disk('public')->put($path . '/' . $imageName, base64_decode($base64_image));
            }
            return $path;
        }

        return false;
    }

    public function create(Request $request)
    {

        if (!$request->has('created_at')) {
            $request->merge(['created_at' => now()]);
        }

        $path = $this->base64Decode($request, 'store');
        $request->merge(['evidences' => $path]);

        if ($request['evidences'] == false) {
            return response()->json(['error' => 'Error en las imagenes'], 500);
        } else {
            $values = $request->all();
            Complaint::insert($values);
            return response()->json(['message' => 'Insercion Completa'], 200);
        }
    }

    public function getAComplaint(Request $request)
    {
        $whereConditions = [
            ['userIdFK', '=', $request->get('userIdFK')]
        ];

        if ($request->has('userIdReported')) {
            array_push($whereConditions, ['products.isAvailable', '=', $request->get('isAvailableStatus')]);
        }

        if ($request->has('productIdFK')) {
            array_push($whereConditions, ['productIdFK', '=', $request->get('productIdFK')]);
        }

        $complaint = Complaint::where($whereConditions)->get();

        if ($complaint->isEmpty()) {
            return response()->json(['message' => 'No se encontró Denuncia.'], 500);
        } else {
            return response()->json($complaint, 200);
        }
    }

    public function getComplaintsStatistics()
    {
        $complaintsStatistics = Complaint::select(
            DB::raw('COUNT(*) AS total'),
            DB::raw('COUNT(wasApproved) AS approvedComplaints'),
            DB::raw('COUNT(*) - COUNT(wasApproved) AS pendingComplaints')
        )->first();


        return response()->json($complaintsStatistics, 200);
    }

    public function getAllComplaints($registersPerPage = null, $page = null)
    {
        $complaintFields = [
            'complaints.id as id',
            DB::raw('CONCAT(complaintOwner.firstName, " ", complaintOwner.lastName) as complaintOwnerName'),
            DB::raw('CONCAT(reportedUser.firstName, " ", reportedUser.lastName) as reportedUserName'),
            'products.name',
            DB::raw('IF(isAwaitingResponse = 1, "Pendiente", "Revisada") as isAwaitingResponse'),
            DB::raw('CASE WHEN wasApproved IS NULL THEN "N/D" ELSE IF(wasApproved = 1, "Aceptada", "Denegada") END as wasApproved'),
            DB::raw('IF(complaints.updated_at IS NULL, "N/D", complaints.updated_at) as updatedAt'),
            'complaints.created_at as createdAt',
        ];

        if (!$page) {
            $complaints = Complaint::join('users as reportedUser', 'reportedUser.id', '=', 'complaints.userIdReported')
                ->join('users as complaintOwner', 'complaintOwner.id', '=', 'complaints.userIdFK')
                ->join('products', 'products.id', '=', 'complaints.productIdFK')
                ->select(...$complaintFields)
                ->paginate(intval($registersPerPage));
        } else {
            $complaints = Complaint::join('users as reportedUser', 'reportedUser.id', '=', 'complaints.userIdReported')
                ->join('users as complaintOwner', 'complaintOwner.id', '=', 'complaints.userIdFK')
                ->join('products', 'products.id', '=', 'complaints.productIdFK')
                ->select(...$complaintFields)
                ->skip(($page - 1) * $registersPerPage)
                ->take($registersPerPage)
                ->get();
        }

        if ($complaints->isEmpty()) {
            return response()->json(['message' => 'No se encontraron productos.'], 500);
        } else {
            return response()->json($complaints, 200);
        }
    }

    public function getComplaintEvidences(Request $request)
    {
        $imagesToObtain = $request->has('imagesToObtain') ? $request->get('imagesToObtain') : 0;
        $path = $request->get('path');

        $result = $this->base64Encode($path, $imagesToObtain);

        if (count($result) > 0) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'No se encontraron imágenes.'], 500);
        }
    }

    public function getComplaintById(Request $request)
    {

        $complaint = Complaint::join('users as complaintOwner', 'complaintOwner.id', '=', 'complaints.userIdFK')
            ->join('users as reportedUser', 'reportedUser.id', '=', 'complaints.userIdReported')
            ->join('products', 'products.id', '=', 'complaints.productIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions as complaintOwnerDirection', 'complaintOwnerDirection.userIdFK', '=', 'complaints.userIdFK')
            ->join('directions as reportedUserDirection', 'reportedUserDirection.userIdFK', '=', 'complaints.userIdReported')
            ->join('departments as complaintOwnerDepartment', 'complaintOwnerDepartment.id', '=', 'complaintOwnerDirection.departmentIdFK')
            ->join('departments as reportedUserDepartment', 'reportedUserDepartment.id', '=', 'reportedUserDirection.departmentIdFK')
            ->join('municipalities as complaintOwnerMunicipality', 'complaintOwnerMunicipality.id', '=', 'complaintOwnerDirection.municipalityIdFK')
            ->join('municipalities as reportedUserMunicipality', 'reportedUserMunicipality.id', '=', 'reportedUserDirection.municipalityIdFK')
            ->select(
                'complaintOwner.id AS complaintOwnerId',
                DB::raw('CONCAT(complaintOwner.firstName, " ", complaintOwner.lastName) as complaintOwnerName'),
                'complaintOwner.email as complaintOwnerEmail',
                'complaintOwner.isBanned as complaintOwnerIsBanned',
                'complaintOwner.raiting as complaintOwnerRating',
                'complaintOwnerDepartment.name as complaintOwnerDeparmentName',
                'complaintOwnerMunicipality.name as complaintOwnerMunicipalityName',
                'reportedUser.id AS reportedUserId',
                DB::raw('CONCAT(reportedUser.firstName, " ", reportedUser.lastName) as reportedUserName'),
                'reportedUser.email as reportedUserEmail',
                'reportedUser.isBanned as reportedUserIsBanned',
                'reportedUser.raiting as reportedUserRating',
                'reportedUserDepartment.name as reportedUserDeparmentName',
                'reportedUserMunicipality.name as reportedUserMunicipalityName',
                'products.id as productId',
                'products.name as productName',
                'products.description as productDescription',
                'products.price as productPrice',
                'products.photos as productPhotos',
                'products.status as productStatus',
                'products.isBanned as productIsBanned',
                'products.isAvailable as productIsAvailable',
                'products.wasSold as productWasSold',
                'products.amount as productAmount',
                'products.created_at as productCreatedAt',
                'categories.name as productCategoryName',
                'complaints.description as complaintDescription',
                'complaints.evidences as complaintEvidences',
                'complaints.isAwaitingResponse as complaintIsAwaitingResponse',
                DB::raw('CASE WHEN wasApproved IS NULL THEN "N/D" ELSE IF(wasApproved = 1, "Aceptada", "Denegada") END as complaintWasApproved'),
                'complaints.created_at as complaintCreatedAt',
                'complaints.updated_at as complaintUpdatedAt'
            )
            ->find($request->get('id'));

        if (is_null($complaint)) {
            return response()->json(['message' => 'Denuncia no encontrada.'], 500);
        } else {
            return response()->json($complaint, 200);
        }
    }

    public function setWasApproved(Request $request)
    {
        $complaint = Complaint::find($request->get('id'));

        if (is_null($complaint)) {
            return response()->json(['message' => 'Denuncia no encontrada.'], 500);
        } else {
            $complaint->wasApproved = $request->get('wasApproved');
            $complaint->isAwaitingResponse = false;
            $complaint->save();
            return response()->json($complaint, 200);
        }
    }
}
