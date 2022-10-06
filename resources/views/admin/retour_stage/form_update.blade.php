@extends('layouts/master')

@push('style')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
        <li><a href="{{ route('retourDeStage.data') }}">Gestion des Retours de stage</a></li>
        <li class="active">MISE A JOUR</li>
    </ol>
    <small>En cliquant sur les boutons de sélection, commencer par écrire</small>
</div>

<div class="row clearfix">
    <div class="col-sm-offset-1 col-sm-10 col-xs-10">
        <div class="card">
            <div class="header">
                <h2>Mettre à jour une entrée</h2>
            </div>
            <div class="body">
                @if(session('success'))
                    <div class="alert bg-green alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Mise à jours avec succès.
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
                <form id="form_advanced_validation" method="POST" action="{{ route('retourDeStage.update',['id'=>$retour_stage->id]) }}">
                    {{csrf_field()}}

                    <div class="row">
                        <h2>Informations</h2>
                    </div>
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Agents</label><br>
                                    <select name="id_agent" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($agents as $agent)
                                            @if($agent->id == $retour_stage->id_agent)
                                                <option value="{{ $agent->id }}" style="background:#919c9e; color: #fff;" selected>{{ $agent->nom_prenoms }}</option>
                                            @else
                                                <option value="{{ $agent->id }}" style="background:#919c9e; color: #fff;">{{ $agent->wording }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>


                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{ $retour_stage->numero_decision_rs ?? ''}}" name="numero_decision_rs" required>
                                    <label class="form-label">Numéro de référence</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Date de signature de retour de stage</label><br>
                                    <input type="text" class="form-control" id="date_signature" value="{{ $retour_stage->date_signature ?? '' }}" name="date_signature" required>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Date de fin de  stage</label><br>
                                    <input type="text" class="form-control" value="{{ $retour_stage->date_fin_formation ?? '' }}" id="date_fin_formation" name="date_fin_formation" required>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Date de reprise de service</label><br>
                                    <input type="text" class="form-control" value="{{ $retour_stage->date_reprise_service ?? '' }}" id="date_reprise_service" name="date_reprise_service" required>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Catégorie RS</label><br>
                                    <select name="categorie_rs" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($categories as $c)
                                            @if($retour_stage->categorie_rs == $c->wording)
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
                                    <label>Année RS</label><br>
                                    <select name="annee_rs" class="form-control show-tick" data-live-search="true" id="annee_rs">
                                        <option value="{{ $retour_stage->annee_rs }}" style="background:#919c9e; color: #fff;" selected>{{ $retour_stage->annee_rs }}</option>
                                            
                                            <option value="2000" style="background:#919c9e; color: #fff;" >2000</option>        
                                            <option value="2001" style="background:#919c9e; color: #fff;" >2001</option>        
                                            <option value="2002" style="background:#919c9e; color: #fff;" >2002</option>        
                                            <option value="2003" style="background:#919c9e; color: #fff;" >2003</option>        
                                            <option value="2004" style="background:#919c9e; color: #fff;" >2004</option>        
                                            <option value="2005" style="background:#919c9e; color: #fff;" >2005</option>        
                                            <option value="2006" style="background:#919c9e; color: #fff;" >2006</option>
                                            <option value="2007" style="background:#919c9e; color: #fff;" >2007</option>        
                                            <option value="2008" style="background:#919c9e; color: #fff;" >2008</option>        
                                            <option value="2009" style="background:#919c9e; color: #fff;" >2009</option>        
                                            <option value="2010" style="background:#919c9e; color: #fff;" >2010</option>        
                                            <option value="2011" style="background:#919c9e; color: #fff;" >2011</option>        
                                            <option value="2012" style="background:#919c9e; color: #fff;" >2012</option>        
                                            <option value="2013" style="background:#919c9e; color: #fff;" >2013</option>
                                            <option value="2014" style="background:#919c9e; color: #fff;" >2014</option>
                                            <option value="2015" style="background:#919c9e; color: #fff;" >2015</option>
                                            <option value="2016" style="background:#919c9e; color: #fff;" >2016</option>
                                            <option value="2017" style="background:#919c9e; color: #fff;" >2017</option>
                                            <option value="2018" style="background:#919c9e; color: #fff;" >2018</option>
                                            <option value="2019" style="background:#919c9e; color: #fff;" >2019</option>
                                            <option value="2020" style="background:#919c9e; color: #fff;" >2020</option>
                                            <option value="2021" style="background:#919c9e; color: #fff;" >2021</option>
                                            <option value="2022" style="background:#919c9e; color: #fff;" >2022</option>
                                            <option value="2023" style="background:#919c9e; color: #fff;" >2023</option>
                                            <option value="2024" style="background:#919c9e; color: #fff;" >2024</option>
                                            <option value="2025" style="background:#919c9e; color: #fff;" >2025</option>
                                            <option value="2026" style="background:#919c9e; color: #fff;" >2026</option>
                                            <option value="2027" style="background:#919c9e; color: #fff;" >2027</option>
                                            <option value="2028" style="background:#919c9e; color: #fff;" >2028</option>
                                            <option value="2029" style="background:#919c9e; color: #fff;" >2029</option>
                                            <option value="2030" style="background:#919c9e; color: #fff;" >2030</option>
                                            <option value="2031" style="background:#919c9e; color: #fff;" >2031</option>
                                            <option value="2032" style="background:#919c9e; color: #fff;" >2032</option>
                                            <option value="2033" style="background:#919c9e; color: #fff;" >2033</option>
                                            <option value="2034" style="background:#919c9e; color: #fff;" >2034</option>
                                            <option value="2035" style="background:#919c9e; color: #fff;" >2035</option>
                                            <option value="2036" style="background:#919c9e; color: #fff;" >2036</option>
                                            <option value="2037" style="background:#919c9e; color: #fff;" >2037</option>
                                            <option value="2038" style="background:#919c9e; color: #fff;" >2038</option>
                                            <option value="2039" style="background:#919c9e; color: #fff;" >2039</option>
                                            <option value="2040" style="background:#919c9e; color: #fff;" >2040</option>
                                            <option value="2041" style="background:#919c9e; color: #fff;" >2041</option>
                                            <option value="2042" style="background:#919c9e; color: #fff;" >2042</option>
                                            <option value="2043" style="background:#919c9e; color: #fff;" >2043</option>
                                            <option value="2044" style="background:#919c9e; color: #fff;" >2044</option>
                                            <option value="2045" style="background:#919c9e; color: #fff;" >2045</option>
                                            <option value="2046" style="background:#919c9e; color: #fff;" >2046</option>
                                            <option value="2047" style="background:#919c9e; color: #fff;" >2047</option>
                                            <option value="2048" style="background:#919c9e; color: #fff;" >2048</option>
                                            <option value="2049" style="background:#919c9e; color: #fff;" >2049</option>
                                            <option value="2050" style="background:#919c9e; color: #fff;" >2050</option>

                                    
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{ $retour_stage->incidence_bn ?? '' }}" name="incidence_bn" required>
                                    <label class="form-label">Incidence BN</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Structure RS</label><br>
                                    <select name="structure_rs" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($structures as $strut)
                                            @if($strut->wording == $retour_stage->structure_rs)
                                                <option value="{{ $strut->wording }}" style="background:#919c9e; color: #fff;" selected="">{{ $strut->wording }}</option>
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
                    
 
                    <button class="btn btn-primary waves-effect" type="submit">VALIDER</button>
                    <a href="{{route('retourDeStage.data')}}" class="btn btn-warning waves-effect" >Liste des mises en stage</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection



@push('script')
 
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script type="text/javascript">
    $( function() {
        $( "#date_signature" ).datepicker({
            dateFormat: "mm/dd/yy",
            minDate: 0, 
            //maxDate: "+12M +10D"
        });

    } );

    $( function() {
        $( "#date_fin_formation" ).datepicker({
            dateFormat: "mm/dd/yy",
            minDate: 0, 
            //maxDate: "+12M +10D"
        });
      } );

    $( function() {
        $( "#date_reprise_service" ).datepicker({
            dateFormat: "mm/dd/yy",
            minDate: 0, 
            //maxDate: "+12M +10D"
        });
      } );
</script>



@endpush