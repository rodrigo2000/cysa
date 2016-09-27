<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped dataTable">
                <thead class="thead-inverse">
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Área</th>
                        <th>Auditoría(s)</th>
                        <th>Descripción PNC</th>
                        <th>Estado del PNC</th>
                        <th>Registra el PNC</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var dataTableOrder = "asc";
    var dataTableFieldOrder = 0;
    var dataTableOrderTargets = [-1];
</script>