@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h3>Editar Cliente: {{ $persona->nombre}}</h3>
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
            {!!Form::model($persona,['method'=>'PATCH','route'=>['cliente.update',$persona->id_persona]])!!}
            {{Form::token()}}
            <div class="row">
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" required value="{{$persona->nombre}}" class="form-control" placeholder="Nombre...">
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" required value="{{$persona->direccion}}" class="form-control" placeholder="Dirección...">
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label>Documento</label>
                <select name="tipo_documento" class="form-control" >
                @if ($persona->tipo_documento=='INE')
                    <option value="INE" selected>INE</option>
                    <option value="RFC">RFC</option>
                    <option value="PAS">PAS</option>
                @elseif ($persona->tipo_documento=='RFC')
                    <option value="INE">INE</option>
                    <option value="RFC" selected>RFC</option>
                    <option value="PAS">PAS</option>
                @else
                    <option value="INE">INE</option>
                    <option value="RFC">RFC</option>
                    <option value="PAS" selected>PAS</option>
                @endif
                </select>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label for="num_documento">No. Documento</label>
                <input type="text" name="num_documento" value="{{$persona->num_documento}}" class="form-control" placeholder="No. Documento...">
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="text" name="telefono" value="{{$persona->telefono}}" class="form-control" placeholder="Telefono...">
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" value="{{$persona->email}}" class="form-control" placeholder="Email...">
            </div>
        </div>

        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        </div>
    </div>

            {!!Form::close()!!}
        </div>
    </div>
@endsection