<?php

function valid_money($str) {
    return (!preg_match('/^(\-?[0-9,\.])+$/i', $str)) ? FALSE : TRUE;
}

function valid_password($str) {
    return (preg_match('/((?=.*\\d)(?=.*[A-Z])(?=.*[\@\#\$\%\^\&\*\(\)\_\+\|\~\-\=\\\`\{\}\[\]\:\"\;\'\<\>]))/u', $str)) ? TRUE : FALSE;
}

function valid_rfc_fisica($str) {
    $str = strtoupper($str);
    if (in_array($str, array('XAXX010101000', 'XEXX010101000'))) {
        return true;
    }
    $result = preg_match('/^[A-ZÑ&]{4}([0-9]{2})([0-1][0-9])([0-3][0-9])[A-Z0-9][A-Z0-9][0-9A]$/u', $str, $matches);
    if (!$result) {
        return false;
    }
    if (intval($matches[1]) <= 12) {
        $matches[1] = 2000 + (int) $matches[1];
    } else {
        $matches[1] = 1900 + (int) $matches[1];
    }
    date_default_timezone_set ( 'America/Merida' );
    return strtotime($matches[1] . '-' . $matches[2] . '-' . $matches[3]) ? true : false;
}

function valid_rfc_moral($str) {
    $str = strtoupper($str);
    if (in_array($str, array('XAXX010101000', 'XEXX010101000'))) {
        return true;
    }
    $result = preg_match('/^[A-ZÑ&]{3}([0-9]{2})([0-1][0-9])([0-3][0-9])[A-Z0-9][A-Z0-9][0-9A]$/u', $str, $matches);
    if (!$result) {
        return false;
    }
    if ((int) $matches[1] <= 12) {
        $matches[1] = 2000 + (int) $matches[1];
    } else {
        $matches[1] = 1900 + (int) $matches[1];
    }
    date_default_timezone_set ( 'America/Merida' );
    return strtotime($matches[1] . '-' . $matches[2] . '-' . $matches[3]) ? true : false;
}

function valid_rfc($str) {
    return valid_rfc_fisica($str) || valid_rfc_moral($str);
}

function valid_password_hide($str) {
    return valid_password($str);
}

function valid_curp($str) {
    return (preg_match('/^[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]$/u', $str)) ? TRUE : FALSE;
}

function valid_yyyymmdd($str) {
    return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $str) ? true : false;
}

function valid_ddmmyyyy($str) {
    return preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $str) ? true : false;
}

function greater_than($str, $val) {
    return $str > $val ? true : false;
}

function lesser_than($str, $val) {
    return $str < $val ? true : false;
}

function validate_config($config) {
    if (sizeof($config) > 0) {
        set_rules($config);
        if ($this->run() == false) {
            $errors = array();
            foreach ($this->_error_array as $index => $value) {
                $errors[] = array('field' => $index, 'message' => $value);
            }
            return array(
                'success' => false,
                'message' => false,
                'data' => $errors
            );
        }
    }
    return array(
        'success' => true,
        'message' => false,
        'data' => array()
    );
}

?>