<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . "/third_party/fpdf/fpdf.php";

class PDF_MC_Table extends FPDF {

    var $widths;
    var $aligns;

    public function __construct() {
        parent::__construct();
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data, $valign = "M", $border = "0") {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
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
            $this->MultiCell($w, 5, $data[$i], $border, $a);
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
            $l+=$cw[$c];
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

}

?>
