<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Compte!</title>
    <style>
        .content {
        max-width: 500px;
        margin: auto;
        }
    </style>
  </head>
  <body  style="background-color: #e3e8e5 ;">
  
    <div class="row content" style="text-align:center;">
        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header justify-content-between card-header-border-bottom">
                    <h2>Cadre de Vie </h2>
                </div>

                <div class="card-body">
                    <p class="mb-5">Charger le fichier </p>

                    <h1 class="mb-2 text-dark">Types 
                        <span class="badge badge-secondary ">xlsx-xlx</span>
                    </h1>

                    <form id="form_advanced_validation" method="POST"
                        action="{{ route('postcdv') }}" enctype="multipart/form-data">

                        {{ csrf_field() }}



                        <div class="form-group form-float">

                            <div class="form-line">

                                <input type="file" class="form-control" name="importfile" required>

                                {{-- <label class="form-label">Dataset Name</label> --}}

                            </div>

                            <div class="help-info"></div>

                        </div>
                        <button class="btn btn-primary waves-effect" type="submit">IMPORTER</button>
                        @if(session("path"))

                            @php
                                
                                $path = session('path');
                                
                            @endphp

                            <a href="{{ url($path) }}" class="btn btn-secondary waves-effect">Telecharger le fichier</a>

                        @endif
                    </form>

                </div>
            </div>
        </div>

    </div>

</body>
</html>