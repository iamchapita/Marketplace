<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

                    // Obteniendo el arreglo del nombre y el contenido del archivo
                    $fileReponse = array(
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
}
