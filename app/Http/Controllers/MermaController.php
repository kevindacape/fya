<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\MermaFormRequest;
use App\Merma;
use App\DetalleMerma;
use Auth;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class MermaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    public function index(Request $request)
    {
        if ($request){
            $query=trim($request->get('searchText'));
            $mermas=DB::table('merma')
            ->select('id_merma','descripcion','fecha_hora','usuario')
            ->where('fecha_hora','LIKE','%'.$query.'%')
            ->orderBy('id_merma','desc')
            
            ->paginate(7);
            if(Auth::user()->permisos != 'admin' && Auth::user()->permisos != 'v' && Auth::user()->permisos != 'cv'){return redirect('/home');}
            return view('mermas.merma.index',["mermas"=>$mermas,"searchText"=>$query]);
        }
    }
    public function create()
    {
        $articulos=DB::table('articulo as art')
            ->join('detalle_merma as dm','art.id_articulo','=','dm.id_articulo')
            ->select(DB::raw('CONCAT(art.codigo, " ",art.nombre) AS articulo'),'art.id_articulo','art.stock',DB::raw('max(di.precio_venta) as precio_promedio'))
            ->where('art.estado','=','Activo')
            ->where('art.stock','>','0')
            ->groupBy('articulo','art.id_articulo','art.stock')
            ->get();
        return view("mermas.merma.create",["articulos"=>$articulos]);
    }

    public function store(VentaFormRequest $request)
    {
        try{
            DB::beginTransaction();
                $merma=new Merma;
                $merma->descripcion=$request->get('descripcion');
                //fecha horaria de la zona
                $mytime = Carbon::now('America/Mexico_City'); 
                $merma->fecha_hora=$mytime->toDateTimeString();
                $marma->usuario=$request->get('usuario');
                $merma->save();

                $id_articulo=$request->get('id_articulo');
                $cantidad=$request->get('cantidad');
                $precio=$request->get('precio');

                $cont=0;

                while($cont < count($id_articulo)){
                    $detalle = new DetalleMerma();
                    $detalle->id_merma=$merma->id_merma;
                    $detalle->id_articulo=$id_articulo[$cont];
                    $detalle->cantidad=$cantidad[$cont];
                    $detalle->descripcion=$descripcion[$cont];
                    $detalle->precio=$precio[$cont];
                    $detalle->save();
                    $cont=$cont+1;
                }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
        }

        return Redirect::to('mermas/merma');
    }

    public function show($id)
    {
        $venta=DB::table('merma as m')
        ->join('detalle_merma as dm','m.id_merma','=','dm.id_merma')
        ->select('m.id_merma','m.descripcion','m.fecha_hora','usuario')
        ->where('m.id_merma','=',$id)
        ->first();

    $detalles=DB::table('detalle_merma as d')
        ->join('articulo as a','d.id_articulo','=','a.id_articulo')
        ->select('a.nombre as articulo','d.cantidad','d.descuento','d.precio')
        ->where('d.id_merma','=',$id)
        ->get();
    return view("mermas.merma.show",["merma"=>$merma,"detalles"=>$detalles]);
        
    }

    public function destroy($id)
    {
        $merma=Merma::findOrFail($id);
        $merma->Update();
        Return Redirect::to('mermas/merma');
    }
}
