<?
require_once(__DIR__."\annotation.php");
require_once(__DIR__."\annotation.svg.php");
require_once(__DIR__."\annotation.png.php");

class Annotator 
{
    private function __construct()
    {
        
    }

    public static function Png($imageUrl, $displayWidth, $jsonAnnotations) 
    {
        return new PngAnnotation($imageUrl, $displayWidth, $jsonAnnotations);
    }

    public static function Svg($imageUrl, $displayWidth, $jsonAnnotations) 
    {
        return new SvgAnnotation($imageUrl, $displayWidth, $jsonAnnotations);
    }
}

?>