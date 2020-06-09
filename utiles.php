<?php
/*
 * Atech utiles 2015
 */

if (!function_exists('vista_con_objetos')) {

    /**
     * Devuelve una vista con los objetos pasados como argumentos
     * Los nombres de objetos pasan a ser el nombre del objeto
     *
     * @todo Si hay dos objetos iguales, llamar con un 2/3/4 a los siguientes
     * @param  string  $vista
     * @param  objects[]   $objetos Admite nombres personalizados
     * @return \Illuminate\View\View
     */
    function vista_con_objetos($vista, $objetos = [])
    {
        $vista = view($vista);

        foreach ($objetos as $key => $objeto) {
            if (!is_int($key)) $vista->with($key, $objeto);
            else if (is_object($objeto)) $vista->with(camel_case(class_basename($objeto)), $objeto);
        }

        return $vista;
    }
}

if (!function_exists('castear')) {

    /**
     * Castea a tipo PHP.
     *
     * @param string $tipo
     * @param mixed $valor
     * @return mixed
     */
    function castear($tipo, $valor)
    {
        if (is_null($valor)) {
            return $valor;
        }

        switch ($tipo) {
            case 'int':
            case 'integer':
                return (int) $valor;
            case 'real':
            case 'float':
            case 'double':
                return (float) $valor;
            case 'string':
                return (string) $valor;
            case 'bool':
            case 'boolean':
                return (bool) $valor;
            case 'object':
                return json_decode($valor);
            case 'array':
            case 'json':
                return json_decode($valor, true);
            case 'collection':
                return collect(json_decode($valor, true));
            default:
                return $valor;
        }
    }
}

if (!function_exists('nl2p')) {

    /**
     * Convierte a párrafos
     * @param string $string
     * @param bool $nl2br Si se desactiva no tomará en cuenta saltos de 1 linea.
     * @return string
     * @link https://github.com/davejamesmiller/djmutil/blob/master/smarty/modifier.nl2p.php
     */
    function nl2p($string, $nl2br = true)
    {
        // Normalise new lines
        $string = str_replace(array("\r\n", "\r"), "\n", $string);

        // Extract paragraphs
        $parts = explode("\n\n", $string);

        // Put them back together again
        $string = '';

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part) {
                if ($nl2br) {
                    // Convert single new lines to <br />
                    $part = nl2br($part);
                }
                $string .= "<p>$part</p>\n\n";
            }
        }

        return $string;
    }
}

if (!function_exists('floatval_from_locale')) {

    /**
     * Devuelve los números en el idioma por defecto
     * @param string $number
     * @link http://stackoverflow.com/a/437642/2389232
     * @return mixed
     */
    function floatval_from_locale($number)
    {
        $locale    = localeconv();
        $sin_miles = str_replace($locale['thousands_sep'], '', (string) $number);
        return floatval(str_replace($locale['decimal_point'], '.', $sin_miles));
    }
}

if (!function_exists('number_format_locale')) {

    /**
     * Devuelve los números en el idioma por defecto
     * @param mixed $number
     * @param int $decimals
     * @link http://stackoverflow.com/a/437642/2389232
     * @return mixed
     */
    function number_format_locale($number, $decimals = 2)
    {
        $locale = localeconv();
        return number_format($number, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
    }
}

if (!function_exists('money_format')) {

    /**
     * Mapeo a numer_format_locale cuando no existe esta función en windows
     */
    function money_format($string, $numero)
    {
        return number_format_locale($numero);
    }
}

if (!function_exists('hex2rgb')) {

    /**
     * Convierte de hex a RGB
     * @param string $hex
     * @param bool $implode Si está activado devuelve un implode separado por comas
     * @return array|string
     */
    function hex2rgb($hex, $implode = false)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        $rgb = array($r, $g, $b);

        return $implode ? implode(',', $rgb) : $rgb;
    }
}

if (!function_exists('rgb2hex')) {

    /**
     * Convierte de RGB a hex
     * @param mixed $rgb
     * @return string
     */
    function rgb2hex($rgb)
    {
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex;
    }
}

if (!function_exists('clases_instanciables_en')) {

    /**
     * Devuelve todas las clases instanciables de un espacio de nombres dado
     *
     * @return [ Clase => class_basename( Clase ) ]
     */
    function clases_instanciables_en($espacio_de_nombres)
    {
        $finder = new Symfony\Component\Finder\Finder;
        $iter   = new hanneskod\classtools\Iterator\ClassIterator($finder->in(app_path()));
        $iter->enableAutoloading();

        $clases = [];
        foreach( $iter->inNamespace($espacio_de_nombres)->where('isInstantiable')->getClassMap() as $class => $splInfo ){
            $clases[ $class ] = class_basename( $class );
        }

        asort( $clases );

        return $clases;
    }
}

if (!function_exists('merge_collections')) {

    /**
     * Mezcla dos colecciones sean del tipo que sean
     * @return Collection $colection1
     * @return Collection $colection2
     */
    function merge_collections( $collection1, $collection2 ){
        $collection = clone $collection1;
        foreach ($collection2 as $item){
            $collection->push($item);
        }
        return $collection;
    }
}

if (!function_exists('get_icon')) {
    /**
     * @param file $file Direccion relativa del archivo
     * @param type $mimeType tipo mime del archivo
     */
    function get_icon($file){     
        $mimeType = public_path().'/'.$file;
        if(str_contains($mimeType, 'image')){
            $icon = $valor;
        }else if(str_contains($mimeType, 'pdf')){
            $icon = asset('assets/img/icons/pdf.png');
        }else{
            $icon = asset('assets/img/icons/generic.png');
        }
        return $icon;
    }
}