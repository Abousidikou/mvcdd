@extends('layouts/master')

@section('content')
<div class="block-header">
    <ol class="breadcrumb breadcrumb-col-orange">
        <li><a href="{{route('home')}}">Accueil</a></li>
        <li class="active">GESTION DES RETOURS EN STAGE</li>
    </ol>

    <small>Pour ajouter un nouvel event de  retour de stage, cliquez sur le bouton <strong>Ajouter une entrée</strong> </small>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>
                    Retour de formation
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
                        Impossible de supprimer... 
                    </div>
                @endif
                <table class="table table-striped table-bordered table-condensed js-basic-example">
                    <thead> 
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & prenoms</th>
                            <th>Catégorie RS</th>
                            <th>Année RS</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($r_stages as $r_s)
                        <tr>
                            <td>{{$r_s->agent->matricule ?? ''}}</td>
                            <td>{{$r_s->agent->nom_prenoms ?? ''}}</td>
                            <td>{{$r_s->categorie_rs ?? ''}}</td>
                            <td>{{$r_s->annee_rs ?? ''}}</td>
                            @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m")
                                <td>
                                    <div style="display: flex">
                                        <a href="{{route('retourDeStage.prepareUpdate', ['id' => $r_s->id])}}" class="btn bg-green waves-effect"
                                            title="Cliquer pour modifier les données de l'agent"
                                        >
                                            <i class="material-icons">edit</i>
                                        </a> 
                                        <a onclick="return confirm('Êtes-vous sûre?')" href="{{route('retourDeStage.delete', ['id' => $r_s->id])}}" class="btn bg-red waves-effect"
                                            title="Cliquer pour supprimer l'agent"
                                        >
                                            <i class="material-icons">delete_forever</i>
                                        </a>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{ $r_s->id }}">
                                        <i class="material-icons">info</i>
                                        </button>
                                    </div>
                                </td>
                            @else
                                    <td>No actions</td>
                            @endif
                        </tr>

                        <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{ $r_s->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog " role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Mise en stage</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <strong>Matricule : </strong>   <small>{{ $r_s->agent->matricule ?? ''}}</small>
                            <hr>
                            <strong>Nom & prenoms : </strong>    <small>{{$r_s->agent->nom_prenoms ?? ''}}</small></strong>
                            <hr>
                            <strong>Numéro de référence : </strong>    <small>{{$r_s->numero_decision_rs ?? ''}}</small>
                            <hr>
                            <strong>Date de signature de retour de stage : </strong>    <small>{{$r_s->date_signature ?? ''}}</small>
                            <hr>
                            <strong>Date de fin de formation : </strong>    <small>{{$r_s->date_fin_formation ?? ''}}</small>
                            <hr>
                            <strong>Date de reprise de service : </strong>    <small>{{$r_s->date_reprise_service ?? ''}}</small>
                            <hr>
                            <strong>Catégorie RS : </strong>    <small>{{$r_s->categorie_rs ?? ''}}</small>
                            <hr>
                            <strong>Annéee RS : </strong>    <small>{{$r_s->annee_rs ?? ''}}</small>
                            <hr>
                            <strong>Incidence BN: </strong>    <small>{{$r_s->incidence_bn ?? ''}}</small>
                            <hr>
                            <strong>Structure RS : </strong>    <small>{{$r_s->structure_rs ?? ''}}</small>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                        @endforeach
                    </tbody>




                    


                    @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m" || Auth::user()->role == "agents_gen" )
                        <caption style="caption-side: top; text-align:center" title="Cliquer pour ajouter un nouvel agent">
                            <a href="{{route('retourDeStage.form_insert')}}" class="btn bg-green waves-effect">Ajouter une entrée</a>
                            <a href="{{ route('retourDeStage.export') }}" class="btn bg-green waves-effect">Exporter</a>
                            <a href="{{route('retourDeStage.form_import')}}" class="btn bg-green waves-effect">Importer un fichier</a>
                            @if (session('path'))
                                @php
                                    $path = session('path');
                                @endphp
                                <a href="{{url($path)}}" class="btn btn-secondary waves-effect" >Télécharger le fichier</a>
                            @endif

                        </caption>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@endsection