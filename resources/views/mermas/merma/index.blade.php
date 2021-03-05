@extends ('layouts.admin')
@section ('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de mermas <a href="merma/create"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('mermas.merma.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-dordered table-condensed table-hover">
                    <thead>
                        <th>Id merma</th>
                        <th>Descripcion</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </thead>
                    @foreach($mermas as $mer)
                    <tr>
                        <td>{{$mer->id_merma}}</td>
                        <td>{{$mer->descripcion}}</td>
                        <td>{{$mer->fecha_hora}}</td>
                        <td>{{$mer->usuario}}</td>
                        <td>
                            <a href="{{URL::action('MermaController@show',$mer->id_merma)}}"><button class="btn btn-primary">Detalles</button></a>
                            <a href="" data-target="#modal-delete-{{$mer->id_merma}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
                        </td>
                    </tr>
                    @include('mermas.merma.modal')
                    @endforeach
                </table>
            </div>
            {{$mermas->render()}}
        </div>
    </div>
@endsection