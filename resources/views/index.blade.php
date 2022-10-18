@extends('layouts/master')


@push('style')
    <!-- <style type="text/css">
            #flot-placeholder
            {
                width:350px;
                height:300px;
            }        
    </style> -->
<style>
/*     #myProgress {
    width: 100%;
    background-color: #5834eb;
    }

    #myBar {
    width: 10%;
    height: 30px;
    background-color: #04AA6D;
    text-align: center;
    line-height: 30px;
    color: white;
    } */
</style>
@endpush

@section('content')

<div class="block-header">

    <h2>TABLEAU DE BORD</h2>

</div>



<!-- Widgets -->

    <div class="row clearfix">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

            <div class="info-box bg-pink hover-expand-effect">

                <div class="icon">

                    <i class="material-icons">playlist_add_check</i>

                </div>

                <div class="content">

                    <div class="text">INDICATEURS</div>

                    <div class="number count-to" data-from="0" data-to="{{$numberOfIndicators}}" data-speed="15" data-fresh-interval="20"></div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

            <div class="info-box bg-cyan hover-expand-effect">

                <div class="icon">

                    <i class="material-icons">playlist_add_check</i>

                </div>

                <div class="content">

                    <div class="text">NIVEAUX</div>

                    <div class="number count-to" data-from="0" data-to="{{$numberOfLevels}}" data-speed="1000" data-fresh-interval="20"></div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

            <div class="info-box bg-light-green hover-expand-effect">

                <div class="icon">

                    <i class="material-icons">playlist_add_check</i>

                </div>

                <div class="content">

                    <div class="text">TYPES</div>

                    <div class="number count-to" data-from="0" data-to="{{$numberOfTypes}}" data-speed="1000" data-fresh-interval="20"></div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

            <div class="info-box bg-orange hover-expand-effect">

                <div class="icon">

                    <i class="material-icons">playlist_add_check</i>

                </div>

                <div class="content">

                    <div class="text">DONNEES</div>

                    <div class="number count-to" data-from="0" data-to="{{$numberOfData}}" data-speed="1000" data-fresh-interval="20"></div>

                </div>

            </div>

        </div>

    </div>

<!-- #END# Widgets -->

<!-- CPU Usage -->

{{-- <div class="row clearfix">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="card">

            <div class="header">

                <div class="row clearfix">

                    <div class="col-xs-12 col-sm-6">

                        <h2>CPU USAGE (%)</h2>

                    </div>

                    <div class="col-xs-12 col-sm-6 align-right">

                        <div class="switch panel-switch-btn">

                            <span class="m-r-10 font-12">REAL TIME</span>

                            <label>OFF<input type="checkbox" id="realtime" checked><span class="lever switch-col-cyan"></span>ON</label>

                        </div>

                    </div>

                </div>

                <ul class="header-dropdown m-r--5">

                    <li class="dropdown">

                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">

                            <i class="material-icons">more_vert</i>

                        </a>

                        <ul class="dropdown-menu pull-right">

                            <li><a href="javascript:void(0);">Action</a></li>

                            <li><a href="javascript:void(0);">Another action</a></li>

                            <li><a href="javascript:void(0);">Something else here</a></li>

                        </ul>

                    </li>

                </ul>

            </div>

            <div class="body">

                <div id="real_time_chart" class="dashboard-flot-chart"></div>

            </div>

        </div>

    </div>

</div> --}}

<!-- #END# CPU Usage -->

{{-- <div class="row clearfix">

    <!-- Visitors -->

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="card">

            <div class="body bg-pink">

                <div class="sparkline" data-type="line" data-spot-Radius="4" data-highlight-Spot-Color="rgb(233, 30, 99)" data-highlight-Line-Color="#fff"

                     data-min-Spot-Color="rgb(255,255,255)" data-max-Spot-Color="rgb(255,255,255)" data-spot-Color="rgb(255,255,255)"

                     data-offset="90" data-width="100%" data-height="92px" data-line-Width="2" data-line-Color="rgba(255,255,255,0.7)"

                     data-fill-Color="rgba(0, 188, 212, 0)">

                    12,10,9,6,5,6,10,5,7,5,12,13,7,12,11

                </div>

                <ul class="dashboard-stat-list">

                    <li>

                        TODAY

                        <span class="pull-right"><b>1 200</b> <small>USERS</small></span>

                    </li>

                    <li>

                        YESTERDAY

                        <span class="pull-right"><b>3 872</b> <small>USERS</small></span>

                    </li>

                    <li>

                        LAST WEEK

                        <span class="pull-right"><b>26 582</b> <small>USERS</small></span>

                    </li>

                </ul>

            </div>

        </div>

    </div>

    <!-- #END# Visitors -->

    <!-- Latest Social Trends -->

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="card">

            <div class="body bg-cyan">

                <div class="m-b--35 font-bold">LATEST SOCIAL TRENDS</div>

                <ul class="dashboard-stat-list">

                    <li>

                        #socialtrends

                        <span class="pull-right">

                            <i class="material-icons">trending_up</i>

                        </span>

                    </li>

                    <li>

                        #materialdesign

                        <span class="pull-right">

                            <i class="material-icons">trending_up</i>

                        </span>

                    </li>

                    <li>#adminbsb</li>

                    <li>#freeadmintemplate</li>

                    <li>#bootstraptemplate</li>

                    <li>

                        #freehtmltemplate

                        <span class="pull-right">

                            <i class="material-icons">trending_up</i>

                        </span>

                    </li>

                </ul>

            </div>

        </div>

    </div>

    <!-- #END# Latest Social Trends -->

    <!-- Answered Tickets -->

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="card">

            <div class="body bg-teal">

                <div class="font-bold m-b--35">ANSWERED TICKETS</div>

                <ul class="dashboard-stat-list">

                    <li>

                        TODAY

                        <span class="pull-right"><b>12</b> <small>TICKETS</small></span>

                    </li>

                    <li>

                        YESTERDAY

                        <span class="pull-right"><b>15</b> <small>TICKETS</small></span>

                    </li>

                    <li>

                        LAST WEEK

                        <span class="pull-right"><b>90</b> <small>TICKETS</small></span>

                    </li>

                    <li>

                        LAST MONTH

                        <span class="pull-right"><b>342</b> <small>TICKETS</small></span>

                    </li>

                    <li>

                        LAST YEAR

                        <span class="pull-right"><b>4 225</b> <small>TICKETS</small></span>

                    </li>

                    <li>

                        ALL

                        <span class="pull-right"><b>8 752</b> <small>TICKETS</small></span>

                    </li>

                </ul>

            </div>

        </div>

    </div>

    <!-- #END# Answered Tickets -->

</div> --}}



<div class="row clearfix">

    <!-- Tableau-graphique et derniere ligne ajoutées de board Info -->
    
    
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
        <div class="card">

            <div class="header">

                <h2 id="label">GRAPHIQUE </h2> 

            </div>

            <div class="body">

                <div id=""><canvas id="flot-placeholder" width="500" height="300"></canvas></div>

            </div>

        </div>
        <!-- Tableau de board Info -->
        <div class="card">

            <div class="header">

                <h2>DERNIERES DONNEES AJOUTEES</h2> 

            </div>

            <div class="body">

                <div class="table-responsive">

                        <table class="table table-striped table-bordered table-condensed table-hover">

                                <thead>

                                    <tr>

                                        <th>Annee</th> 

                                        <th>Sous domaines</th>

                                        <th>Indicateurs</th>

                                        <th>Niveaux</th>  

                                        <th>Valeurs</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($infos as $info)

                                    <tr>

                                        <td>{{$info->year}}</td>

                                        <td>{{$info->subd}}</td>

                                        <td>{{$info->indicat}}</td>

                                        <td>{{$info->levelof}}

                                            @if ($info->multilevel == true)

                                                <a onclick="showMoreLevel({{$info->id}})" href="javascript:void(0);" style="font-size:large" title="cliquez pour voir les autres niveaux">+</a>                                 

                                            @endif

                                        </td>

                                        <td>{{$info->value}}</td>

                                        <td>

                                            <a onclick="showMoreDetails({{$info->id}})" href="javascript:void(0);" class="btn btn-xs btn-warning waves-effect"

                                                title="Cliquer pour plus de details" style=""

                                            >

                                                <i class="material-icons">info</i>

                                            </a>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                </div>

            </div>

        </div>
        <!-- #END#  Info -->
    </div>

    <!-- #END#  Info -->



    <!-- List subdomain 2/4 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="card">

            <div class="header">

                <h2>INDICATEURS RENSEIGNÉES</h2>

            </div>

            <div class="body">

                <div id="myBar" class="dashboard-donut-chart"></div>

            </div>

        </div>

    </div>

    <!-- #END# List subdomain -->
 
    <!-- Donuts Usage -->

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="card">

            <div class="header">

                <h2>POURCENTAGE DE DONNEES PAR DOMAINE</h2>

            </div>

            <div class="body">

                <div id="donut_chart" class="dashboard-donut-chart"></div>

            </div>

        </div>

    </div>

    <!-- #END# Donuts Usage -->

</div>

@endsection

@push('script')

    <script>

        var domains = [];
        var datas_audit = [];
        var datas_func = [];


        window.onload = function(){
            var server_url = '{{url("/dash/stat")}}';
            $.ajax({

                url: server_url,

                method: "GET",

                dataType: 'text', 

                contentType:false,

                processData: false,

    

                success: function(response)

                {

                    if(document.getElementById("donut_chart"))

                    {

                        Morris.Donut({

                            element: 'donut_chart',

                            data: JSON.parse(response),

                            colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

                            formatter: function (y) {

                                return y + '%'

                            }

                        });

                    }

                },

                error: function(error)

                {

                    console.log(JSON.parse(error));

                }

            })

            var list_url = "{{ url('/listSousdomain') }}"
            $.ajax({

                url: list_url,

                    method: "GET",

                    dataType: 'text', 

                    contentType:false,

                    processData: false,



                    success: function(response)

                    {
                       
                        domains = JSON.parse(response);
                        //console.log(domains);

                        var cont = document.getElementById('myBar');
                        document.getElementById('myBar').style.height = "250px";
                        var ul = document.createElement('ul');
                        ul.setAttribute('style', 'padding: 0; margin: 0;');
                        ul.setAttribute('id', 'theList');
                        for (const [key, obj1] of Object.entries(domains[3])) {
                            var li = document.createElement('li');     // create li element.
                            li.innerHTML = "<strong style='font-weight:bold;'>"+key+"</strong>"+" : "+obj1[0]+"/"+obj1[1];      // assigning text to li using array value.
                            li.setAttribute('style', 'display: block;');   // remove the bullets.
                            ul.appendChild(li);     // append li to ul.
                        }

                        cont.appendChild(ul);       // add list to the container. */

                    },


                error: function(error)

                {

                    console.log(error);

                }

            });

           var audit_trav_url = "{{ url('/auditANDtravail') }}"
            $.ajax({

                url: audit_trav_url,

                method: "GET",

                dataType: 'text', 

                contentType:false,

                processData: false,



                success: function(response)

                {
                    datas_audit = JSON.parse(response);
                    document.getElementById('flot-placeholder').replaceChildren();
                    document.getElementById('label').innerHTML = "Nombre d'usagers/clients ayant adressé une requête ou plainte au MTFP";
                    //console.log('audit and travail : ',datas_audit);
                    
                    if(document.getElementById("flot-placeholder"))

                    {

                        const ctx = document.getElementById('flot-placeholder').getContext('2d');
                        const dataer = {
                            labels: datas_audit[0][0],
                            datasets: [
                                {
                                label: 'Plaintes en cours',
                                data: datas_audit[0][1],
                                borderColor: "#cf0610",
                                fill: false,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.4
                                }, {
                                label: 'Plaintes traitées',
                                data: datas_audit[0][2],
                                borderColor: "#1b08c9",
                                fill: false,
                                tension: 0.4
                                }, {
                                label: 'Plaintes traitées',
                                data: datas_audit[0][2],
                                borderColor: "#08c945",
                                fill: false
                                }
                            ]
                        };
                        new Chart(ctx, {
                            type: 'line',
                            data: dataer,
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Nombre d\'usagers/clients ayant adressé une requête ou plainte au MTFP'
                                    },
                                },
                                interaction: {
                                    intersect: false,
                                },
                                scales: {
                                    x: {
                                        display: true,
                                        title: {
                                        display: true
                                        }
                                    },
                                    y: {
                                        display: true,
                                        title: {
                                        display: true,
                                        text: 'Value'
                                        },
                                        suggestedMin: -10,
                                        suggestedMax: 200
                                    }
                                }
                            },
                        });
                            

                    }
                },

                error: function(error)

                {

                    console.log(error);

                }

            });
            

            var func_reform_url = "{{ url('/funcANDreform') }}"
            $.ajax({

                url: func_reform_url,

                method: "GET",

                dataType: 'text', 

                contentType:false,

                processData: false,



                success: function(response)

                {
                    datas_func = JSON.parse(response);
                    document.getElementById('flot-placeholder').replaceChildren();
                    document.getElementById('flot-placeholder').style.width = "500px";
                    document.getElementById('flot-placeholder').style.height = "300px";
                    //console.log('func and reform : ',datas_func);
                    

                },

                error: function(error)

                {

                    console.log(error);

                }

            });

        }




        /*************************           Fonction d'affichage             ******************************/
        // ✅ Get first element with data-id = `box1²


        
        const travailSocial = document.querySelector('[data-id="10"]');
        travailSocial.addEventListener("click", graphicTravailSocial);
        const fonctionPublic = document.querySelector('[data-id="11"]');
        fonctionPublic.addEventListener("click", graphicFonctionPublique);
        const reforme = document.querySelector('[data-id="12"]');
        reforme.addEventListener("click", graphicReforme);
        const audit = document.querySelector('[data-id="13"]');
        audit.addEventListener("click", graphicAudit);

        
       /*************************           END   Fonction d'affichage             ******************************/
        
        
        

        /************************       Fonctions            *********************/
        function graphicAudit()
        {
            document.getElementById('flot-placeholder').replaceChildren();
            document.getElementById('myBar').replaceChildren();
            document.getElementById('flot-placeholder').style.width = "500px";
            document.getElementById('flot-placeholder').style.height = "300px";
            document.getElementById('myBar').style.height = "250px";

            document.getElementById('label').innerHTML = "Evolution du nombre de plaintes";
            
            var cont = document.getElementById('myBar');
            var ul = document.createElement('ul');
            ul.setAttribute('style', 'padding: 0; margin: 0;');
            ul.setAttribute('id', 'theList');
            for (const [key, obj1] of Object.entries(domains[3])) {
                var li = document.createElement('li');     // create li element.
                li.innerHTML = "<strong style='font-weight:bold;'>"+key+"</strong>"+" : "+obj1[0]+"/"+obj1[1];      // assigning text to li using array value.
                li.setAttribute('style', 'display: block;');   // remove the bullets.
                ul.appendChild(li);     // append li to ul.
            }

            cont.appendChild(ul);       // add list to the container. */


            if(document.getElementById("flot-placeholder"))

            {

                const ctx = document.getElementById('flot-placeholder').getContext('2d');
                const dataer = {
                    labels: datas_audit[0][0],
                    datasets: [
                            {
                            label: 'Plaintes en cours',
                            data: datas_audit[0][1],
                            borderColor: "#cf0610",
                            fill: false,
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                            }, {
                            label: 'Plaintes traitées',
                            data: datas_audit[0][2],
                            borderColor: "#1b08c9",
                            fill: false,
                            tension: 0.4
                            }, {
                            label: 'Plaintes traitées',
                            data: datas_audit[0][2],
                            borderColor: "#08c945",
                            fill: false
                            }
                        ]
                    };
                    new Chart(ctx, {
                        type: 'line',
                        data: dataer,
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Nombre d\'usagers/clients ayant adressé une requête ou plainte au MTFP'
                                },
                            },
                            interaction: {
                                intersect: false,
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                    display: true
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                    display: true,
                                    text: 'Value'
                                    },
                                    suggestedMin: -10,
                                    suggestedMax: 200
                                }
                            }
                    },
                });
                    

            }
            
        } 




        function graphicTravailSocial()
        {
            document.getElementById('flot-placeholder').replaceChildren();
            document.getElementById('myBar').replaceChildren();
            document.getElementById('flot-placeholder').style.width = "500px";
            document.getElementById('flot-placeholder').style.height = "300px";
            document.getElementById('myBar').style.height = "250px";

            document.getElementById('label').innerHTML = "Evolution du nombre de contrat de travail visé ";
            var cont = document.getElementById('myBar');

            var ul = document.createElement('ul');
            ul.setAttribute('style', 'padding: 0; margin: 0;');
            ul.setAttribute('id', 'theList');
            for (const [key, obj1] of Object.entries(domains[0])) {
                var li = document.createElement('li');     // create li element.
                li.innerHTML = "<strong style='font-weight:bold;'>"+key+"</strong>"+" : "+obj1[0]+"/"+obj1[1];      // assigning text to li using array value.
                li.setAttribute('style', 'display: block;');   // remove the bullets.
                ul.appendChild(li);     // append li to ul.
            }

            cont.appendChild(ul);       // add list to the container. */





            if(document.getElementById("flot-placeholder"))

            {

                const ctx = document.getElementById('flot-placeholder').getContext('2d');
                const dataer = {
                    labels: datas_audit[1][0],
                    datasets: [
                        {
                        label: 'Expatriés',
                        data: datas_audit[1][1],
                        borderColor: "#cf0610",
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4
                        }, {
                        label: 'Nationaux',
                        data: datas_audit[1][2],
                        borderColor: "#1b08c9",
                        fill: false,
                        tension: 0.4
                        }, {
                        label: 'Total',
                        data: datas_audit[1][2],
                        borderColor: "#08c945",
                        fill: false
                        }
                    ]
                };
                new Chart(ctx, {
                    type: 'line',
                    data: dataer,
                    options: {
                        responsive: true,
                        plugins: {
                        title: {
                            display: true,
                            text: 'Evolution du nombre de contrat de travail visé'
                        },
                        },
                        interaction: {
                        intersect: false,
                        },
                        scales: {
                        x: {
                            display: true,
                            title: {
                            display: true
                            }
                        },
                        y: {
                            display: true,
                            title: {
                            display: true,
                            text: 'Value'
                            },
                            suggestedMin: -10,
                            suggestedMax: 200
                        }
                        }
                    },
                });
                    

            }
            
        }

        function graphicFonctionPublique()
        {

            document.getElementById('flot-placeholder').replaceChildren();
            document.getElementById('myBar').replaceChildren();
            document.getElementById('flot-placeholder').style.width = "500px";
            document.getElementById('flot-placeholder').style.height = "300px";
            document.getElementById('myBar').style.height = "250px";

            document.getElementById('label').innerHTML = "Evolution des effectifs des agents civils de l’état par statut";
            var cont = document.getElementById('myBar');

            var ul = document.createElement('ul');
            ul.setAttribute('style', 'padding: 0; margin: 0;');
            ul.setAttribute('id', 'theList');
            for (const [key, obj1] of Object.entries(domains[1])) {
                var li = document.createElement('li');     // create li element.
                li.innerHTML = "<strong style='font-weight:bold;'>"+key+"</strong>"+" : "+obj1[0]+"/"+obj1[1];      // assigning text to li using array value.
                li.setAttribute('style', 'display: block;');   // remove the bullets.
                ul.appendChild(li);     // append li to ul.
            }

            cont.appendChild(ul);       // add list to the container. */



            
            if(document.getElementById("flot-placeholder"))

            {

                const ctx = document.getElementById('flot-placeholder').getContext('2d');
                const dataer = {
                    labels: datas_func[0][0],
                    datasets: [
                        {
                        label: 'ACE',
                        data: datas_func[0][1],
                        borderColor: "#cf0610",
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4
                        }, {
                        label: 'APE',
                        data: datas_func[0][2],
                        borderColor: "#1b08c9",
                        fill: false,
                        tension: 0.4
                        }, {
                        label: 'Total',
                        data: datas_func[0][2],
                        borderColor: "#08c945",
                        fill: false
                        }
                    ]
                };
                new Chart(ctx, {
                    type: 'line',
                    data: dataer,
                    options: {
                        responsive: true,
                        plugins: {
                        title: {
                            display: true,
                            text: 'Evolution des effectifs des agents civils de l’état par statut'
                        },
                        },
                        interaction: {
                        intersect: false,
                        },
                        scales: {
                        x: {
                            display: true,
                            title: {
                            display: true
                            }
                        },
                        y: {
                            display: true,
                            title: {
                            display: true,
                            text: 'Value'
                            },
                            suggestedMin: -10,
                            suggestedMax: 200
                        }
                        }
                    },
                });
                    

            }

        }




        function graphicReforme()
        {
            document.getElementById('flot-placeholder').replaceChildren();
            document.getElementById('myBar').replaceChildren();
            document.getElementById('flot-placeholder').style.width = "500px";
            document.getElementById('flot-placeholder').style.height = "300px";
            document.getElementById('myBar').style.height = "250px";
            

            document.getElementById('label').innerHTML = "Nombre de séminaire et formation ";
            var cont = document.getElementById('myBar');

            var ul = document.createElement('ul');
            ul.setAttribute('style', 'padding: 0; margin: 0;');
            ul.setAttribute('id', 'theList');
            for (const [key, obj1] of Object.entries(domains[2])) {
                var li = document.createElement('li');     // create li element.
                li.innerHTML = "<strong style='font-weight:bold;'>"+key+"</strong>"+" : "+obj1[0]+"/"+obj1[1];      // assigning text to li using array value.
                li.setAttribute('style', 'display: block;');   // remove the bullets.
                ul.appendChild(li);     // append li to ul.
            }

            cont.appendChild(ul);       // add list to the container. */


            if(document.getElementById("flot-placeholder"))

            {

                const ctx = document.getElementById('flot-placeholder').getContext('2d');
                const dataer = {
                    labels: datas_func[1][0],
                    datasets: [
                        {
                        label: 'INFOSEC',
                        data: datas_func[1][1],
                        borderColor: "#cf0610",
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4
                        }, {
                        label: 'Séminaires',
                        data: datas_func[1][2],
                        borderColor: "#1b08c9",
                        fill: false,
                        tension: 0.4
                        }, {
                        label: 'Formations',
                        data: datas_func[1][2],
                        borderColor: "#08c945",
                        fill: false
                        }
                    ]
                };
                new Chart(ctx, {
                    type: 'line',
                    data: dataer,
                    options: {
                        responsive: true,
                        plugins: {
                        title: {
                            display: true,
                            text: 'Nombre de séminaire et formation'
                        },
                        },
                        interaction: {
                        intersect: false,
                        },
                        scales: {
                        x: {
                            display: true,
                            title: {
                            display: true
                            }
                        },
                        y: {
                            display: true,
                            title: {
                            display: true,
                            text: 'Value'
                            },
                            suggestedMin: -10,
                            suggestedMax: 200
                        }
                        }
                    },
                });
                    

            }
            
        }
    

        
        
        /************************       END    Fonction publique             *********************/
        


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