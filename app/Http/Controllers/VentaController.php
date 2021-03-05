<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\VentaFormRequest;
use App\Venta;
use App\DetalleVenta;
use Auth;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    public function index(Request $request)
    {
        if ($request){
            $query=trim($request->get('searchText'));
            $ventas=DB::table('venta as v')
            ->join('persona as p','v.id_cliente','=','p.id_persona')
            ->join('detalle_venta as dv','v.id_venta','=','dv.id_venta')
            ->select('v.id_venta','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.impuesto','v.estado','v.total_venta')
            ->where('v.num_comprobante','LIKE','%'.$query.'%')
            ->orderBy('v.id_venta','desc')
            
            ->paginate(7);
            if(Auth::user()->permisos != 'admin' && Auth::user()->permisos != 'v' && Auth::user()->permisos != 'cv'){return redirect('/home');}
            return view('ventas.venta.index',["ventas"=>$ventas,"searchText"=>$query]);
        }
    }
    public function create()
    {
        $personas=DB::table('persona')->where('tipo_persona','=','Cliente')->get();
        $articulos=DB::table('articulo as art')
            ->join('detalle_ingreso as di','art.id_articulo','=','di.id_articulo')
            ->select(DB::raw('CONCAT(art.codigo, " ",art.nombre) AS articulo'),'art.id_articulo','art.stock',DB::raw('max(di.precio_venta) as precio_promedio'))
            ->where('art.estado','=','Activo')
            ->where('art.stock','>','0')
            ->groupBy('articulo','art.id_articulo','art.stock')
            ->get();
        return view("ventas.venta.create",["personas"=>$personas,"articulos"=>$articulos]);
    }

    public function store(VentaFormRequest $request)
    {
        try{
            DB::beginTransaction();
                $venta=new Venta;
                $venta->id_cliente=$request->get('id_cliente');
                $venta->tipo_comprobante=$request->get('tipo_comprobante');
                $venta->serie_comprobante=$request->get('serie_comprobante');
                $venta->num_comprobante=$request->get('num_comprobante');
                $venta->total_venta=$request->get('total_venta');
                //fecha horaria de la zona
                $mytime = Carbon::now('America/Mexico_City'); 
                $venta->fecha_hora=$mytime->toDateTimeString();
                $venta->impuesto='16';
                $venta->estado='A';
                $venta->save();

                $id_articulo=$request->get('id_articulo');
                $cantidad=$request->get('cantidad');
                $descuento=$request->get('descuento');
                $precio_venta=$request->get('precio_venta');

                $cont=0;

                while($cont < count($id_articulo)){
                    $detalle = new DetalleVenta();
                    $detalle->id_venta=$venta->id_venta;
                    $detalle->id_articulo=$id_articulo[$cont];
                    $detalle->cantidad=$cantidad[$cont];
                    $detalle->descuento=$descuento[$cont];
                    $detalle->precio_venta=$precio_venta[$cont];
                    $detalle->save();
                    $cont=$cont+1;
                }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
        }

        return Redirect::to('ventas/venta');
    }

    public function show($id)
    {
        $venta=DB::table('venta as v')
        ->join('persona as p','v.id_cliente','=','p.id_persona')
        ->join('detalle_venta as dv','v.id_venta','=','dv.id_venta')
        ->select('v.id_venta','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.impuesto','v.estado','v.total_venta')
        ->where('v.id_venta','=',$id)
        ->first();

    $detalles=DB::table('detalle_venta as d')
        ->join('articulo as a','d.id_articulo','=','a.id_articulo')
        ->select('a.nombre as articulo','d.cantidad','d.descuento','d.precio_venta')
        ->where('d.id_venta','=',$id)
        ->get();
    return view("ventas.venta.show",["venta"=>$venta,"detalles"=>$detalles]);
        
    }

    public function destroy($id)
    {
        $venta=Venta::findOrFail($id);
        $venta->Estado='C';
        $venta->Update();
        Return Redirect::to('ventas/venta');
    }
}
