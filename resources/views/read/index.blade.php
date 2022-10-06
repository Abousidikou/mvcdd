@extends('layouts/master')



@section('content')

<div class="block-header">

    <h2>VISUALISATION DES DONNEES du sous domaine {{$subdomain->wording}} </h2>

    <small>Vous pouvez filtrer les données en appliquant les filtres de votre choix</small>

</div>



<div class="row clearfix">

        <div class="col-sm-12 col-xs-10">

            <div class="card">

                <div class="header">

                    <h2>

                        DONNEES
                        &nbsp;&nbsp;&nbsp;&nbsp;| {{$indicator->wording}}
                        <form method="GET">
                            <div class="row">
                                @foreach ($typeAndLevelArray as $key => $typeArr)
                                <div class="col-md-3">
                                    <select name="level-{{$loop->index}}[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" selected>Filtrer par {{$key}}</option>
                                        @foreach ($typeArr as $level)
                                            <option value="{{$level}}" {{$level == old('level-'.$loop->parent->index) ? 'selected': ''}}>{{$level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endforeach

                                <div class="col-md-3">
                                    <select name="periodes[]" class="form-control show-tick" data-live-search="true" multiple>
                                        <option value="null" selected>Filtrer par Périodes</option>
                                        @foreach ($allyears as $year)
                                            <option value="{{$year}}">{{$year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <button type="submit"  name="filter" class="btn btn-success btn-md" value="0"> Filtrer </button>
                                    @if($isPlus)
                                        <button type="submit"  name="plus" class="btn btn-info btn-md" value="{{ $last_id +1 }}"> Voir Plus </button>
                                    @endif
                                    @if($last_id > 0)
                                            <button type="submit"  name="plus" class="btn btn-info btn-md" value="{{ $last_id }}">Arrière</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </h2>

                </div>
                
                <div class="body table table-responsive" style="overflow-x: scroll">

                    <table class="table table-responsive table-striped table-bordered table-condensed table-hover js-basic-example">

                        <thead>

                            <tr>
                                @foreach ($types as $type)
                                    <th>{{$type}}</th>
                                @endforeach

                                <th>Units</th>

                                @if (count($periodes) == 0)
                                    @foreach ($allyears as $year)
                                        <th>{{$year}}</th>
                                    @endforeach
                                @else
                                    @foreach ($periodes as $periode)
                                        <th>{{$periode}}</th>
                                    @endforeach
                                @endif
                            </tr>

                        </thead>

                        <tbody>
                            @foreach ($infoArray as $info)
                                <tr> 
                                    @foreach ($info->levels as $level)
                                        <td>{{$level}}</td>
                                    @endforeach
                                    <td>Nombre</td>
                                    @if (count($periodes) == 0)
                                        @foreach ($allyears as $year)
                                            @if (array_key_exists($year, $info->years))
                                                <td>{{$info->years[$year]}}</td>
                                                @else
                                                <td>--</td>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($periodes as $periode)
                                            @if (array_key_exists($periode, $info->years))
                                                <td>{{$info->years[$periode]}}</td>
                                                @else
                                                <td>--</td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                        <caption style="caption-side: top; text-align:center">

                            <a href="{{route('graph.data', ['indicator' => $id_indicator, 'subdomain' => $id_subdomain])}}" class="btn bg-warning waves-effect" title="Cliquer pour voir le graphe">Voir graphe</a>

                            <a href="{{route('export.data', ['indicator' => $id_indicator, 'subdomain' => $id_subdomain])}}" class="btn bg-green waves-effect">Exporter en Excel</a>


                            

                            @if (session('path'))

                                @php

                                    $path = session('path');

                                @endphp

                                <a href="{{url($path)}}" class="btn btn-secondary waves-effect" >{{url($path)}}</a>

                            @endif

                        </caption>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection





@push('script')

    <script>

        function showMoreDetails(infoId)

        {

            var formData = new FormData();

            formData.append('infoId',infoId);

            var server_url = '{{url("/info/details")}}';

            

            $.ajax({

                url: server_url,

                method: "POST",

                data: formData,

                dataType: 'text', 

                contentType:false,

                processData: false,

    

                success: function(response)

                {

                    response = JSON.parse(response);

                    var title = response.title;

                    var builtList = response.builtList;



                    $("#modal-title").html("Plus de detail pour l'information statistique" + title);

                    $("#modal-body").html(builtList);

                    $("#modal").modal("show");

                },

                error: function(error)

                {

                    console.log(JSON.parse(error));

                }

            });

        }



        function showMoreLevel(infoId)

        {

            var formData = new FormData();

            formData.append('infoId',infoId);

            var server_url = '{{url("/info/levels")}}';

            

            $.ajax({

                url: server_url,

                method: "POST",

                data: formData,

                dataType: 'text', 

                contentType:false,

                processData: false,

    

                success: function(response)

                { 

                    response = JSON.parse(response);

                    var title = response.title;

                    var builtList = response.builtList;



                    $("#modal-title").html("Liste des Niveaux associes");

                    $("#modal-body").html(builtList);

                    $("#modal").modal("show");

                },

                error: function(error)

                {

                    console.log(JSON.parse(error));

                }

            });

        }

    </script>

@endpush

