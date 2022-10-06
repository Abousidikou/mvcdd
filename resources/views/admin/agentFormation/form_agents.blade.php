@extends('layouts/master')

@push('style')
<!-- Select2 Css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style type="text/css">
        div.dropdown-menu.open
        {
            max-height: 10px !important;
            overflow: hidden;
        }
        ul.dropdown-menu.inner
        {
            max-height: 10px !important;
            overflow-y: auto;
        }
        .dropdown-menu span.text{
            white-space:normal;
        }
    </style>
@endpush

@section('content')
<div class="block-header">
    <ol class="breadcrumb breadcrumb-col-orange">
        <li><a href="{{route('home')}}">Accueil</a></li>
        <li class="active">GESTION DES AGENTS</li>
    </ol>
    <small>En cliquant sur les boutons de sélection, commencer par écrire</small>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>Ajouter un agent</h2>
            </div>
            <div class="body">
                @if(session('success'))
                    <div class="alert bg-green alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Nouvel agent ajouté avec succès.
                    </div>
                @elseif(session('error'))
                    <div class="alert bg-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Une erreur s'est produite au cours de l'ajout... Veuillez rééssayer.
                    </div>
                @elseif(session('validation'))
                    <div class="alert bg-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Erreur de validation... Il se peut que le domaine existe déjà. <br>
                        Les champs ne doivent pas contenir des chiffres
                    </div>
                @endif
                <form id="form_advanced_validation" method="POST" action="{{ route('agentFormation.insert') }}">
                    {{csrf_field()}}

                    <div class="row">
                        <h2>Informations personnelles</h2>
                    </div>
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="matricule" required>
                                    <label class="form-label">Matricule</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="nom_prenoms" required>
                                    <label class="form-label">Noms et Prénoms</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="diplome_base" required>
                                    <label class="form-label">Diplôme de base</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input class="form-check-input" type="checkbox" name="avis_commission" id="flexCheckChecked"  checked>
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
                                            <option value="{{ $plan->id }}" style="background:#919c9e; color: #fff;">{{ $plan->annee_debut }}-{{ $plan->annee_fin }}</option>
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
                                            <option value="{{ $s->wording }}" style="background:#919c9e; color: #fff;">{{ $s->wording }}</option>
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
                                            <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;">{{ $c->wording }}</option>
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
                                            <option value="{{ $c->wording }}" style="background:#919c9e; color: #fff;">{{ $c->wording }}</option>
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
                                            <option value="{{ $cor->wording }}" style="background:#919c9e; color: #fff;">{{ $cor->wording }}</option>
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
                                            <option value="{{ $strut->wording }}" style="background:#919c9e; color: #fff;">{{ $strut->wording }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
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



@push('script')
    <!-- Select2 Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-single').select2({

        });
    });
</script>

@endpush