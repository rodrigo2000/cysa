<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . "/third_party/fpdf/fpdf.php";

class PDF_MC_Table extends FPDF {

    var $widths;
    var $aligns;
    var $hideFooter = FALSE;
    var $hideHeader = FALSE;

    const DPI = 300;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const LETTER_HEIGHT = 279;
    const LETTER_WIDTH = 215;
    const OFFICE_HEIGHT = 0;
    const OFFICE_WIDTH = 0;
    // tweak these values (in pixels)
    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 500;

    public function __construct() {
        parent::__construct();
        $this->SetAuthor("Unidad de Contraloría", TRUE);
    }

    function Header() {
        if (!$this->hideHeader) {
            $this->centrar_imagen(APP_SAC_URL . "resources/images/logo-icon.png");
            $this->Ln(3);
        }
    }

    function Footer() {
        if (!$this->hideFooter) {
            $this->SetY(-45);
            $this->Image(APP_SAC_URL . "resources/images/reportes_pie_pagina.png", 15, NULL, 200);
            $this->SetY(-25);
            $this->SetFont('Arial', '', 8);
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
        }
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data, $valign = "M", $border = "0", $textHeight = 5) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $textHeight * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            if ($border == "0") {
                //Draw the border
                $this->Rect($x, $y, $w, $h);
            }

            switch ($valign) {
                case "M": // M = Middle
                    // Calculamos el alineamiento MIDDLE vertidal
                    // Obtengo el numero de lineas que tendra la celda con respecto al ancho de la celda
                    $nbDelTexto = $this->NbLines($this->widths[$i], $data[$i]);
                    // IF    el numero de lineas es mayor a 1   AND    numeros de lineas del texto menos que el numero de lineas maximo THEN
                    if ($nb > 1 && $nbDelTexto < $nb) {
                        // Calculo la nueva posicion vertical   PosYActual + Alto
                        $yy = $y + (($h / ($nbDelTexto + 1)) - 2);
                        // Asigno la posicion dentro de la celda
                        $this->SetXY($x, $yy);
                    } break;
                case "B": // B = Bottom
                    // Calculo para alineamiento BOTTOM vertical
                    // Obtengo el numero de lineas que tendra la celda con respecto al ancho de la celda
                    $nbDelTexto = $this->NbLines($this->widths[$i], $data[$i]);
                    // IF    el numero de lineas es mayor a 1   AND    numeros de lineas del texto menos que el numero de lineas maximo THEN
                    if ($nb > 1 && $nbDelTexto < $nb) {
                        // Calculo la nueva posicion vertical   PosYActual + Alto del rectangulo - (numero de lineas del texto MULTIPLICADO POR LA CONSTANTE 5 [asi se multiplico el $h])
                        $yy = $y + $h - ($nbDelTexto * 5);
                        // Asigno la posicion dentro de la celda
                        $this->SetXY($x, $yy);
                    }
                // Es TOP
                default : break;
            }

            //Print the text
            $this->MultiCell($w, $textHeight, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI / 2;
    }

    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    function pixel2mm($px) {
        return $px / 2.02;
    }

    function centrar_imagen($img) {
        list($width, $height) = $this->resizeToFit($img);
        // you will probably want to swap the width/height
        // around depending on the page's orientation
        list($anchoPagina, $altoPagina) = $this->_getpagesize($this->CurPageSize);
        $this->Image($img, ($anchoPagina - $width) / 2);
    }

    public function floatingImage($imgPath, $height) {
        // https://stackoverflow.com/questions/22358578/aligning-image-next-to-text-in-fpdf
        list($w, $h) = getimagesize($imgPath);
        $ratio = $w / $h;
        $imgWidth = $height * $ratio;
        $this->Image($imgPath, $this->GetX(), $this->GetY());
        $this->x += $imgWidth;
    }

    public function pageWidth() {
        $width = $this->w;
        $leftMargin = $this->lMargin;
        $rightMargin = $this->rMargin;
        return array($width, $width - $rightMargin - $leftMargin);
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $indent = 0) {
        //Output text with automatic or explicit line breaks
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;

        $wFirst = $w - $indent;
        $wOther = $w;

        $wmaxFirst = ($wFirst - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $wmaxOther = ($wOther - 2 * $this->cMargin) * 1000 / $this->FontSize;

        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L')))
                    $b2 .= 'L';
                if (is_int(strpos($border, 'R')))
                    $b2 .= 'R';
                $b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        $first = true;
        while ($i < $nb) {
            //Get next character
            $c = $s[$i];
            if ($c == "\n") {
                //Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];

            if ($first) {
                $wmax = $wmaxFirst;
                $w = $wFirst;
            } else {
                $wmax = $wmaxOther;
                $w = $wOther;
            }

            if ($l > $wmax) {
                //Automatic line break
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    $SaveX = $this->x;
                    if ($first && $indent > 0) {
                        $this->SetX($this->x + $indent);
                        $first = false;
                    }
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                    $this->SetX($SaveX);
                } else {
                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                        $this->_out(sprintf('%.3f Tw', $this->ws * $this->k));
                    }
                    $SaveX = $this->x;
                    if ($first && $indent > 0) {
                        $this->SetX($this->x + $indent);
                        $first = false;
                    }
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    $this->SetX($SaveX);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
            } else
                $i++;
        }
        //Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        if ($border && is_int(strpos($border, 'B')))
            $b .= 'B';
        $this->Cell($w, $h, substr($s, $j, $i), $b, 2, $align, $fill);
        $this->x = $this->lMargin;
    }

}

?>
