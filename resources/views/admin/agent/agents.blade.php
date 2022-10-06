@extends('layouts/master')

@section('content')

<div class="block-header">

    <ol class="breadcrumb breadcrumb-col-orange">

        <li><a href="{{route('home')}}">Accueil</a></li>

        <li class="active">GESTION DES AGENTS DU MTFP</li>

    </ol>



    <small>Pour ajouter un agent, cliquez sur le bouton <strong>Ajouter un Agent</strong> </small>

</div>



<div class="row clearfix">

        <div class="col-sm-offset-1 col-sm-10 col-xs-10">

            <div class="card">

                <div class="header">

                    <h2>

                        AGENTS DU MTFP

                    </h2>

                </div>

                <div class="body table-responsive">

                    @if(session('success'))

                        <div class="alert bg-green alert-dismissible" role="alert">

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                            Supprimé avec succès.

                        </div>

                    @elseif(session('error'))

                        <div class="alert bg-danger alert-dismissible" role="alert">

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                            Une erreur s'est produite au cours de la suppression... Veuillez rééssayer.

                        </div>

                    @endif

                    <table class="table table-striped table-bordered table-condensed js-basic-example">

                        <thead>

                            <tr>

                                <th>Nom et Prénoms</th>

                                <th>Email</th>

                                <th>Structure</th>

                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($agents as $agent)

                            <tr>

                                    <td>{{$agent->name}}</td>

                                    <td>{{$agent->email}}</td>

                                    <td>{{$agent->struct}}</td>
                                    <td>

                                            <a href="{{route('prepareUpdate.agent', ['id' => $agent->id])}}" class="btn bg-green waves-effect"

                                                title="Cliquer pour modifier"

                                            >

                                                <i class="material-icons" >edit</i>

                                            </a>

                                            <a onclick="return confirm('Êtes-vous sûre?')" href="{{route('delete.agent', ['id' => $agent->id])}}" class="btn bg-red waves-effect"
                                                title="Cliquer pour supprimer"
                                            >
                                                <i class="material-icons">delete_forever</i>
                                            </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                        <caption style="caption-side: top; text-align:center" title="Cliquer pour ajouter un agent">
                            <a href="{{route('form.agent')}}" class="btn bg-green waves-effect">Ajouter un Agent</a>
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection