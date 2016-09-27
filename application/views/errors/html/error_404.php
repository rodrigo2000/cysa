<!doctype html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
        <title><?= HTMLVARS_TITLE_PAGE; ?></title>
        <link rel="stylesheet" href="<?= APP_SAC_URL; ?>resources/styles/app.min.css">
    </head>
    <body>
        <div class="app error-page no-padding no-footer layout-static">
            <div class="session-panel">
                <div class="session bg-primary">
                    <div class="session-content text-xs-center">
                        <div>
                            <div class="error-number">
                                <strong>404</strong>
                            </div>
                            <div class="m-x-1 m-y-1">
                                <h6 class="text-uppercase">
                                    <strong>Página no encontrada!</strong>
                                </h6>
                                <p>Lo sentimos, pero la página que estas tratando de ver no existe.</p>
                            </div>
                            <a href="<?= APP_SAC_URL; ?>" class="btn btn-secondary b-a-0">Ir a inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>