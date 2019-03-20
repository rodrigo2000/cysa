<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
        <link rel="icon" href="../../timeline/images/ico/32x32.png" type="image/png">
        <title><?= $auditoria['nombreAuditoria']; ?></title>
        <link href="../../timeline/styles/app.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="../../timeline/styles/app.min.print.css" rel="stylesheet" type="text/css" media="print"/>
        <link href="../../timeline/styles/app_print.css" rel="stylesheet" type="text/css" media="print"/>
        <link href="../../timeline/styles/personalizados.css" rel="stylesheet" type="text/css"/>
        <link href="../../timeline/styles/personalizados_cysa.css" rel="stylesheet" type="text/css"/>
        <script src="../../_js/timeline/jquery-3.1.0.min.js" type="text/javascript"></script>

        <!-- Promise.finally support -->
        <script src="../../timeline/plugins/finally.js" type="text/javascript"></script>
        <!-- Bootrstrap -->
        <script src="../../timeline/plugins/app.min.js" type="text/javascript"></script>
        <!-- Tether 1.3.3 -->
        <link href="../../timeline/plugins/tether-1.3.3/css/tether.min.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/tether-1.3.3/js/tether.min.js" type="text/javascript"></script>
        <script>
            var idAuditoria = <?= $idAuditoria; ?>;
            moment.locale('es'); // change the global locale to Spanish
        </script>
        <script src="../../timeline/timeline_view.js" type="text/javascript"></script>
        <link href="../../timeline/styles/timeline.css" rel="stylesheet" type="text/css"/>
        <style>
            div { text-align: center;}
        </style>
    </head>
    <body>
        <div class="app">
            <div class="main-panel">
                <div class="jumbotron">
                    <h1 class="display-3">Opps!</h1>
                    <p class="lead">Las auditorías anteriores al año 2016 no pueden ser visualizadas en la línea de tiempo.</p>
                    <hr class="my-4">
                    <p>Para cualquier duda dirigirse al Departamento de ATI.</p>
                    <p class="lead">
                        <a class="btn btn-primary btn-lg" href="javascript:window.close();" role="button">Cerrar ventana</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>