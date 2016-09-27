<?php

function validaXML($cadena_xml = "", $validaSAT = true) {
    $r = array(
        'success' => true,
        'numCtaPago' => "",
        'error' => '',
        'xml' => ''
    );

    if (trim($cadena_xml) == "") {
        $r['success'] = false;
        $r['error'] = 'XML vacio';
    }

    $xml = new DOMDocument();

//    $ctx = stream_context_create([
//        'ssl' => [
//            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
//        ],
//    ]);


    $archivo_esquema = 'res/xml/cfdv32.xsd';
    $esquema = file_get_contents($archivo_esquema, false);


// Validamos solamente la estructura del XML para ver si cumple con la estructura del SAT
    if ($cadena_xml !== false) {
        $datos = array("rfc_emisor" => "", "rfc_receptor" => "", "total" => "", "uuid" => "");
        $xml->loadXML($cadena_xml);
        // establecer el gestor de errores definido por el usuario
        $gestor_errores_antiguo = set_error_handler("miGestorDeErrores");
        $error = $xml->schemaValidateSource($esquema);
        //$error = true;
        // Reestablecemos el gestor de errores definido por el sistema
        restore_error_handler();
        if ($error == false || is_string($error) || (is_array($error) && $error['success'] == false)) {
            $r['success'] = false;
            $msg = is_array($error) || is_string($error) ? "" . $error['errstr'] : "El archivo XML no cumple con la estructura del CFDI del SAT.";
            $r['error'] = "XML con formato incorrecto.<br><br>" . $msg;
        } else {
            $array = XML2Array::createArray($cadena_xml);
            $r['xml'] = $array;
            $error = array();
            if (isset($array) && $array != false && count($array) > 0) {
                if (!valid_rfc($array['cfdi:Comprobante']['cfdi:Emisor']['@attributes']['rfc'])) {
                    $error[] = "RFC del emisor invalido";
                    $r['success'] = false;
                } else {
                    $r['rfc_emisor'] = $datos['rfc_emisor'] = $array['cfdi:Comprobante']['cfdi:Emisor']['@attributes']['rfc'];
                }
                if (!valid_rfc($array['cfdi:Comprobante']['cfdi:Receptor']['@attributes']['rfc'])) {
                    $error[] = "RFC del receptor inv�lido";
                    $r['success'] = false;
                } else {
                    $r['rfc_receptor'] = $datos['rfc_receptor'] = $array['cfdi:Comprobante']['cfdi:Receptor']['@attributes']['rfc'];
                }

                if (isset($array['cfdi:Comprobante']['@attributes']['NumCtaPago']) && $array['cfdi:Comprobante']['@attributes']['NumCtaPago']) {
                    $r['numCtaPago'] = $array['cfdi:Comprobante']['@attributes']['NumCtaPago'];
                }

                if (isset($array['cfdi:Comprobante']['@attributes']['total']) && $array['cfdi:Comprobante']['@attributes']['total']) {
                    $datos['total'] = $array['cfdi:Comprobante']['@attributes']['total'];
                } else {
                    //$datos['total'] = 0;
                    $error[] = "La factura no tiene importe de TOTAL";
                    $r['success'] = false;
                }
                $datos['uuid'] = $array['cfdi:Comprobante']['cfdi:Complemento']['tfd:TimbreFiscalDigital']['@attributes']['UUID'];
                if (isset($array['cfdi:Comprobante']['cfdi:Complemento']['tfd:TimbreFiscalDigital']['@attributes']['UUID']) && $array['cfdi:Comprobante']['cfdi:Complemento']['tfd:TimbreFiscalDigital']['@attributes']['UUID']) {
                    $datos['uuid'] = $array['cfdi:Comprobante']['cfdi:Complemento']['tfd:TimbreFiscalDigital']['@attributes']['UUID'];
                } else {
                    $error[] = "La factura no tiene UUID";
                    $r['success'] = false;
                }

                if ($r['success'] && $validaSAT === true) {
                    //$t = json_encode(array('success' => true, 'mensaje' => 'Validando con el SAT'));
                    $sat = validarXMLenSAT($datos);
                    if (substr($sat['CodigoEstatus'], 0, 1) == "N") {
                        $error[] = "El SAT reporta: " . $sat['CodigoEstatus'] . "<br>Estado: " . $sat['Estado'];
                        $r['success'] = false;
                    }
                }
                $r['error'] = "";
                if (count($error) > 0) {
                    $r['error'] = implode("<br><br><br>", $error);
                }
            }
        }
    }
    return $r;
}

// función de gestión de errores
function miGestorDeErrores($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // Este código de error no está incluido en error_reporting
        return;
    }
    $tt = array(
        'success' => false,
        'errno' => $errno,
        'errstr' => $errstr,
        'errfile' => $errfile,
        'errline' => $errline
    );

//    switch ($errno) {
//        case E_USER_ERROR:
//            $tt['success'] = false;
//            echo "<b>Mi ERROR</b> [$errno] $errstr<br />\n";
//            echo "  Error fatal en la línea $errline en el archivo $errfile";
//            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
//            echo "Abortando...<br />\n";
//            exit(1);
//            break;
//
//        case E_USER_WARNING:
//            $tt['success'] = false;
//            echo "<b>Mi WARNING</b> [$errno] $errstr<br />\n";
//            break;
//
//        case E_USER_NOTICE:
//            $tt['success'] = false;
//            echo "<b>Mi NOTICE</b> [$errno] $errstr<br />\n";
//            break;
//
//        default:
//            echo "Tipo de error desconocido: [$errno] $errstr<br />\n";
//            $tt['success'] = false;
//            break;
//    }

    /* No ejecutar el gestor de errores interno de PHP */
    return $tt;      // antes return TRUE;
}

// Validar SELLO en el servidor del SAT
function validarXMLenSAT($data) {
    error_reporting(E_ALL ^ E_WARNING);
    CI()->load->library('nuSoap');
    $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";

    $soapclient = new nusoap_client($url, $esWSDL = true);
    $soapclient->soap_defencoding = 'UTF-8';
    $soapclient->decode_utf8 = false;

    $rfc_emisor = $data['rfc_emisor'];
    $rfc_receptor = $data['rfc_receptor'];
    $impo = floatval($data['total']);
    //$impo = sprintf("%.6f", $impo);
    //$impo = str_pad($impo, 17, "0", STR_PAD_LEFT);
    $uuid = strtoupper($data['uuid']);
    $factura = "?re=" . $rfc_emisor . "&rr=" . $rfc_receptor . "&tt=" . $impo . "&id=" . $uuid;
    $prm = array('expresionImpresa' => $factura);
    $buscar = $soapclient->call('Consulta', $prm);
    $buscar['ConsultaResult']['cadena'] = $factura;

    //echo "<h3>El portal del SAT reporta</h3>";
    //echo "QR: $factura<br>";
    //echo "<pre>"; 
    //echo htmlspecialchars($soapclient->getdebug()); 
    //echo "<br>"; 
    //echo htmlspecialchars($soapclient->request); 
    //echo "<br>"; 
    //echo "</pre>"; 
    //echo "El codigo: " . $buscar['ConsultaResult']['CodigoEstatus'] . "<br>";
    //echo "El estado: " . $buscar['ConsultaResult']['Estado'] . "<br>";
    return $buscar['ConsultaResult'];
}

?>