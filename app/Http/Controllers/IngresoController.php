<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\IngresoFormRequest;
use App\Ingreso;
use App\DetalleIngreso;
use Auth;
use DB;

//para la fecha de la zonaHoraria
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class IngresoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    public function index(Request $request)
    {
        if ($request){
            $query=trim($request->get('searchText'));
            $ingresos=DB::table('ingreso as i')
            ->join('persona as p','i.id_proveedor','=','p.id_persona')
            ->join('detalle_ingreso as di','i.id_ingreso','=','di.id_ingreso')
            ->select('i.id_ingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado',DB::raw('sum(di.cantidad*precio_compra) as total'))
            ->where('i.num_comprobante','LIKE','%'.$query.'%')
            ->orderBy('i.id_ingreso','desc')
            ->groupBy('i.id_ingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado')
            ->paginate(7);
            if(Auth::user()->permisos != 'admin' && Auth::user()->permisos != 'c' && Auth::user()->permisos != 'cv'){return redirect('/home');}
            return view('compras.ingreso.index',["ingresos"=>$ingresos,"searchText"=>$query]);
        }
    }
    public function create()
    {
        $personas=DB::table('persona')->where('tipo_persona','=','Proveedor')->get();
        $articulos=DB::table('articulo as art')
            ->select(DB::raw('CONCAT(art.codigo, " ",art.nombre) AS articulo'),'art.id_articulo')
            ->where('art.estado','=','Activo')
            ->get();
        return view("compras.ingreso.create",["personas"=>$personas,"articulos"=>$articulos]);
    }

    public function store(IngresoFormRequest $request)
    {
        try{
            DB::beginTransaction();
                $ingreso=new Ingreso;
                $ingreso->id_proveedor=$request->get('id_proveedor');
                $ingreso->tipo_comprobante=$request->get('tipo_comprobante');
                $ingreso->serie_comprobante=$request->get('serie_comprobante');
                $ingreso->num_comprobante=$request->get('num_comprobante');
                //fecha horaria de la zona
                $mytime = Carbon::now('America/Mexico_City'); 
                $ingreso->fecha_hora=$mytime->toDateTimeString();
                $ingreso->impuesto='16';
                $ingreso->estado='A';
                $ingreso->save();

                $id_articulo=$request->get('id_articulo');
                $cantidad=$request->get('cantidad');
                $precio_compra=$request->get('precio_compra');
                $precio_venta=$request->get('precio_venta');

                $cont=0;

                while($cont < count($id_articulo)){
                    $detalle = new DetalleIngreso();
                    $detalle->id_ingreso=$ingreso->id_ingreso;
                    $detalle->id_articulo=$id_articulo[$cont];
                    $detalle->cantidad=$cantidad[$cont];
                    $detalle->precio_compra=$precio_compra[$cont];
                    $detalle->precio_venta=$precio_venta[$cont];
                    $detalle->save();
                    $cont=$cont+1;
                }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
        }

        return Redirect::to('compras/ingreso');
    }

    public function show($id)
    {
        $ingreso=DB::table('ingreso as i')
        ->join('persona as p','i.id_proveedor','=','p.id_persona')
        ->join('detalle_ingreso as di','i.id_ingreso','=','di.id_ingreso')
        ->select('i.id_ingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado',DB::raw('round(sum(di.cantidad*precio_compra),2) as total'))
        ->where('i.id_ingreso','=',$id)
        ->groupBy('i.id_ingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado')
        ->first();

    $detalles=DB::table('detalle_ingreso as d')
        ->join('articulo as a','d.id_articulo','=','a.id_articulo')
        ->select('a.nombre as articulo','d.cantidad','d.precio_compra','d.precio_venta')
        ->where('d.id_ingreso','=',$id)
        ->get();
    return view("compras.ingreso.show",["ingreso"=>$ingreso,"detalles"=>$detalles]);
        
    }

    public function destroy($id)
    {
        $ingreso=Ingreso::findOrFail($id);
        $ingreso->Estado='C';
        $ingreso->Update();
        Return Redirect::to('compras/ingreso');
    }
}
