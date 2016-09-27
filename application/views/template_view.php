<?php
header("Content-type: text/html; charset=utf-8");
$app = strtoupper($this->uri->segment(0));
$controlador = strtoupper($this->uri->segment(1));
$function = strtoupper($this->uri->segment(2));
if (empty($controlador)) {
    redirect(APP_SAC_URL . "Dashboard/");
}
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
        <link rel="icon" href="<?= APP_SAC_URL; ?>resources/images/ico/32x32.png" type="image/png">
        <title><?= HTMLVARS_TITLE_PAGE; ?></title>
        <link href="<?= APP_SAC_URL; ?>resources/styles/app.min.css" rel="stylesheet">
        <link href="<?= APP_SAC_URL; ?>resources/styles/personalizados.css" rel="stylesheet" type="text/css"/>
        <link href="<?= APP_SAC_URL; ?>resources/styles/personalizados_sac.css" rel="stylesheet" type="text/css"/>
        <script src="<?= APP_SAC_URL; ?>resources/scripts/jquery-2.2.4.min.js" type="text/javascript"></script>
        <script src="<?= APP_SAC_URL; ?>resources/plugins/tether-1.3.3/js/tether.min.js" type="text/javascript"></script>
        <script src="<?= APP_SAC_URL; ?>resources/scripts/bootstrap.min.js" type="text/javascript"></script>

        <!-- Plugins predeterminados -->
        <script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-validation/dist/localization/messages_es.js" type="text/javascript"></script>

        <link href="<?= APP_SAC_URL; ?>resources/plugins/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css"/>
        <script src="<?= APP_SAC_URL; ?>resources/plugins/datatables/media/js/jquery.dataTables.js"></script>
        <script src="<?= APP_SAC_URL; ?>resources/plugins/datatables/media/js/dataTables.bootstrap4.js"></script>

        <!-- Alert -->
        <link href="<?= APP_SAC_URL; ?>resources/plugins/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css"/>
        <script src="<?= APP_SAC_URL; ?>resources/plugins/sweetalert/dist/sweetalert.min.js" type="text/javascript"></script>

        <script>
            var base_url = "<?= base_url(); ?>";
            var base_sac_url = "<?= APP_SAC_URL; ?>";
            var controller = "<?= $this->module['controller']; ?>";
            var tituloApp = "<?= APP_NAME; ?>";
        </script>
        <script src="<?= APP_SAC_URL; ?>resources/scripts/reemplazar_alert.js" type="text/javascript"></script>
        <script src="<?= APP_SAC_URL; ?>resources/scripts/funciones_basicas.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="app"> 
            <div class="off-canvas-overlay" data-toggle="sidebar"></div>
            <div class="sidebar-panel">
                <div class="brand"> 
                    <a href="javascript:;" data-toggle="sidebar" class="toggle-offscreen hidden-lg-up"><i class="material-icons">menu</i> </a>
                    <a class="brand-logo" href="<?= APP_SAC_URL; ?>"><img class="expanding-hidden" src="<?= APP_SAC_URL; ?>resources/images/logo.png" alt=""> </a>
                </div>
                <div class="nav-profile dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        <div class="user-image">
                            <img src="<?= APP_SAC_URL; ?>resources/images/avatar.jpg" class="avatar img-circle" alt="user" title="user">
                        </div>
                        <div class="user-info expanding-hidden"><?= strtoupper($this->session->usuario); ?>
                            <small class="bold"><?= ucfirst(strtolower($this->session->puesto)); ?></small>
                        </div>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?= APP_SAC_URL; ?>Mi_perfil">Mi cuenta</a>
                        <a class="dropdown-item" href="javascript:;">Opcion 1</a>
                        <a class="dropdown-item" href="javascript:;">
                            <span class="label bg-danger pull-right">34</span>
                            <span>Notificaciones</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:;">Ayuda</a>
                        <a class="dropdown-item" href="<?= APP_SAC_URL . "Login/cerrar_sesion" ?>">Cerrar sesión</a>
                    </div>
                </div>
                <nav>
                    <!--<p class="nav-title">APLICACIONES</p>-->
                    <ul class="nav">
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "DASHBOARD" ? 'bg-info' : ''; ?>">
                            <a href="<?= APP_SAC_URL; ?>Dashboard">
                                <i class="fa fa-2x fa-home text-primary"></i>
                                <span>SAC</span>
                            </a>
                        </li>
                        <li class="cysa<?= (APP_NAMESPACE === "cysa") ? " open" : ""; ?>">
                            <a href="javascript:;">
                                <span class="menu-caret">
                                    <i class="material-icons">arrow_drop_down</i>
                                </span>
                                <i class="fa fa-2x fa-file-text text-success"></i>
                                <span>CYSA</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "DASHBOARD" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Dashboard"><i class="material-icons">today</i><span>Inicio</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "AUDITORIAS" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Auditorias"><i class="material-icons">verified_user</i><span>Mi auditoría</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "CATALOGOS" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Catalogos"><i class="material-icons">grid_on</i><span>Catálogo</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "REPORTES" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Reportes"><i class="material-icons">print</i><span>Reportes</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "ISO" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Iso"><i class="material-icons">star</i><span>ISO SGC</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "PRODUCTOS" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Productos"><i class="material-icons">mood_bad</i><span>Producto No Conforme</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cysa" && $controlador == "AYUDA" ? 'bg-success' : ''; ?>"><a href="<?= APP_CYSA_URL; ?>Ayuda"><i class="material-icons">help_outline</i><span>Ayuda</span></a></li>
                            </ul>
                        </li>
                        <li class="cisop<?= (APP_NAMESPACE === "cisop") ? " open" : ""; ?>">
                            <a href="javascript:;">
                                <span class="menu-caret"><i class="material-icons">arrow_drop_down</i></span>
                                <i class="fa fa-2x fa-archive text-danger"></i>
                                <span>CISOP</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="<?= APP_NAMESPACE === "cisop" && $controlador == "STOCKS" ? 'bg-danger' : ''; ?>"><a href="<?= APP_CISOP_URL; ?>Stocks"><i class="material-icons">playlist_add_check</i><span>Stock de materiales</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cisop" && $controlador == "SOLICITUDES" ? 'bg-danger' : ''; ?>"><a href="<?= APP_CISOP_URL; ?>Solicitudes"><i class="material-icons">shopping_cart</i><span>Solicitud de materiales</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cisop" && $controlador == "SURTIDOS" ? 'bg-danger' : ''; ?>"><a href="<?= APP_CISOP_URL; ?>Surtidos"><i class="material-icons">archive</i><span>Surtido de materiales</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cisop" && $controlador == "PROVEEDURIAS" ? 'bg-danger' : ''; ?>"><a href="<?= APP_CISOP_URL; ?>Proveedurias"><i class="material-icons">location_city</i><span>Solicitud de Proveeduría</span></a></li>
                                <li class="<?= APP_NAMESPACE === "cisop" && $controlador == "ENTRADAS" ? 'bg-danger' : ''; ?>"><a href="<?= APP_CISOP_URL; ?>Entradas"><i class="material-icons">local_shipping</i><span>Entrada a Bodega</span></a></li>
                            </ul>
                        </li>
                        <li class="correspondencia<?= (APP_NAMESPACE === "correspondencia") ? " open " . APP_NAMESPACE : ""; ?>">
                            <a href="javascript:;">
                                <span class="menu-caret">
                                    <i class="material-icons">arrow_drop_down</i>
                                </span>
                                <i class="fa fa-2x fa-envelope text-warning"></i>
                                <span>Correspondencia</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="<?= APP_NAMESPACE === "correspondencia" && $controlador == "CORRESPONDENCIAS" ? 'bg-warning' : ''; ?>"><a href="<?= APP_CORRESPONDENCIA_URL; ?>Correspondencias"><i class="material-icons">drafts</i><span>Inicio</span></a></li>
                                <li class="<?= APP_NAMESPACE === "correspondencia" && $controlador == "REPORTES" ? 'bg-warning' : ''; ?>"><a href="<?= APP_CORRESPONDENCIA_URL; ?>Reportes"><i class="material-icons">print</i><span>Reporte</span></a></li>
                            </ul>
                        </li>
                    </ul>
                    <p class="nav-title">OPCIONES</p>
                    <ul class="nav">
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "SOLICITUDES" ? 'bg-info' : ''; ?>"><a href="<?= APP_SAC_URL; ?>Solicitudes"><i class="material-icons">beenhere</i><span>Solicitudes de Servicio</span></a></li>
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "ACTIVIDADES" ? 'bg-info' : ''; ?>"><a href="<?= APP_SAC_URL; ?>Actividades"><i class="material-icons">av_timer</i><span>Control de actividades</span></a></li>
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "PRESTAMOS_EQUIPOS" ? 'bg-info' : ''; ?>"><a href="<?= APP_SAC_URL; ?>Prestamos_equipos"><i class="material-icons">devices</i><span>Préstamo de equipos</span></a></li>
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "USUARIOS" ? 'bg-info' : ''; ?>"><a href="<?= APP_SAC_URL; ?>Usuarios"><i class="material-icons">supervisor_account</i><span>Control de usuarios</span></a></li>
                        <li class="<?= APP_NAMESPACE === "sac" && $controlador == "CATALOGOS" ? 'bg-info' : ''; ?>"><a href="<?= APP_SAC_URL; ?>Admin_catalogos"><i class="material-icons">grid_on</i><span>Administración de catálogos</span></a></li>
                    </ul>
                </nav>
            </div>

            <div class="main-panel">
                <nav class="header navbar">
                    <div class="header-inner">
                        <div class="navbar-item navbar-spacer-right brand hidden-lg-up">
                            <a href="javascript:;" data-toggle="sidebar" class="toggle-offscreen">
                                <i class="material-icons">menu</i>
                            </a>
                            <a class="brand-logo hidden-xs-down brand-logo-white">
                                <img src="<?= APP_SAC_URL; ?>resources/images/logo_white.png" alt="logo">
                            </a>
                        </div>
                        <a class="navbar-item navbar-spacer-right navbar-heading hidden-md-down" href="<?= base_url() . $this->module['controller']; ?>">
                            <span><?= $this->module['title']; ?></span>
                        </a>
                    </div>
                </nav>
                <?php
                $info = $this->session->flashdata("informacion");
                if ($info !== NULL) {
                    echo '<div id="infoAlert" class="alert alert-' . $info['state'] . '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $info['message'] . '</div>';
                }
                ?>
                <div class="main-content">
                    <div class="content-view">
                        <?= $this->template; ?>
                    </div>
                    <div class="content-footer">
                        <nav class="footer-right">
                            <ul class="nav">
                                <li>
                                    <a href="javascript:;">Feedback</a>
                                </li>
                            </ul>
                        </nav>
                        <nav class="footer-left">
                            <ul class="nav">
                                <li>
                                    <a href="javascript:;">
                                        <span>Copyright</span> &copy; <?= date("Y"); ?> SAC
                                    </a>
                                </li>
                                <li class="hidden-md-down">
                                    <a href="<?= APP_SAC_URL; ?>Dashboard/politicas_privacidad">Políticas de privacidad</a>
                                </li>
                                <li class="hidden-md-down">
                                    <a href="<?= APP_SAC_URL; ?>Dashboard/terminos_y_condiciones">Términos y condiciones</a>
                                </li>
                                <li class="hidden-md-down">
                                    <a href="<?= APP_SAC_URL; ?>Ayuda">Ayuda</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <!--<script src="<?= APP_SAC_URL; ?>resources/scripts/app.min.js" type="text/javascript"></script>-->
    <script src="<?= APP_SAC_URL; ?>resources/scripts/app.js"></script>
    <script src="<?= APP_SAC_URL; ?>resources/scripts/main.js" type="text/javascript"></script>
</html>