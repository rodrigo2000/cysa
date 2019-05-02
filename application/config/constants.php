<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Constantes de la APP
define('APP_NAME', "Sistema de Control y Seguimiento de Auditorías");
define('APP_ABREVIACION', 'CYSA');
define('APP_NAMESPACE', "cysa");

include_once('../constantes.php');

// Constantes de sitio web
define('HTMLVARS_TITLE_PAGE', 'Contraloría :: ' . APP_NAME);

// Puestos de trabajo
define("PUESTO_COORDINADOR_AUDITORIA", 269);
define("PUESTO_JEFE_DEPARTAMENTO", 59);
define("PUESTO_SUBDIRECTOR", 106);
define("PUESTO_DIRECTOR", 45);
define('PUESTO_NINGUNO', 0);
define('PUESTO_AUXILIAR_ADMINISTRATIVO', 8);
define('PUESTO_AUXILIAR_DE_AUDITORIA', 13);
define('PUESTO_SUPERVISOR', 107);
define('PUESTO_SECRETARIA', 119);
define('PUESTO_INGENIERO_EN_SOFTWARE', 253);
define('PUESTO_AUDITOR', 7);
define('PUESTO_COORDINADOR', 40);
define('PUESTO_SUBDIRECTOR_INACTIVO', 1);
define('PUESTO_PRESIDENTE_MUNICIPAL', 85);
define('PUESTO_SINDICO', 256);

// Dependencias
define('DEPENDENCIA_CONTRALORIA_MUNICIPAL', 5);

// Áreas
define('AREA_DIRECCION', 1);
define('AREA_APOYOS', 2);
define('AREA_AUDITORIA_INTERNA', 3);

define('USUARIO_PERFIL_ADMNISTRADOR', 1);
define('USUARIO_PERFIL_EMPLEADO_CONTRALORIA', 2);
define('USUARIO_PERFIL_RESGUARDANTE_BODEGA', 3);
define('USUARIO_PERFIL_SERVICIO_SOCIAL', 4);
define('USUARIO_PERFIL_EXTERNO', 5);

define('AUDITORIAS_STATUS_CANCELADA', 0);
define('AUDITORIAS_STATUS_EN_PROCESO', 1);
define('AUDITORIAS_STATUS_FINALIZADA', 2);
define('AUDITORIAS_STATUS_FINALIZADA_RESERVADA', 3);
define('AUDITORIAS_STATUS_FINALIZADA_MANUAL', 4);
define('AUDITORIAS_STATUS_FINALIZADA_REPROGRAMADA', 5);
define('AUDITORIAS_STATUS_FINALIZADA_SUSTITUIDA', 6);

define('GENERO_MASCULINO', 1);
define('GENERO_FEMENINO', 0);

define('CONTRALORIA_TITULAR_EMPLEADOS_NOMBRE', 'María Gómez Nechar');
define('CONTRALORÍA_TITULAR_EMPLEADOS_ID', 17000);
define('CONTRALORIA_TITULAR_NOMBRAMIENTO_CABILDO', 'Titular de la Contraloría Municipal');

define('TIPO_DOCUMENTO_AMPLIACION', 1);
define('TIPO_DOCUMENTO_REPROGRAMACION', 2);
define('TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA', 3);
define('TIPO_DOCUMENTO_RESOLUCION_PRORROGA', 4);
define('TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION', 5);
define('TIPO_DOCUMENTO_ACTA_RESULTADOS_INTERVENSION_CONTROL', 6);
define('TIPO_DOCUMENTO_ACTA_RESULTADOS_MONITOREO', 7);
define('TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION_MONITOREO', 8);
define('TIPO_DOCUMENTO_SOLICITUD_INFORMACION', 9);
define('TIPO_DOCUMENTO_ORDEN_AUDITORIA', 10);
define('TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION', 11);
define('TIPO_DOCUMENTO_CITATORIO', 12);
define('TIPO_DOCUMENTO_ENVIO_DOCUMENTOS', 18);
define('TIPO_DOCUMENTO_ACTA_ADMINISTRATIVA', 21);
define('TIPO_DOCUMENTO_ACTA_INICIO_AUDITORIA', 29);
define('TIPO_DOCUMENTO_RESOLUCION_AMPLIACION_PLAZO', 31);

define('TIPO_ASISTENCIA_ELIMINADO', 0);
define('TIPO_ASISTENCIA_RESPONSABLE', 1);
define('TIPO_ASISTENCIA_TESTIGO', 2);
define('TIPO_ASISTENCIA_INVOLUCRADO', 3);
define('TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA', 4);

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
