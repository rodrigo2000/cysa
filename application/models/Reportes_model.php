<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Reportes_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "r";
        $this->model_name = __CLASS__;
    }

    /**
     * Genera el archivo XLSX con las especificaciones proporcionadas
     * @param array $data Arreglo con la información de las auditorías
     * @param string $nombre_archivo Nombre que tendrá el archivo descargado
     * @param array $campos Arreglo con los nombres de los campos que se desean mostrar en el reporte
     * @return boolean Deveulve TRUE cuando el archivo se generó correctamente. FALSE en caso contrario.
     */
    function generar_reporte_xls($data, $nombre_archivo = NULL, $campos = NULL, $sheet_name = 'Hoja1') {
        $this->load->library("Excel");
        // Create new PHPExcel object
        $objPHPExcel = new Excel();
        $objPHPExcel->getProperties()->setCreator(APP_NAME);
        $objPHPExcel->getProperties()->setCategory('Reportes CYSA');

        $descripciones = array(
            'auditorias_id' => 'ID',
            'auditorias_origen_id' => 'ID Auditoria Origen',
            'auditorias_area' => 'Area de la auditoría',
            'auditorias_tipo' => 'Tipo de auditoría',
            'auditorias_numero' => 'Número de la auditoría',
            'auditorias_anio' => 'Año de la auditoría',
            'auditorias_is_programada' => '1=Indica que la auditoría pertenece al PAA, 0=Cualquier otro caso',
            'auditorias_segundo_periodo' => '1=Indica que la auditoría pertecene al segundo período del año',
            'auditorias_cc_id' => 'Identificador del centro de costos',
            'auditorias_periodos_id' => 'Identificador del período',
            'auditorias_direcciones_id' => 'Identificador de la Dirección',
            'auditorias_subdirecciones_id' => 'Identificador de la Subdirección',
            'auditorias_departamentos_id' => 'Identificador del Departamento',
            'clv_dir' => '',
            'clv_subdir' => '',
            'clv_depto' => '',
            'auditorias_rubro' => 'Rubro de la auditoría',
            'auditorias_objetivo' => 'Objetivo de la auditoría',
            'auditorias_alcance' => 'Alcance de la auditoría',
            'auditorias_auditor_lider' => 'Identificador del empleado que funge como auditior líder de la auditoría',
            'auditorias_status_id' => 'Status de la Auditoría',
            'auditorias_enlace_designado' => 'Identificador del empleado que funge como representante/enlace designado de la auditoría',
            'auditorias_is_sin_observaciones' => '1=Auditoría sin observaciones, 0=Cualquier otro caso',
            'auditorias_folio_oficio_representante_designado' => 'Folio del oficio donde se especifica el enlace designado',
            'auditorias_notificacion_correo_electronico' => '1=Indica que se ha notificado por correo electrónico a los involucrados en la auditoría'
        );

        // Creamos el estilo generico de los datos
        $styleArrayData = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN
//                )
//            ),
//            'font' => array(
//                'bold' => true,
//                'color' => array('rgb' => 'FF0000'),
//                'size' => 15,
//                'name' => 'Verdana'
//            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        );

        $titulos = array(
            'auditorias_id' => array('label' => 'ID'),
            'auditorias_origen_id' => array('label' => 'ID Origen'),
            'auditorias_area' => array('label' => 'ID Area'),
            'auditorias_tipo' => array('label' => 'ID Tipo'),
            'auditorias_numero' => array('label' => 'Número de auditoría', 'aligment_h' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'auditorias_anio' => array('label' => 'Año de auditoría'),
            'auditorias_is_programada' => array('label' => '¿PAA?'),
            'auditorias_segundo_periodo' => array('label' => '¿Segundo período?'),
            'auditorias_cc_id' => array('label' => 'ID CC'),
            'auditorias_periodos_id' => array('label' => 'ID Periodo'),
            'auditorias_direcciones_id' => array('label' => 'ID Dirección'),
            'auditorias_subdirecciones_id' => array('label' => 'ID Subdirección'),
            'auditorias_departamentos_id' => array('label' => 'ID Departamento'),
            'clv_dir' => array('label' => 'clv_dir'),
            'clv_subdir' => array('label' => 'clv_subdir'),
            'clv_depto' => array('label' => 'clv_depto'),
            'auditorias_rubro' => array('label' => 'Rubro', 'width' => 50, 'wraptext' => TRUE),
            'auditorias_objetivo' => array('label' => 'Objetivo'),
            'auditorias_alcance' => array('label' => 'Alcance'),
            'auditorias_auditor_lider' => array('label' => 'ID Auditor Líder'),
            'auditorias_status_id' => array('label' => 'ID Status'),
            'auditorias_status_nombre' => array('label' => 'Status Auditoría'),
            'auditorias_enlace_designado' => array('label' => 'ID Enlace Designado'),
            'auditorias_is_sin_observaciones' => array('label' => '¿Tuvo obseraciones?'),
            'auditorias_folio_oficio_representante_designado' => array('label' => 'Folio Oficio Repre. Designado'),
            'auditorias_notificacion_correo_electronico' => array('label' => 'Notificación correo electrónico'),
            'fecha_insert' => array('label' => 'Fecha Insert'),
            'fecha_update' => array('label' => 'Fecha Update'),
            'fecha_delete' => array('label' => 'Fecha Delete'),
            'numero_auditoria' => array('label' => 'Auditoría'),
            'auditorias_areas_siglas' => array('label' => 'Área (Siglas)'),
            'auditorias_areas_nombre' => array('label' => 'Area de auditoría'),
            'auditorias_tipos_nombre' => array('label' => 'Tipo de auditoría'),
            'auditorias_tipos_siglas' => array('label' => 'Tipo (Siglas)'),
            'auditorias_fechas_auditorias_id' => array('label' => 'ID', 'datatype' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER),
            'auditorias_fechas_inicio_programado' => array('label' => 'Fecha Inicio Programado', 'map_function' => 'phpDate2excelDate'),
            'auditorias_fechas_inicio_real' => array('label' => 'Fecha Inicio Real'),
            'auditorias_fechas_fin_programado' => array('label' => 'Fecha Fin Programado'),
            'auditorias_fechas_fin_real' => array('label' => 'Fecha Fin Real'),
            'auditorias_fechas_sello_orden_entrada' => array('label' => 'Fecha Sello OEA'),
            'auditorias_fechas_solicitud_informacion' => array('label' => ''),
            'auditorias_fechas_sello_oficio_representante_designado' => array('label' => 'Fecha Sello Oficio Repre. Designado'),
            'auditorias_fechas_acei' => array('label' => 'Fecha ACEI'),
            'auditorias_fechas_vobo_jefe' => array('label' => 'VoBo Jefe'),
            'auditorias_fechas_vobo_subdirector' => array('label' => 'VoBo Subdirector'),
            'auditorias_fechas_vobo_director' => array('label' => 'Vobo Director'),
            'auditorias_fechas_citatorio' => array('label' => 'Fecha Citatorio'),
            'auditorias_fechas_lectura' => array('label' => 'Fecha Lectura ARA'),
            'auditorias_fechas_oficio_envio_documentos' => array('label' => 'Fecha Oficio de Envío de Documento'),
            'auditorias_fechas_envio_evaluacion_general' => array('label' => ''),
            'auditorias_fechas_recibir_informacion_etapa_1' => array('label' => 'Fecha recepción información'),
            'auditorias_fechas_limite_recibir_informacion_etapa_1' => array('label' => 'Fecha límite recepción información'),
            'auditorias_fechas_inicio_programado_etapa_1' => array('label' => 'Fecha Inicio Programado Solventación'),
            'auditorias_fechas_inicio_real_etapa_1' => array('label' => 'Fecha Inicio Real Solventación'),
            'auditorias_fechas_fin_programado_etapa_1' => array('label' => 'Fecha Fin Programado Solventación'),
            'auditorias_fechas_fin_real_etapa_1' => array('label' => 'Fecha Fin Real Solventación'),
            'auditorias_fechas_acei_etapa_1' => array('label' => 'Fecha ACEI Solventación'),
            'auditorias_fechas_vobo_jefe_etapa_1' => array('label' => 'VoBo Jefe Solventación'),
            'auditorias_fechas_vobo_subdirector_etapa_1' => array('label' => 'VoBo Subdirector Solventación'),
            'auditorias_fechas_vobo_director_etapa_1' => array('label' => 'VoBo Director Solventación'),
            'auditorias_fechas_citatorio_etapa_1' => array('label' => 'Fecha Citatorio Solventación'),
            'auditorias_fechas_lectura_etapa_1' => array('label' => 'Fecha Lectura ARR'),
            'auditorias_fechas_oficio_envio_documentos_etapa_1' => array('label' => 'Fecha Envío Documentos ARR'),
            'auditorias_fechas_recibir_informacion_etapa_2' => array('label' => ''),
            'auditorias_fechas_limite_recibir_informacion_etapa_2' => array('label' => ''),
            'auditorias_fechas_inicio_programado_etapa_2' => array('label' => ''),
            'auditorias_fechas_inicio_real_etapa_2' => array('label' => ''),
            'auditorias_fechas_fin_programado_etapa_2' => array('label' => ''),
            'auditorias_fechas_fin_real_etapa_2' => array('label' => ''),
            'auditorias_fechas_acei_etapa_2' => array('label' => ''),
            'auditorias_fechas_vobo_jefe_etapa_2' => array('label' => ''),
            'auditorias_fechas_vobo_subdirector_etapa_2' => array('label' => ''),
            'auditorias_fechas_vobo_director_etapa_2' => array('label' => ''),
            'auditorias_fechas_citatorio_etapa_2' => array('label' => ''),
            'auditorias_fechas_lectura_etapa_2' => array('label' => ''),
            'auditorias_fechas_oficio_envio_documentos_etapa_2' => array('label' => ''),
            'auditorias_fechas_compromiso_observaciones' => array('label' => 'Fecha compromiso para solventar observaciones'),
            'empleados_id' => array('label' => 'ID Empleado'),
            'empleados_cc_id' => array('label' => 'ID Empleado CC'),
            'empleados_puestos_id' => array('label' => 'ID Empleado Puesto'),
            'empleados_puestos_nivel_id' => array('label' => 'ID Empleado Nivel'),
            'empleados_titulos_id' => array('label' => 'ID Empleado Titulo'),
            'empleados_subtitulos_id' => array('label' => 'ID Empleado Subdtítulo'),
            'empleados_numero_empleado' => array('label' => 'Número de empleado'),
            'empleados_nombre' => array('label' => 'Nombre del empleado'),
            'empleados_apellido_paterno' => array('label' => 'Apellido paterno del empleado'),
            'empleados_apellido_materno' => array('label' => 'Apellido materno del empleado'),
            'empleados_nombramiento' => array('label' => 'Nombramiento del empleado'),
            'empleados_correo_electronico' => array('label' => 'Correo electrónico del empleado'),
            'empleados_fecha_ingreso' => array('label' => 'Fecha de ingreso del empleado'),
            'empleados_fecha_baja' => array('label' => 'Fecha de baja del empleado'),
            'empleados_curp' => array('label' => 'CURP de empleado'),
            'empleados_credencial_elector_delante' => array('label' => 'INE Delante'),
            'empleados_credencial_elector_detras' => array('label' => 'INE Detrás'),
            'empleados_licencia_manejo' => array('label' => 'Lic. Manejor'),
            'empleados_identificacion_pasaporte' => array('label' => 'Pasaporte'),
            'empleados_identificacion_cedula_profesional' => array('label' => 'Cédula profesional'),
            'empleados_domicilio' => array('label' => 'Domicilio'),
            'empleados_localidad' => array('label' => 'Localidad'),
            'empleados_poblacion' => array('label' => 'Población'),
            'empleados_fecha_nacimiento' => array('label' => 'Fecha de nacimiento'),
            'empleados_genero' => array('label' => 'Género'),
            'auditor_lider_nombre_completo' => array('label' => 'Auditor Líder'),
            'puestos_nombre' => array('label' => 'Puesto del empleado'),
            'titulos_masculino_siglas' => array('label' => 'Título Académico (Siglas)'),
            'titulos_masculino_nombre' => array('label' => 'Título Académico'),
            'titulos_femenino_siglas' => array('label' => 'Título Académico (Siglas)'),
            'titulos_femenino_nombre' => array('label' => 'Título Académico'),
            'cc_id' => array('label' => 'ID CC'),
            'cc_periodos_id' => array('label' => 'ID Periodo'),
            'cc_direcciones_id' => array('label' => 'ID Dirección'),
            'cc_subdirecciones_id' => array('label' => 'ID Subdirección'),
            'cc_departamentos_id' => array('label' => 'ID Departamento'),
            'cc_empleados_id' => array('label' => 'ID Empleado Titular del CC'),
            'cc_etiqueta_direccion' => array('label' => 'Etiqueda CC Dirección'),
            'cc_etiqueta_subdireccion' => array('label' => 'Etiqueda CC Subdirección'),
            'cc_etiqueta_departamento' => array('label' => 'Etiqueda CC Departamento'),
            'direcciones_nombre' => array('label' => 'Dirección'),
            'direcciones_is_descentralizada' => array('label' => '¿Descentralizada?'),
            'direcciones_ubicacion' => array('label' => 'Ubicación'),
            'direcciones_tipos_ua_id' => array('label' => 'ID Tipo de UA'),
            'tipos_ua_nombre' => array('label' => 'Tipo de UA'),
            'tipos_ua_genero' => array('label' => 'Género de UA'),
            'subdirecciones_nombre' => array('label' => 'Subdirección'),
            'departamentos_nombre' => array('label' => 'Departamento'),
        );

        // DATA
        $countSheet = 0;
        $fila = 1;
        // Creamos la fila de encabezados
        $encabezados = array(); // Arreglo donde se almacenará las etiquetas que irán en los encabezados
        $indices = array(); // Arreglo que contiene los índices de los campos que se requieren visualizar en el reporte
        if (!empty($campos)) {
            foreach ($campos as $key => $c) {
                if (is_numeric($key)) {
                    if (array_key_exists($c, $titulos)) {
                        array_push($encabezados, $titulos[$c]['label']);
                        $indices[$c] = 1;
                    }
                } else {
                    if (array_key_exists($key, $titulos)) {
                        $indices[$key] = 1;
                        if (is_array($c) && isset($c['label'])) {
                            array_push($encabezados, $c['label']);
                        } else {
                            array_push($encabezados, $titulos[$key]['label']);
                        }
                    }
                }
            }
        }
        // Si existen índices significa que son campos personalizados, entonces solo se deben mostrar esos campos.
        if (!empty($indices)) {
            $aux = array();
            foreach ($data as $d) {
                $aux2 = array();
                foreach ($indices as $key => $i) {
                    if (isset($titulos[$key]['map_function'])) {
                        $d[$key] = call_user_func($titulos[$key]['map_function'], $d[$key]);
                    } elseif (stripos($key, 'fecha') !== FALSE) {
                        $d[$key] = phpDate2excelDate($d[$key]);
                    }
                    $aux2[$key] = $d[$key];
                }
                array_push($aux, $aux2);
            }
            $data = $aux;
        }
        $sheet = $objPHPExcel->getActiveSheet();
        if (!empty($sheet_name)) {
            $sheet->setTitle($sheet_name);
        }
        $sheet->fromArray($encabezados, NULL, 'A' . $fila++);
        // Agregamos los datos
        foreach ($data as $index => $d) {
            // Agregamos la fila con los datos
            $sheet->fromArray($d, NULL, 'A' . $fila++);
        }

        // Aplicamos el estilo generíco de los datos
        $ultima_columna = $sheet->getHighestColumn(1);
        $sheet->getStyle('A2:' . $ultima_columna . $fila++)->applyFromArray($styleArrayData);

        // Aplicamos el tipo de dato a los datos de la columna
        $i = 0;
        foreach ($indices as $key => $c) {
            $letra = chr($i + 65);
            // Agregamos las descripciones
            if (isset($descripciones[$key]) && !empty($descripciones[$key])) {
                $sheet->getComment($letra . '1')->getText()->createTextRun($descripciones[$key]);
            }

            // 
            if (isset($titulos[$key]['datatype'])) {
                $sheet->getStyle($letra . "2:" . $letra . $fila)->getNumberFormat()->setFormatCode($titulos[$key]['datatype']);
            } else {
                if (stripos($key, 'fecha') !== FALSE) {
                    $sheet->getStyle($letra . "2:" . $letra . $fila)->getNumberFormat()->setFormatCode('[$-es-MX]d" de "mmmm" de "yyyy');
                }
            }
            if (isset($titulos[$key]['width'])) {
                $sheet->getColumnDimension($letra)->setWidth($titulos[$key]['width']);
            } elseif (stripos($key, 'fecha') !== FALSE) {
                $sheet->getColumnDimension($letra)->setWidth(25); // 25pt de forma predeterminada
            } else {
                $sheet->getColumnDimension($letra)->setAutoSize(TRUE);
            }
            if (isset($titulos[$key]['wraptext']) && $titulos[$key]['wraptext'] === TRUE) {
                $sheet->getStyle($letra . '2:' . $letra . $fila)->getAlignment()->setWrapText(TRUE);
            }
            $i++;
        }

        // Aplicamos los comentarios
        // Aplicamos formato a los encabezados
        $styleArrayHeaders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
            //'color' => array('rgb' => 'FF0000'),
            //'size' => 15,
            //'name' => 'Verdana'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        );
        $ultima_columna = $sheet->getHighestColumn(1);
        $sheet->getStyle('A1:' . $ultima_columna . '1')->applyFromArray($styleArrayHeaders);

        // Creamos el archivo
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
        header('Cache-Control: max-age=0');
        if ($this->input->server("REQUEST_METHOD") === 'GET') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        } else {
            $path = "archivos/";
            $archivo = $nombre_archivo;
            if(!is_dir($path)){
                mkdir($path);
            }
            $objWriter->save($path . $archivo);
            return array('success' => TRUE, 'archivo' => $archivo);
        }
        return TRUE;
    }

    function generar_reporte_auditorias_en_proceso() {
        $anios = NULL;
        $status = AUDITORIAS_STATUS_EN_PROCESO;
        $areas = NULL;
        $auditorias = $this->Auditorias_model->get_auditorias($anios, $status, $areas);
        return $auditorias;
    }

    function generar_reporte_paa($anio) {
        $anios = $anio;
        $status = NULL;
        $areas = NULL;
        $auditorias = $this->Auditorias_model->get_auditorias($anios, $status, $areas);
        return $auditorias;
    }

    function generar_reporte_fechas_autorizacion($anio, $auditorias_status_id) {
        $anios = $anio;
        $status = $auditorias_status_id;
        $areas = NULL;
        $auditorias = $this->Auditorias_model->get_auditorias($anios, $status, $areas);
        return $auditorias;
    }

}
