@extends('layouts/master')



@section('content')
<div class="block-header">
    <ol class="breadcrumb breadcrumb-col-orange">
        <li><a href="{{route('home')}}">Accueil</a></li>
        <li class="active">MISE A JOUR AGENT</li>
    </ol>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>Mettre à jour un agent</h2>
            </div>
            <div class="body">
                @if(session('success'))
                    <div class="alert bg-green alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Mise à jour faite  avec succès.
                    </div>
                @elseif(session('error'))
                    <div class="alert bg-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Une erreur s'est produite au cours de la mise à jour... Veuillez rééssayer.
                    </div>
                @elseif(session('validation'))
                    <div class="alert bg-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Erreur de validation... Il se peut que le domaine existe déjà. <br>
                        Les champs ne doivent pas contenir des chiffres
                    </div>
                @endif
                <form id="form_advanced_validation" method="POST" action="{{ route('agentFormation.update', ['id' => $agent->id]) }}">
                    {{csrf_field()}}

                    <div class="row">
                        <h2>Informations personnelles</h2>
                    </div>
                    <div class="row">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="matricule" value="{{ $agent->matricule ?? '' }}"  required>
                                    <label class="form-label">Matricule</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="nom_prenoms" value="{{ $agent->nom_prenoms ?? '' }}" required>
                                    <label class="form-label">Noms et Prénoms</label>
                                </div>
                                <div class="help-info"></div>
                            </div>
                            
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="diplome_base" value="{{ $agent->diplome_base ?? '' }}" required>
                                    <label class="form-label">Diplôme de base</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    @if ($agent->avis_commission == 1)
                                        <input class="form-check-input" type="checkbox" name="avis_commission" id="flexCheckChecked"  checked>
                                    @else
                                    <input class="form-check-input" type="checkbox" name="avis_commission" id="flexCheckChecked" >
                                    @endif
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Avis de la commission 
                                    </label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Plan de formations</label><br>
                                    <select name="plan_formations[]" class="selectpicker form-control show-tick" data-live-search="true" multiple>
                                        @foreach($plan_formations as $plan)
                                            @if ($agentPlans->search($plan->id))
                                                <option value="{{ $plan->id }}" style="background:#919c9e; color: #fff;" selected>{{ $plan->annee_debut }}-{{ $plan->annee_fin }}</option>
                                            @else
                                                <option value="{{ $plan->id }}" style="background:#919c9e; color: #fff;">{{ $plan->annee_debut }}-{{ $plan->annee_fin }}</option>
                                            @endif
                                            
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Statut</label><br>
                                    <select name="status" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($status as $s)
                                            @if($agent->status == $s->wording)
                                                <option value="{{ $s->wording }}" style="background:#919c9e; color: #fff;" selected="">{{ $s->wording }}</option>
                                            @else
                                                <option value="{{ $s->wording }}" style="background:#919c9e; color: #fff;">{{ $s->wording }}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Catégorie</label><br>
                                    <select name="categorie" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($categories as $c)
                                            @if($agent->categorie == $c->wording)
                                                <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;" selected>{{ $c->wording }}</option>
                                            @else
                                                <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;">{{ $c->wording }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>


                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Sexe</label><br>
                                    <select name="sexe" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($sexes as $c)
                                            @if($agent->sexe == $c->wording)
                                                <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;" selected>{{ $c->wording }}</option>
                                            @else
                                                <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;">{{ $c->wording }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>



                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Corps de la fonction publique</label><br>
                                    <select name="corps" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($corps as $cor)
                                            @if($agent->corps == $cor->wording)
                                                <option value="{{ $cor->wording }}" style="background:#919c9e; color: #fff;" selected>{{ $cor->wording }}</option>
                                            @else
                                                <option value="{{ $cor->wording }}" style="background:#919c9e; color: #fff;">{{ $cor->wording }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>


                             <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Structure</label><br>
                                    <select name="structure" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($structures as $strut)
                                            @if($agent->structure == $strut->wording)
                                                <option value="{{ $strut->wording }}" style="background:#919c9e; color: #fff;" selected>{{ $strut->wording }}</option>
                                            @else
                                                <option value="{{ $strut->wording }}" style="background:#919c9e; color: #fff;">{{ $strut->wording }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>



                        </div>

                        
                    </div>
                    
 
                    <button class="btn btn-primary waves-effect" type="submit">VALIDER</button>
                    <a href="{{route('agentFormation.data')}}" class="btn btn-warning waves-effect" >Liste des agents en formation</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


