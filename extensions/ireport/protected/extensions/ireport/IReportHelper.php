<?php

class IReportHelper {

    public static function time_to_sec($time) {
        $hours = substr($time, 0, -6);
        $minutes = substr($time, -5, 2);
        $seconds = substr($time, -2);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    public static function sec_to_time($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60);
        $seconds = $seconds % 60;

        return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public static function formatText($txt, $pattern) {
        if (is_double($txt)) {
            if ($pattern == "###0")
                return number_format($txt, 0, "", "");
            elseif ($pattern == "#,##0")
                return number_format($txt, 0, ".", ",");
            elseif ($pattern == "###0.0")
                return number_format($txt, 1, ".", "");
            elseif ($pattern == "#,##0.0")
                return number_format($txt, 1, ".", ",");
            elseif ($pattern == "###0.00")
                return number_format($txt, 2, ".", "");
            elseif ($pattern == "#,##0.00")
                return number_format($txt, 2, ".", ",");
            elseif ($pattern == "###0.000")
                return number_format($txt, 3, ".", "");
            elseif ($pattern == "#,##0.000")
                return number_format($txt, 3, ".", ",");
            elseif ($pattern == "#,##0.0000")
                return number_format($txt, 4, ".", ",");
            elseif ($pattern == "###0.0000")
                return number_format($txt, 4, ".", "");
        }elseif ($pattern == "dd/MM/yyyy" && $txt != "")
            return date("d/m/Y", strtotime($txt));
        elseif ($pattern == "MM/dd/yyyy" && $txt != "")
            return date("m/d/Y", strtotime($txt));
        elseif ($pattern == "yyyy/MM/dd" && $txt != "")
            return date("Y/m/d", strtotime($txt));
        elseif ($pattern == "dd-MMM-yy" && $txt != "")
            return date("d-M-Y", strtotime($txt));
        elseif ($pattern == "dd-MMM-yy" && $txt != "")
            return date("d-M-Y", strtotime($txt));
        elseif ($pattern == "dd/MM/yyyy h.mm a" && $txt != "")
            return date("d/m/Y h:i a", strtotime($txt));
        elseif ($pattern == "dd/MM/yyyy HH.mm.ss" && $txt != "")
            return date("d-m-Y H:i:s", strtotime($txt));
        else
            return $txt;
    }

    public static function hex_code_color($value) {
        $r = hexdec(substr($value, 1, 2));
        $g = hexdec(substr($value, 3, 2));
        $b = hexdec(substr($value, 5, 2));
        return array("r" => $r, "g" => $g, "b" => $b);
    }

    public static function get_first_value($value) {
        return (substr($value, 0, 1));
    }

    public static function right($value, $count) {

        return substr($value, ($count * -1));
    }

    public static function left($string, $count) {
        return substr($string, 0, $count);
    }

}

?>
