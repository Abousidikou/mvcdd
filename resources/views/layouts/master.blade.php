<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Accueil | Travail BJ</title>
    <!-- Favicon-->
    <link rel="icon" href="{{ asset('favicon.jpg') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{asset('template/adminBSB/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{asset('template/adminBSB/plugins/node-waves/waves.css')}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{asset('template/adminBSB/plugins/animate-css/animate.css')}}" rel="stylesheet" />

    <!-- Dropzone Css -->
    <link href="{{asset('template/adminBSB/plugins/dropzone/dropzone.css')}}" rel="stylesheet">

    <!-- Multi Select Css -->
    <link href="{{asset('template/adminBSB/plugins/multi-select/css/multi-select.css')}}" rel="stylesheet">

    <!-- Bootstrap Spinner Css -->
    <link href="{{asset('template/adminBSB/plugins/jquery-spinner/css/bootstrap-spinner.css')}}" rel="stylesheet">

    <!-- Bootstrap Tagsinput Css -->
    <link href="{{asset('template/adminBSB/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css')}}" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="{{asset('template/adminBSB/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

    <!-- Lou multi-select Css -->
    <link href="{{asset('template/adminBSB/plugins/lou-multi-select/css/multi-select.css')}}" rel="stylesheet">


    <!-- Morris Chart Css-->
    <link href="{{asset('template/adminBSB/plugins/morrisjs/morris.css')}}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{asset('template/adminBSB/css/style.css')}}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{asset('template/adminBSB/css/themes/all-themes.css')}}" rel="stylesheet" />
    <!-- JQuery DataTable Css -->
    <link href="{{asset('template/adminBSB/plugins/dataTables.bootstrap.css')}}" rel="stylesheet">

   
    


    <meta name="_token" content="{!! csrf_token() !!}" />
    @stack('style')
    <style>
        .dpdown{
            font-weight: bolder;
            color:#118959
        }
        .sidebar .user-info .info-container .user-helper-dropdown{
            right: -3%;
            bottom: -70px;
        }

        .bg-warning{
            background-color: darkgoldenrod;
        }

        .bg-danger{
            background-color: brown;
        }

        .right-sidebar p {
            margin: 0px !important;
        }
        .dropdown-menu {
            overflow: inherit !important;
        }
        .struct{
            font-size: revert !important;
        }
    </style>
</head>

<body class="theme-red">
    <!-- Page Loader -->
    {{-- <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Chargement en cours, veuillez patienter...</p>
        </div>
    </div> --}}
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    {{--  <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>  --}}
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="{{action('HomeController@index')}}" style="color: black">MTFP - BENIN</a>
                <div style="margin-left:35%; width:800px;">
                    <?= $builtUIStrucutresH ?>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- Call Search -->
                    {{--  <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>  --}}
                    <!-- #END# Call Search -->
                    <!-- Notifications -->
                        
                    <!-- #END# Notifications -->
                    <!-- Tasks -->
                        
                    <!-- #END# Tasks -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons" style="color:gray">more_vert</i></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
        <div class="user-info"  ondblclick="location.href='/'">
                <div class="image">
                    {{--  <img src="{{asset('images/logo/logo-mtfp.png')}}" width="48" height="48" alt="User" />  --}}
                </div>
                <div class="info-container">
                    {{--  <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#118959">{{ Auth::user()->name }}</div>
                    <div class="email" style="color:#118959">{{ Auth::user()->email }}</div>  --}}
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons dpdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{route('profile')}}"><i class="material-icons">person</i>Profil</a></li>
                            <li role="seperator" class="divider"></li>
                            <li><a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                <i class="material-icons">input</i>Se déconnecter</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list" id="indicators-list">
                    @guest
                        <li id="header" class="header text-center">Mode Lecture</li>
                    @else
                        <li id="header" class="header text-center">{{ Auth::user()->email }}</li>
                    @endguest
                    <?= $builtUIIndicator ?> 
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2018-2022 <a href="javascript:void(0);">MTFP - Informations Statistique</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0
                </div> 
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        <aside id="rightsidebar" class="right-sidebar">
            <div class="list-group">
                <a href="javascript:void(0);" class="list-group-item">
                    <h4 class="list-group-item-heading text-center" >PARAMETRES GENERAUX</h4>
                </a>
                @guest
                        <a href="{{ route('login') }}" class="list-group-item">
                            <p class="list-group-item-text">
                                Login     
                            </p>
                        </a>
                @else

                    @if(Auth::user()->role=="admin")
                    <div class="dropdown">
                        <a href="#" class="list-group-item btn btn-secondary dropdown-toggle"  role="button" id="dropdownMenuLinkAdmin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <p class="list-group-item-text" style="font-size: larger;">
                                  Admin<i class="material-icons">arrow_drop_down</i>
                                </p>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a href="{{route('domains')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES DOMAINES
                                </p>
                            </a>

                            <a href="{{route('subdomains')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES SOUS-DOMAINES
                                </p>
                            </a>

                            <a href="{{route('structures')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES STRUCTURES
                                </p>
                            </a>

                            <a href="{{route('agents')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES UTILISATEURS
                                </p>
                            </a>
                            
                            <a href="{{route('indicators')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES INDICATEURS
                                </p>
                            </a>


                            <a href="{{route('levels')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES NIVEAUX DE DESAGREGATION
                                </p>
                            </a>

                            <a href="{{route('infos')}}" class="list-group-item">
                                    <p class="list-group-item-text">
                                        GESTION DES INFORMATIONS STATISTIQUES
                                    </p>
                            </a>
                        </div>
                    </div>
                    <br><br><br>
                    <div class="dropdown">
                        <a href="#" class="list-group-item btn btn-secondary dropdown-toggle"  role="button" id="dropdownMenuLinkFormation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <p class="list-group-item-text" style="font-size: larger;">
                                  Formation<i class="material-icons">arrow_drop_down</i>
                                </p>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a href="{{route('agentFormation.data')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES PLANS DE FORMATION
                                </p>
                            </a>
                            <a href="{{route('miseEnStage.data')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES MISES EN STAGE
                                </p>
                            </a>

                            <a href="{{route('retourDeStage.data')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    GESTION DES RETOURS DE STAGE
                                </p>
                            </a>

                            <a href="{{route('aggregat.index')}}" class="list-group-item">
                                <p class="list-group-item-text">
                                    EXPORTER LES AGREGATS
                                </p>
                            </a>
                        </div>
                    </div>
                    @elseif(Auth::user()->role=="agents_m" || Auth::user()->role == "agents_gen")
                        <a href="{{route('agentFormation.data')}}" class="list-group-item">
                            <p class="list-group-item-text">
                                GESTION DES PLANS DE FORMATION
                            </p>
                        </a>
                        <a href="{{route('miseEnStage.data')}}" class="list-group-item">
                            <p class="list-group-item-text">
                                GESTION DES MISES EN STAGE
                            </p>
                        </a>

                        <a href="{{route('retourDeStage.data')}}" class="list-group-item">
                            <p class="list-group-item-text">
                                GESTION DES RETOURS DE STAGE
                            </p>
                        </a>

                        <a href="{{route('aggregat.index')}}" class="list-group-item">
                            <p class="list-group-item-text">
                                EXPORTER LES AGREGATS
                            </p>
                        </a>

                    @endif         
                        <br><br><br>
                        <a href="{{ route('logout') }}"
                                class="list-group-item text-center btn-danger"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"
                                style="color: white"
                        >
                            <i class="material-icons">input</i>Se déconnecter
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                @endguest
            </div>
        </aside> 
        <!-- #END# Right Sidebar -->
    </section>

    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                </div>
                <div class="modal-body" id="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">FERMER</button>
                </div>
            </div>
        </div>
    </div>

    
    
    <!-- Jquery Core Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery/jquery.min.js')}}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{asset('template/adminBSB/plugins/bootstrap/js/bootstrap.js')}}"></script>

    <!-- Select Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-slimscroll/jquery.slimscroll.js')}}"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/node-waves/waves.js')}}"></script>


    
    <!-- Dropzone Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/dropzone/dropzone.js')}}"></script>

    <!-- Input Mask Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-inputmask/jquery.inputmask.bundle.js')}}"></script>

    <!-- Multi Select Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/multi-select/js/jquery.multi-select.js')}}"></script> 

    <!-- Jquery Spinner Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-spinner/js/jquery.spinner.js')}}"></script>

    <!-- Bootstrap Tags Input Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js')}}"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-countto/jquery.countTo.js')}}"></script>

    <!-- Morris Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/raphael/raphael.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/morrisjs/morris.js')}}"></script>

    <!-- ChartJs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/flot-charts/jquery.flot.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/flot-charts/jquery.flot.resize.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/flot-charts/jquery.flot.pie.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/flot-charts/jquery.flot.categories.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/flot-charts/jquery.flot.time.js')}}"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-sparkline/jquery.sparkline.js')}}"></script>

    <!-- Lou multi-select Js -->
    <script src="{{asset('template/adminBSB/plugins/lou-multi-select/js/jquery.multi-select.js')}}"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/jquery.dataTables.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/buttons.flash.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/jszip.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/pdfmake.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/vfs_fonts.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/buttons.html5.min.js')}}"></script>
    <script src="{{asset('template/adminBSB/plugins/jquery-datatable/extensions/export/buttons.print.min.js')}}"></script>

    <!-- Custom Js -->
    <script src="{{asset('template/adminBSB/js/admin.js')}}"></script>
    <script src="{{asset('template/adminBSB/js/pages/forms/advanced-form-elements.js')}}"></script>
    <script src="{{asset('template/adminBSB/js/pages/index.js')}}"></script>
    <script src="{{asset('template/adminBSB/js/pages/tables/jquery-datatable.js')}}"></script>

    <script type="text/javascript" src="{{asset('js/gcharts/loader.js')}}"></script>
    
    <!-- Demo Js -->
    <script src="{{asset('template/adminBSB/js/demo.js')}}"></script>

    <!-- Custom local Dev Js -->
    <script>

  
        jQuery(document).ready(function($) {
                $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN' : $('meta[name="_token"]').attr('content') }
            });
          });
        
        $('.struct').off().on('click',function() {
            var id = this.id || $(this).data('id');
            $('.hbar').each(function(index,element){
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });

            $('[data-id='+id+']').removeClass('btn-primary');
            $('[data-id='+id+']').addClass('btn-default');

            $('.struct').each(function(index,element){
                $(this).removeClass('active');
            });
            
            $("#"+id).addClass('active');
            var formData = new FormData();
            formData.append('id',id);
            var server_url = '{{url("/build/indicators")}}'; 
            
            $.ajax({
                url: server_url,
                method: "POST",
                data: formData,
                dataType: 'text', 
                contentType:false,
                processData: false,
    
                success: function(response)
                {
                    $('.subdomains').remove(); 
                    $("#indicators-list").append(response);
                    $.AdminBSB.leftSideBar.activate();
                },
                error: function(error)
                {
                    console.log(JSON.parse(error));
                }
            })
            
        });

        $('.mt').off().on('click',function(e){
            e.preventDefault();
            alert("Fonctionnalite en cours de developpement");
            return;
        });
    </script>
    @stack('script')
</body>

</html>