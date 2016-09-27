<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <div>
            <form id="myForm" method="post" action="<?= $urlAction; ?>" novalidate="novalidate">
                <div class="form-group row">
                    <label for="tipo_servicio" class="col-sm-2 form-control-label">Tipo de préstamo</label>
                    <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> Externo
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> Interno
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="servicio" class="col-sm-2 form-control-label">Entrega</label>
                    <div class="col-sm-10">
                        <select id="servicio" name="servicio" class="form-control">
                            <option value="0">SELECCIONE</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-sm-2 form-control-label">Fecha de devolución</label>
                    <div class="col-sm-10">
                        <input type="text" name="fecha_crecion" class="form-control" value="<?= ahora(); ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-sm-2 form-control-label">Motivo</label>
                    <div class="col-sm-10">
                        <input type="text" name="fecha_crecion" class="form-control" placeholder="Razón por la cual se realiza el préstamo">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-sm-2" for="clientes_direcciones_id">Lista de equipos</label>
                    <div id="listaEquipos" class="col-sm-10">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-offset-2 col-sm-10">
                        <a href="#" id="agregarEquipo" class="btn btn-success btn-sm m-t-5 pull-right"><i></i>Agregar equipo</a>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>
<div id="template" class="card hidden-xs-up">
    <div class="card-header">Equipo 1
        <div class="card-controls">
            <a href="javascript:;" class="card-remove" data-toggle="card-remove"></a>
        </div>
    </div>
    <div class="card-block">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">Cantidad</span>
                <input class="form-control" id="clientes_direcciones_id" name="direcciones_calle" type="text" value="<?= isset($r['direcciones_calle']) ? $r['direcciones_calle'] : ''; ?>" maxlength="30">
                <?= form_error('direcciones_calle'); ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">No de equipo</span>
                <input class="form-control" id="direcciones_municipio" name="direcciones_municipio" type="text" value="<?= isset($r['direcciones_municipio']) ? $r['direcciones_municipio'] : ''; ?>" maxlength="50">
                <?= form_error('direcciones_municipio'); ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">Equipo</span>
                <input class="form-control" id="direcciones_municipio" name="direcciones_municipio" type="text" value="<?= isset($r['direcciones_municipio']) ? $r['direcciones_municipio'] : ''; ?>" maxlength="50">
                <?= form_error('direcciones_municipio'); ?>
            </div>
        </div>
        &nbsp;
    </div>
</div>
<script>
    $(document).ready(function () {

        $("#listaEquipos").on("click", "a.card-remove",function () {
            $(this).parents(".card").get(0).remove();
        });

        $("#agregarEquipo").on("click", function () {
            obj = $("#template").clone();
            $(obj).removeClass("hidden-xs-up")
            $("div#listaEquipos").append(obj);
        });
    });
</script>