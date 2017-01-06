<form action="<?= $urlActionDelete; ?>" method="post">
    <div class="jumbotron">
        <h1><?= isset($etiqueta) && trim($etiqueta) != "" ? $etiqueta : '¿Esta seguro que desea eliminar este elemento?'; ?></h1>
        <p>&nbsp;</p>
        <p align="center" class="text-danger">Nota: Esta acción no se puede deshacer.</p>
        <p>&nbsp;</p>
        <div class="row col-xs-12 center-block">
            <div class="col-xs-6 center-block">
                <a type="submit" href="<?= $urlActionCancel; ?>" class="btn btn-danger btn-lg btn-block">No</a>
            </div>
            <div class="col-xs-6 center-block centered">
                <button type="submit" class="btn btn-success btn-lg btn-block">Sí</button>
            </div>
        </div>
        <br>
        <input type="hidden" name="id" value="<?= $id; ?>">
    </div>
</form>