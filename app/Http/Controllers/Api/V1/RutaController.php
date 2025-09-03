<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\FactorService;
use App\Http\Requests\FactorRequest;
use Illuminate\Support\Facades\Validator; 
use App\Service\LineamientoService; 

class FactorController extends Controller
{
    protected $factorService; 
    protected $lineamientoService; 
    
    public function __construct(
        FactorService $factorService ,
        LineamientoService $lineamientoService     )
    {
        $this->factorService = $factorService; 
        $this->lineamientoService = $lineamientoService; 
    }


    /**
    * Retorna la vista inicial.
    *
    * @return \Illuminate\Http\Response
    */
    public function obtenerVista(Request $request)
    { 
        $array_lineamiento = $this->lineamientoService->listarTodo('','true', 1, 200); 

        return \view('factor.index') 
        ->with('array_lineamiento', $array_lineamiento) ;
    }

    /**
    * Permite guardar y actualizar un recursos.
    *
    * @return \Illuminate\Http\Response
    */
   public function guardar(FactorRequest $request)
   {
        $output =  $this->factorService->guardar( (object)
            [  
                'id' => $request['id'] ,   
                'lineamiento_id' => $request['lineamiento_id'] ,   
                'codigo' => $request['codigo'] ,   
                'nombre' => $request['nombre'] ,   
                'descripcion' => $request['descripcion'] , 
                'usuariocreacion' =>1,
                'usuariomodificacion' =>1,
                'ipmodificacion' => $request->getClientIp(),
                'ipcreacion' => $request->getClientIp()
                  
            ]
        );

        return \response()->json([
            'msg' => $request['id'] > 0 ? ['Datos actualizados exitosamente'] :['Datos guardados exitosamente'],
            'obj' =>  $output
        ],202);
   }


    public function obtenerRecurso(Request $request, $id)
    {
        if (!$request->ajax())
        {
            return \response()->json([
                'state' => 400,
                'msg' => ['La petición no fue enviado mediante ajax'],
                'title' =>'Error de solicitud'
            ],400);
        }

        $array_mensajes = [
            'id.required' => 'El campo id es obligatorio',
            'id.integer' => 'El campo id debe ser entero',
            'id.min' => 'El campo id debe ser minimo 1'
        ];

        $valid = Validator::make(
            array('id' => $id),
            [
                'id' => 'required|integer|min:1'
            ],
            $array_mensajes);

        if ($valid->fails())
        {
            return \response()->json([
                'state' => 422,
                'msg' => $valid->errors()->all(),
                'title' => 'Campos invalidos'
            ],422);
        }

        $obj = $this->factorService->obtenerRecurso($request['id']);

        return \response()->json(
            $obj
        ,202);
    }


    public function listarTodo(Request $request)
    {
        if (!$request->ajax())
        {
            return \response()->json([
                'state' => 400,
                'msg' => ['La petición no fue enviado mediante ajax'],
                'title'=> 'Error de solicitud'
            ],400);
        }

        $array_mensajes = [
            'page.required' => 'El campo pagina es obligatorio',
            'numero_items.required' => 'El campo numero de registros es obligatorio',
            'numero_items.min' => 'El campo numero de registros debe ser minimo 1',
            'page.integer' => 'El campo page de ser tipo integer',
            'page.min' => 'El campo page de ser minimo 1',
            'numero_items.integer' => 'El campo numero de registros de ser tipo integer'
        ];

        $valid = Validator::make(
            $request->all(),
            [
                'page' => 'required|integer|min:1',
                'numero_items' => 'required|integer|min:1',
            ],
            $array_mensajes);

        if ($valid->fails())
        {
            return \response()->json([
                'state' => 422,
                'msg' => $valid->errors()->all(),
                'title' => 'Campos con valores incorrectos'
            ],422);
        }

        $page = $request['page'];
        $find = $request['find'];
        $numero_items = $request['numero_items'];
        $estado = isset($request['estado']) ? $request['estado'] : '';

        $array_data = $this->factorService->listarTodo($find, $estado, $page, $numero_items);

        return \response()->json($array_data,202);
    }

  
    public function eliminar(Request $request)
    {
        if (!$request->ajax())
        {
            return \response()->json([
                "state" => 400,
                "msg" => ["La petición no fue enviado mediante ajax"],
                "title" => "Error de solicitud"
            ],400);
        }

        $array_mensajes = [
            "array_ids.required" => "Los identificadores son obligatorios",
            "array_ids.array" => "Los identificadores debe ser enviado en array",
            "array_ids.*.integer" => "El identificador :input debe ser un entero"
        ];

        $valid = Validator::make(
            $request->all(),
            [
                "array_ids" => "required|array|min:1",
                "array_ids.*" => "integer"
            ],
            $array_mensajes);

        if ($valid->fails())
        {
            return \response()->json([
                "state" => 422,
                "msg" => $valid->errors()->all()
            ],422);
        }

        $out_put = $this->factorService->eliminar($request['array_ids']);

        return \response()->json(
            $out_put
        ,202);
    }

    

    public function cambiarEstado(Request $request, $id)
    {
        if (!$request->ajax())
        {
            return \response()->json([
                "state" => 400,
                "msg" => ["La petición no fue enviado mediante ajax"],
                "title" => "Error de solicitud"
            ],400);
        }

        $array_mensajes = [
            "id.required" => "El campo id es obligatorio",
            "estado.required" => "El campo estado es obligatorio",
            "id.integer" => "El campo id debe ser entero",
            "id.min" => "El campo id debe ser minimo 1"
        ];

        $valid = Validator::make(
            array("id" => $id),
            [
                "id" => "required|integer|min:1"
            ],
            $array_mensajes);

        if ($valid->fails())
        {
            return \response()->json([
                "state" => 422,
                "msg" => $valid->errors()->all(),
                "title" => "Campos con valores incorrectos"
            ],422);
        }

        $obj = $this->factorService->cambiarEstado($id, $request["estado"]);

        return \response()->json(
            $obj
        ,202);
    }


}
?>