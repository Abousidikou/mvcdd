@extends('layouts/master')





@section('content')

<div class="block-header">

    <h2>AGGREGATS</h2>

    <small>Vous pouvez filtrer les données en appliquant les filtres de votre choix</small>

</div>



<div class="row clearfix">

        <div class="col-sm-12 col-xs-10">

            <div class="card">

                <div class="header">

                    <h2>

                        DONNEES
                        &nbsp;&nbsp;&nbsp;&nbsp;| 
                        <form method="POST" action="{{ route('aggregat.export') }}">
                            {{csrf_field()}}
                            
                            
                            
                            <div class="row">
                            @if(session('success'))
                                <div class="alert bg-green alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    Excell généré
                                </div>
                            @elseif(session('error'))
                                <div class="alert bg-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    Selectionner un indicateur et une année , et<br>
                                    N'oubliez pas de générer les aggrégats avant de les exporters.
                                </div>
                            @endif
                                <div class="col-md-3">
                                        <label class="form-label"><small>Indicateurs</small> </label><br>
                                        <select name="indicators" class="form-control show-tick" data-live-search="true" data-max-options="2" required >
                                            <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                            @foreach ($indics as $indic)
                                                <option value="{{ $indic->id }}" style="background:#919c9e; color: #fff;">{{ $indic->wording }}</option>
                                            @endforeach
                                        </select>
                                </div>

                                <div class="col-md-3">
                                <label class="form-label"><small>Sexe</small> </label><br>
                                    <select name="sexe_id[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                        @foreach ($sexes as $sexe)
                                        	<option value="{{ $sexe->id }}" style="background:#919c9e; color: #fff;">{{ $sexe->wording }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                <label class="form-label"><small>Statuts</small> </label><br>
                                    <select name="statut_id[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                        @foreach ($statuts as $stat)
                                        	<option value="{{ $stat->id }}" style="background:#919c9e; color: #fff;">{{ $stat->wording }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-3">
                                <label class="form-label"><small>Catégories</small> </label><br>
                                    <select name="categorie_id[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                        @foreach ($categories as $cat)
                                        	<option value="{{ $cat->id }}" style="background:#919c9e; color: #fff;">{{ $cat->wording }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                

                                <div class="col-md-3">
                                    <label class="form-label"><small>Structure</small> </label><br>
                                    <select name="structure_id[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                        @foreach ($structures as $struct)
                                        	<option value="{{ $struct->id }}" style="background:#919c9e; color: #fff;">{{ $struct->wording }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                <label class="form-label"><small>Corps de la fonction Publique</small> </label><br>
                                    <select name="corps_id[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" style="background:#919c9e; color: #fff;">Pas de selection</option>
                                        @foreach ($corps as $cp)
                                        	<option value="{{ $cp->id }}" style="background:#919c9e; color: #fff;">{{ $cp->wording }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                
                            
                                <div class="col-md-3">
                                    <label class="form-label"><small> Année du stage</small> </label><br>
                                    <select name="annee[]" class="form-control show-tick" data-live-search="true" id="annee_stage" multiple required>
                                        @foreach ($ann as $an)
                                        <option value="{{ $an }}" style="background:#919c9e; color: #fff;" >{{ $an }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label"></label>
                                </div>

                                
                                <div class="col-md-3" style="margin-top: 25px;">
                                     <button type="submit"  name="generer" class="btn btn-success btn-md" value="0">Générer L'Excell</button>
                                </div>
                            </div>
                        </form>
                    </h2>

                </div>

                

            </div>
            @if (session('path'))
                <div class="card">
                    <div class="body">
                        <label class="form-label">Telecharger le fichier :   </label><small style="text-align:center;">
                            @php
                                $path = session('path');
                            @endphp
                            <a href="{{url($path)}}" class="btn btn-secondary waves-effect" >Télécharger le fichier</a>
                        </small>
                    </div>
                </div>
                                    
            @endif 
            

        </div>

    </div>
@endsection



