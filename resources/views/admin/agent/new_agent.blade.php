@extends('layouts/master')



@section('content')

<div class="block-header">

    <ol class="breadcrumb breadcrumb-col-orange">

        <li><a href="{{route('home')}}">Accueil</a></li>

        <li><a href="{{route('agents')}}">Gestion des agents du MTFP</a></li>

        <li class="active">CREER AGENT</li>

    </ol>



    <small>Afin de valider la création, remplissez les champs puis cliquez sur le bouton <strong>Valider</strong> </small>

</div>



<div class="row clearfix">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="card">

            <div class="header">

                <h2>CREER AGENT</h2> 

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

                        Erreur de validation... Il se peut que l'agent existe déjà. <br>

                        Veuillez rééssayer.

                    </div>

                @endif

                <form id="form_advanced_validation" method="POST" action="{{route('create.agent')}}">

                    {{csrf_field()}}

                    <div class="form-group form-float">

                        <div class="form-line">

                            <input type="text" class="form-control" name="name" required>

                            <label class="form-label">Nom & Prénoms</label>

                        </div>

                        <div class="help-info"></div>

                    </div>

                    <div class="form-group form-float">

                        <div class="form-line">

                            <input type="email" class="form-control" name="email" required>

                            <label class="form-label">Email</label>

                        </div>

                        <div class="help-info"></div>

                    </div>




                    <div class="form-group form-float">

                        <div class="form-line">
                            <label class="form-label" >Role</label><br>
                            <select name="role" id="role" class="form-control show-tick" required>
                                    <option value="see_stats" selected>Utilisateur simple</option>
                                    <option value="admin" >Administrateur</option>
                                    <option value="agents_m">Gérant des Agents</option>
                                    <option value="agents_gen">Gérant des Agents | Agrégats</option>
                            </select>

                            

                        </div>

                    </div>

                    <div class="form-group form-float" id="passwd">

                        <div class="form-line">

                            <input type="password" class="form-control" name="passwd" required>

                            <label class="form-label">Mot de passe</label>

                        </div>

                        <div class="help-info"></div>

                    </div>

                    <div class="form-group form-float" id="cpasswd">

                        <div class="form-line">

                            <input type="password" class="form-control" name="cpasswd" required>

                            <label class="form-label">Confirmer le mot de passe</label>

                        </div>

                        <div class="help-info"></div>

                    </div>

                    <div class="form-group form-float">

                        <div class="form-line">

                            <select name="structure" class="form-control show-tick" data-live-search="true">

                                <option value="null">Veuillez choisir une structure</option>

                                <?= $structuresOptions ?>

                            </select>

                            <label class="form-label"></label>

                        </div>

                    </div>

                    

                    <button class="btn btn-primary waves-effect" type="submit">VALIDER</button>

                    <a class="btn btn-warning waves-effect" href="{{route('agents')}}">Liste des utilisateurs</a>

                </form>

            </div>

        </div>

    </div>

</div>



@endsection


