@extends('layouts/master')

@section('content')
<div class="block-header">
    <ol class="breadcrumb breadcrumb-col-orange">
        <li><a href="{{route('home')}}">Accueil</a></li>
        <li class="active">GESTION DES AGENTS</li>
    </ol>

    <small>Pour ajouter un nouveau domaine, cliquez sur le bouton <strong>Ajouter un domaine</strong> </small>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>
                    Les Agents en formation
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
                @elseif(session('aggregat_insert_error'))
                    <div class="alert bg-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Une erreur s'est produite lors du lancement de la génération d'aggrégat.
                    </div>
                @elseif(session('aggregat_insert_success'))
                    <div class="alert bg-green alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        La génération des aggrégats a commencée. <br> Cette opération peut prendre plusieurs minutes.
                    </div>
                @endif
                <table class="table table-striped table-bordered table-condensed js-basic-example">
                    <thead> 
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & prenoms</th>
                            <th>Status</th>
                            <th>Categorie</th>
                            <th>Sexe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agents as $agent)
                        <tr>
                            <td>{{$agent->matricule ?? ''}}</td>
                            <td>{{$agent->nom_prenoms ?? ''}}</td>
                            <td>{{$agent->status ?? ''}}</td>
                            <td>{{$agent->categorie ?? ''}}</td>
                            <td>{{$agent->sexe ?? ''}}</td>
                            @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m")
                                <td>
                                    <div style="display: flex">
                                        <a href="{{route('agentFormation.prepareUpdate', ['id' => $agent->id])}}" class="btn bg-green waves-effect"
                                            title="Cliquer pour modifier les données de l'agent"
                                        >
                                            <i class="material-icons">edit</i>
                                        </a> 
                                        <a onclick="return confirm('Êtes-vous sûre?')" href="{{route('agentFormation.delete', ['id' => $agent->id])}}" class="btn bg-red waves-effect"
                                            title="Cliquer pour supprimer l'agent"
                                        >
                                            <i class="material-icons">delete_forever</i>
                                        </a>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{ $agent->id }}">
                                        <i class="material-icons">info</i>
                                        </button>
                                    </div>
                                </td>
                            @else
                                    <td>No actions</td>
                            @endif
                        </tr>

                        <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{ $agent->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog " role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agent Détails</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <strong>Matricule : </strong>   <small>{{$agent->matricule ?? ''}}</small>
                            <hr>
                            <strong>Nom & prenoms : </strong>    <small>{{$agent->nom_prenoms ?? ''}}</small></strong>
                            <hr>
                            <strong>Status : </strong>    <small>{{$agent->status ?? ''}}</small>
                            <hr>
                            <strong>Categorie : </strong>    <small>{{$agent->categorie ?? ''}}</small>
                            <hr>
                            <strong>Sexe : </strong>    <small>{{$agent->sexe ?? ''}}</small>
                            <hr>
                            <strong>Corps : </strong>    <small>{{$agent->corps ?? ''}}</small>
                            <hr>
                            <strong>Structure : </strong>    <small>{{$agent->structure ?? ''}}</small>
                            <hr>
                            <strong>Avis de la commission : </strong>    <small>{{$agent->avis_commission ?? ''}}</small>
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




                    


                    @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_m" || Auth::user()->role == "agents_gen" )
                        <caption style="caption-side: top; text-align:center" title="Cliquer pour ajouter un nouvel agent">
                            @if(Auth::user()->role == "admin" || Auth::user()->role == "agents_gen")
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-sm">Générer les agrégats</button>    
                                <!-- Small modal -->
                    

                                <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Générer les Aggrégats</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                        <small>Selectionner les indicateurs et les années pour lesquels générer l'aagrégats <br> Selectionner [*] pour une génération globale.</small>
                                    </div>
                                    
                                    <form id="aggregat" method="POST" action="{{ route('aggregat.genererAggregat') }}">
                                    {{csrf_field()}}
                                    <div class="modal-body">
                                            <div class="form-group">
                                                <label for="indicator">Selectionner l'indicateur</label><br>
                                                <select name="indicator[]" class="form-control show-tick" data-live-search="true" id="indicateur" multiple required>
                                                    <option value="-1" style="background:#919c9e; color: #fff;" selected>[*]</option>  
                                                    <option value="226" style="background:#919c9e; color: #fff;" >Effectifs des agents civils de l’Etat ayant bénéficié de décision de mise en stage</option>        
                                                    <option value="252" style="background:#919c9e; color: #fff;" >Effectifs des agents civils de l’Etat ayant bénéficié de décision de retour de stage</option>        
                                                    <option value="225" style="background:#919c9e; color: #fff;" >Effectifs des agents civils de l’Etat ayant bénéficié de bourses</option>        
                                                    <option value="251" style="background:#919c9e; color: #fff;" >Effectifs des agents civils de l’Etat n'ayant pas bénéficié de bourses</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="annee">Selectionner l'année</label><br>
                                                <select name="annee[]" class="form-control show-tick" data-live-search="true" id="annee" multiple  required>
                                                    <option value="-1" style="background:#919c9e; color: #fff;" selected>[*]</option>
                                                    @foreach ($ann as $agg)
                                                        <option value="{{ $agg }}" style="background:#919c9e; color: #fff;" >{{ $agg }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Générer</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                    </form>
                                    </div>
                                </div>
                                </div>
                            @endif
                            <a href="{{route('agentFormation.form')}}" class="btn bg-green waves-effect">Ajouter des agents</a>
                            <a href="{{route('agentFormation.formimport')}}" class="btn bg-green waves-effect">Importer un fichier</a>
                            <a href="{{ route('agentFormation.exporter') }}" class="btn bg-green waves-effect">Exporter</a>
                            <a href="{{ route('agentFormation.oneLine') }}" class="btn bg-green waves-effect">Exporter toute la base</a>
                            @if (session('path'))
                                @php
                                    $path = session('path');
                                @endphp
                                <a href="{{url($path)}}" class="btn btn-secondary waves-effect" >Télécharger les agents</a>
                            @endif
                            @if (session('data'))
                                @php
                                    $path = session('data');
                                @endphp
                                <a href="{{url($path)}}" class="btn btn-secondary waves-effect" >Télécharger la base</a>
                            @endif

                        </caption>
                    @endif
                </table>

                

                    

                    
            </div>
        </div>
    </div>
</div>

@endsection

