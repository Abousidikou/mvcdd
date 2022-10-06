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
        <li><a href="{{ route('miseEnStage.data') }}">Gestion des Mises en stage</a></li>
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
                        Erreur de validation...  <br>
                        Sélectionner l'agent
                    </div>
                @endif
                <form id="form_advanced_validation" method="POST" action="{{ route('miseEnStage.update',['id'=>$mise_stage->id]) }}">
                    {{csrf_field()}}

                    <div class="row">
                        <h2>Informations</h2>
                    </div>
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Agents</label><br>
                                    <select name="id_agent" id="agentselected" class="selectpicker form-control show-tick" data-live-search="true">
                                        @foreach($agents as $agent)
                                            @if($agent->id == $mise_stage->id_agent)
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
                                    <input type="text" class="form-control" name="numero_decision_ms" value="{{ $mise_stage->numero_decision_ms ?? '' }}" required>
                                    <label class="form-label">Numéro de référence</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Date de signature de mise en stage</label><br>
                                    <input type="text" class="form-control" id="date_signature" value="{{ $mise_stage->date_signature ?? '' }}"  name="date_signature" required>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    @if ($mise_stage->isBoursier == 1)
                                        <input class="form-check-input" type="checkbox" name="isBoursier" id="flexCheckChecked" checked>
                                    @else
                                        <input class="form-check-input" type="checkbox" name="isBoursier" id="flexCheckChecked">
                                    @endif
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Bouriser
                                    </label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="nature_bourse" value="{{ $mise_stage->nature_bourse ?? '' }}" id="nature" required>
                                    <label class="form-label">Nature de la bourse</label>
                                </div>
                                <div class="help-info"></div>
                            </div>


                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Date de démarrage du stage</label><br>
                                    <input type="text" class="form-control" id="date_demarrage_stage" name="date_demarrage_stage" value="{{ $mise_stage->date_demarrage_stage ?? '' }}"  required>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="niveau" value="{{ $mise_stage->niveau ?? '' }}" required>
                                    <label class="form-label">Niveau</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="filiere" value="{{ $mise_stage->filiere ?? '' }}" required>
                                    <label class="form-label">Filière</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="spec_option" value="{{ $mise_stage->spec_option ?? '' }}" required>
                                    <label class="form-label">Spécialité/Option</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="ecole_stage" value="{{ $mise_stage->ecole_stage ?? '' }}" required>
                                    <label class="form-label">Ecole de stage</label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="duree" value="{{ $mise_stage->duree ?? '' }}" required>
                                    <label class="form-label">Durée</label>
                                </div>
                                <div class="help-info"></div>
                            </div>


                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Année du stage</label><br>
                                    <select name="annee_stage" class="form-control show-tick" data-live-search="true" id="annee_stage">
                                        <option value="{{ $mise_stage->annee_stage }}" style="background:#919c9e; color: #fff;" selected>{{ $mise_stage->annee_stage }}</option>
                                            
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
                                    <label>Pays de stage</label><br>
                                    <select name="pays_stage" class="form-control show-tick" data-live-search="true" id="country-dropdown">
                                        @foreach($countries as $country)
                                           @if($country->id == $mise_stage->pays_stage_id)
                                                <option value="{{ $country->id }}" style="background:#919c9e; color: #fff;" selected>{{ $country->name }}</option>
                                            @else
                                                <option value="{{ $country->id }}" style="background:#919c9e; color: #fff;">{{ $country->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line" id="state-dropdown">
                                    <label>Region de stage</label><br>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            <div class="form-group form-float">
                                <div class="form-line" id="city-dropdown">
                                    <label>Ville de stage</label><br>
                                </div>
                                <div class="help-info"></div>
                            </div>

                            
                    </div>
                    
 
                    <button class="btn btn-primary waves-effect" type="submit">VALIDER</button>
                    <a href="{{route('miseEnStage.data')}}" class="btn btn-warning waves-effect" >Liste des mises en stage</a>
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
            //minDate: 0, 
            //maxDate: "+12M +10D"
        });
      } );

        $( function() {
        $( "#date_demarrage_stage" ).datepicker({
            dateFormat: "mm/dd/yy",
            //minDate: sYear, 
            //maxDate: eYear
        });
      } );

      


 
    function al(){
        var id_state = document.getElementById('states').value;
        console.log(id_state);
        var url = "{{route('get-cities-by-state', '')}}"+"/"+id_state;
            fetch(url)
              .then(function(res) {
                if (res.ok) {
                  return res.json();
                }
              })
              .then(function(value) {
                console.log(value);
                var myDiv = document.getElementById("city-dropdown");
                //Create and append select list
                if(!!document.getElementById("cities")){
                    document.getElementById("cities").remove();
                }
                var selectList = document.createElement("select");
                selectList.setAttribute("name", "ville_stage");
                selectList.setAttribute("id", "cities");
                selectList.setAttribute("class", "form-control");
                myDiv.appendChild(selectList);

                for (const [key, val] of Object.entries(value)) {
                    for (const [k2, v2] of Object.entries(val)) {
                        var id = "";
                        var name = "";
                        for (const [k3, v3] of Object.entries(v2)) {
                            if(k3 == 'id'){
                                id = v3;
                                continue;
                            }
                                name = v3;
                        }
                        var option = document.createElement("option");
                        option.setAttribute("value",id);
                        option.text = name;
                        selectList.appendChild(option);
                    }
                  
                }
              })
              .catch(function(err) {
                // Une erreur est survenue
              });
    }

    $(document).ready(function() {
        $('#country-dropdown').on('change', function() {
            console.log('in change');
                var id_country = document.getElementById('country-dropdown').value;
                console.log(id_country);
                var url = "{{route('get-states-by-country', '')}}"+"/"+id_country;
                fetch(url)
              .then(function(res) {
                if (res.ok) {
                  return res.json();
                }
              })
              .then(function(value) {
                console.log(value);
                var myDiv = document.getElementById("state-dropdown");
                //Create and append select list
                if(!!document.getElementById("states")){
                    document.getElementById("states").remove();
                }
                var selectList = document.createElement("select");
                selectList.setAttribute("name", "region_stage");
                selectList.setAttribute("id", "states");
                selectList.setAttribute("onchange", "al()");
                selectList.setAttribute("class", "form-control");
                myDiv.appendChild(selectList);

                for (const [key, val] of Object.entries(value)) {
                    for (const [k2, v2] of Object.entries(val)) {
                        var id = "";
                        var name = "";
                        for (const [k3, v3] of Object.entries(v2)) {
                            if(k3 == 'id'){
                                id = v3;
                                continue;
                            }
                                name = v3;
                        }
                        var option = document.createElement("option");
                        option.setAttribute("value",id);
                        option.text = name;
                        selectList.appendChild(option);
                    }
                  
                }

              })
              .catch(function(err) {
                // Une erreur est survenue
              });
        }); 
    });

    function validate() {
        if (document.getElementById('flexCheckChecked').checked) {
            document.getElementById('nature').disabled = false;
        } else {
            alert("Le box boursier est désactivé. Vous ne pouvez plus écrire dans 'Nature de la bourse'");
            document.getElementById('nature').value = "";
            document.getElementById('nature').disabled = true;
        }
    }


    

</script>



@endpush