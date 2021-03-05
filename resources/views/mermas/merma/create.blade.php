@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h3>Nueva Merma</h3>
            @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
            {!!Form::open(array('url'=>'mermas/merma','method'=>'POST','autocomplete'=>'off'))!!}
            {{Form::token()}}
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="form-group">
                <label for="cliente">Cliente</label>
                <select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-Live-search="true">
                    @foreach($personas as $persona)
                    <option value="{{$persona->id_persona}}">{{$persona->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Tipo Comprobante</label>
                <select name="tipo_comprobante" class="form-control" >
                    <option value="Ticket">Ticket</option>
                    <option value="Factura">Factura</option>
                    <option value="Nota">Nota</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label for="serie_comprobante">Serie Comprobante</label>
                <input type="text" name="serie_comprobante" value="{{old('serie_comprobante')}}" class="form-control" placeholder="Serie Comprobante...">
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label for="num_comprobante">No. Comprobante</label>
                <input type="text" name="num_comprobante" required value="{{old('num_comprobante')}}" class="form-control" placeholder="No. Comprobante...">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <div class="form-group">
                    <label>Artículo</label>
                    <select name="p_id_articulo" class="form-control selectpicker" id="p_id_articulo" data-Live-search="true">
                        @foreach($articulos as $articulo)
                            <option value="{{$articulo->id_articulo}}_{{$articulo->stock}}_{{$articulo->precio_promedio}}">{{$articulo->articulo}}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" name="p_cantidad" id="p_cantidad" class="form-control" placeholder="Cantidad...">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" disabled name="p_stock" id="p_stock" class="form-control" placeholder="Stock...">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                    <label for="precio_venta">Precio Venta</label>
                        <input type="number" name="p_precio_venta" id="p_precio_venta" class="form-control" placeholder="Precio Venta...">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                    <label for="descuento">Descuento</label>
                        <input type="number" name="p_descuento" id="p_descuento" class="form-control" placeholder="Descuento...">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                    <button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color:#A9D0F5">
                            <th>Opciones</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Precio Venta</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </thead>
                        <tfoot>
                            <th>TOTAL</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><h4 id="total">$ 0.00</h4><input type="hidden" name="total_venta" id="total_venta"></th>
                        </tfoot>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
            <div class="form-group">
                <input name="_token" value="{{csrf_token()}}" type="hidden"></input>
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        </div>
    </div>
    
            {!!Form::close()!!}
@push('scripts')
<script>
    $(document).ready(function(){
        $('#bt_add').click(function(){
            agregar();
        });
    });
    var cont=0;
    total=0;
    subtotal=[];
    $("#guardar").hide();
    $("#p_id_articulo").change(mostrarValores);

    function mostrarValores(){
        datosArticulo=document.getElementById('p_id_articulo').value.split('_');
        $("#p_precio_venta").val(datosArticulo[2]);
        $("#p_stock").val(datosArticulo[1]);
    }

    function agregar(){
        datosArticulo=document.getElementById('p_id_articulo').value.split('_');

        id_articulo=datosArticulo[0];
        articulo=$("#p_id_articulo option:selected").text();
        cantidad=$("#p_cantidad").val();
        descuento=$("#p_descuento").val();
        precio_venta=$("#p_precio_venta").val();
        stock=$("#p_stock").val();

        if(id_articulo!="" && cantidad!="" && cantidad>0 && descuento!="" && precio_venta!=""){
            if(stock>=cantidad){
                subtotal[cont]=(cantidad*precio_venta-descuento);
                total=total+subtotal[cont];

                var fila='<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+')">X</button></td><td><input type="hidden" name="id_articulo[]" value="'+id_articulo+'">'+articulo+'</td><td><input type="number" name="cantidad[]" value="'+cantidad+'"></td><td><input type="number" name="precio_venta[]" value="'+precio_venta+'"></td><td><input type="number" name="descuento[]" value="'+descuento+'"></td><td>'+subtotal[cont]+'</td></tr>';
                cont++;
                limpiar();
                $("#total").html("$ "+total);
                $("#total_venta").val(total);
                evaluar();
                $('#detalles').append(fila);
            }else{
                alert('La cantidad a vender supera el stock');
            }
        }else{
            alert("Error al ingresar el detalle de la venta, revise los datos del artículo");
        }
    }
    function limpiar(){
        $("#p_cantidad").val("");
        $("#p_descuento").val("");
        $("#p_precio_venta").val("");
    }

    function evaluar(){
        if(total>0){
            $("#guardar").show();
        }else{
            $("#guardar").hide();
        }
    }

    function eliminar(index){
        total=total-subtotal[index];
        $("#total").html("$ "+total);
        $("#total_venta").val(total);
        $("#fila"+index).remove();
        evaluar();
    }


</script>
@endpush

@endsection