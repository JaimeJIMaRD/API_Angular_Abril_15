<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Peticione;
use App\Models\User;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PeticioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('auth:api',['except'=>['index','show','list']]);
    }
    public function index(Request $request)
    {
        $peticiones = Peticione::all()->load(['user','categoria','files']);;
        return $peticiones;
    }

    public function listMine(Request $request)
    {

        try {
            $user = Auth::user();
            $peticiones = Peticione::where('user_id', $user->id)->get()->load(['user','categoria','files']);
            return $peticiones;
        }catch (\Exception $exception){
            return back()->withErrors( $exception->getMessage())->withInput();
        }

    }

    public function show(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id)->load(['user','categoria','files']);;;
        return $peticion;
    }

    public function update(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        if($request->user()->cannot('update',$peticion)){
            return response()->json(['message' => 'No estas autorizado para actualizar esta peticion'],403);
        }
        $res = $peticion->update($request->all());
        if($res){
            return response()->json(['message' => 'Petición actualizada satisfactoriamente','peticion'=>$peticion],201);
        }
        return response()->json(['message' => 'Error actualizando la peticion'],500);

        //return $peticion;
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'titulo' => 'required|max:255',
                'descripcion' => 'required',
                'destinatario' => 'required',
                'categoria_id' => 'required',
                //'file' => 'required',
            ]);


        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],401);
        }
        $validator = Validator::make($request->all(),
            [
                'file' => 'required|mimes:png,jpg|max:4096',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        if ($file = $request-> file('file')){
            $name = $file->getClientOriginalName();
            Storage::put($name, file_get_contents($request->file('file')->getRealPath()));
            $file->move('storage/',$name);
            $input['file']=$name;
        }

        //hola
        $category = Categoria::findOrFail($request->input('categoria_id'));
        $user = Auth::user();
        $user = User::findOrFail($user->id);

        $peticion = new Peticione();
        $peticion->titulo = $request->input('titulo');
        $peticion->descripcion = $request->input('descripcion');
        $peticion->destinatario = $request->input('destinatario');
        //$peticion->image = $input['file'];

        $peticion->user()->associate($user);
        $peticion->categoria()->associate($category);

        $peticion->firmantes = 0;
        $peticion->estado = 'pendiente';
        $res = $peticion->save();

        $imgdb = new File();
        $imgdb-> name = $input['file'];
        $imgdb->file_path='storage/'.$input['file'];
        $imgdb->peticione_id=$peticion->id;
        $imgdb->save();



        if($res){
            return response()->json(['message'=>'Peticion creada satisfactoriamente','peticion'=>$peticion],201);
        }
        return response()->json(['message'=>'Error creando la peticion'],500);
    }

    public function fileUpload(Request $req, $peticion_id=null){
    }

    public function firmar(Request $request, $id)
    {
        try{
            $peticion = Peticione::findOrFail($id);
            $user = Auth::user();
            $firmas = $peticion->firmas;
            foreach($firmas as $firma){
                if($firma->id==$user->id){
                    return response()->json(['message'=>'Ya has firmado esta peticion'],403);
                }
            }
            $user_id = [$user->id];
            $peticion->firmas()->attach($user_id);
            $peticion->firmantes = $peticion->firmantes + 1;
            $peticion->save();
        } catch (\Throwable$th) {
            return response()->json(['message'=>'La peticion no se ha podido firmar'],500);
        }
        if($peticion->firmas()){
            return response()->json(['message'=>'Peticion firmada satisfactoriamente','peticion'=>$peticion],201);
        }
        return response()->json(['message'=>'La peticion no se ha podido firmar'],500);
    }

    public function cambiarEstado(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        if($request->user()->cannot('cambiarEstado',$peticion)){
            return response()->json(['message'=>'No estas autorizado para realizar esta accion'],403);
        }
        $peticion->estado = 'aceptada';
        $res = $peticion->save();
        if($res){
            return response()->json(['message'=>'Peticion actualizada satisfactoriamente','peticion'=>$peticion],201);
        }
        return response()->json(['message'=>'Error actualizando la peticion'],500);
    }

    public function delete(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $res=$peticion->delete();
        if($res){
            return response()->json(['message'=>'Peticion eliminada satisfactoriamente'],201);
        }
        return response()->json(['message'=>'Error eliminando la petición'],500);
    }

}
