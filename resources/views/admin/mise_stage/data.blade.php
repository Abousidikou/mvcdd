@extends('layouts/master')

@section('content')
<div class="block-header">
    <ol class="breadcrumb breadcrumb-col-orange">
        <li><a href="{{route('home')}}">Accueil</a></li>
        <li class="active">GESTION DES MISES DE STAGE</li>
    </ol>

    <small>Pour ajouter un nouvel event de retour en stage, cliquez sur le bouton <strong>Ajouter une entrée</strong> </small>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>
                    Mise en formation
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
                            <th>Nature de la Bourse</th>
                            <th>Durée</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($m_stages as $m_s)
                        <tr>
                            <td>{{$m_s->agent->matricule ?? ''}}</td>
                            <td>{{$m_s->agent->nom_prenoms ?? ''}}</td>
                            <td>{{$m_s->nature_bourse ?? ''}}</td>
                            <td>{{$m_s->duree ?? ''}}</td>
                            @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m")
                                <td>
                                    <div style="display: flex">
                                        <a href="{{route('miseEnStage.prepareUpdate', ['id' => $m_s->id])}}" class="btn bg-green waves-effect"
                                            title="Cliquer pour modifier les données de l'agent"
                                        >
                                            <i class="material-icons">edit</i>
                                        </a> 
                                        <a onclick="return confirm('Êtes-vous sûre?')" href="{{route('miseEnStage.delete', ['id' => $m_s->id])}}" class="btn bg-red waves-effect"
                                            title="Cliquer pour supprimer l'agent"
                                        >
                                            <i class="material-icons">delete_forever</i>
                                        </a>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{ $m_s->id }}">
                                        <i class="material-icons">info</i>
                                        </button>
                                    </div>
                                </td>
                            @else
                                    <td>No actions</td>
                            @endif
                        </tr>

                        <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{ $m_s->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog " role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Mise en stage</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <strong>Matricule : </strong>   <small>{{ $m_s->agent->matricule ?? ''}}</small>
                            <hr>
                            <strong>Nom & prenoms : </strong>    <small>{{$m_s->agent->nom_prenoms ?? ''}}</small></strong>
                            <hr>
                            <strong>Nature de la Bourse : </strong>    <small>{{$m_s->nature_bourse ?? ''}}</small>
                            <hr>
                            <strong>Numero de decision MS : </strong>    <small>{{$m_s->numero_decision_ms ?? ''}}</small>
                            <hr>
                            <strong>Date de la signature de mise en stage : </strong>    <small>{{$m_s->date_signature ?? ''}}</small>
                            <hr>
                            <strong>Date de démarrage du stage : </strong>    <small>{{$m_s->date_demarrage_stage ?? ''}}</small>
                            <hr>
                            <strong>Durée : </strong>    <small>{{$m_s->duree ?? ''}}</small>
                            <hr>
                            <strong>Ecole de stage : </strong>    <small>{{$m_s->ecole_stage ?? ''}}</small>
                            <hr>
                            <strong>Niveau : </strong>    <small>{{$m_s->niveau ?? ''}}</small>
                            <hr>
                            <strong>Filière : </strong>    <small>{{$m_s->filiere ?? ''}}</small>
                            <hr>
                            <strong>Spécialité/options : </strong>    <small>{{$m_s->spec_option ?? ''}}</small>
                            <hr>
                            <strong>Année de stage : </strong>    <small>{{$m_s->annee_stage ?? ''}}</small>
                            <hr>
                            <strong>Pays de stage : </strong>    <small>{{$m_s->pays_stage ?? ''}}</small>
                            <hr>
                            <strong>Region de stage : </strong>    <small>{{$m_s->region_stage ?? ''}}</small>
                            <hr>
                            <strong>Ville de stage : </strong>    <small>{{$m_s->ville_stage ?? ''}}</small>
                            <hr>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                        @endforeach
                    </tbody>




                    


                    @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m" || Auth::user()->role == "agents_gen")
                        <caption style="caption-side: top; text-align:center" title="Cliquer pour ajouter un nouvel agent">
                            <a href="{{route('miseEnStage.form_insert')}}" class="btn bg-green waves-effect">Ajouter une entrée</a>
                            <a href="{{ route('miseEnStage.export') }}" class="btn bg-green waves-effect">Exporter</a>
                            <a href="{{route('miseEnStage.form_import')}}" class="btn bg-green waves-effect">Importer un fichier</a>
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