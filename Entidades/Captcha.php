<?php
include_once("DB/AccesoDatos.php");
class Captcha
{
    function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }

    function getColor(){
        $retorno = "";
        $num = rand(1,4);
        switch($num){
            case 1:
                $retorno = "Rojo";
                break;
            case 2:
                $retorno = "Amarillo";
                break;
            case 3:
                $retorno = "Azul";
                break;
            case 4:
                $retorno = "Verde";
                break;
        }

        return $retorno;
    }

    function getFoto($color){
        $retorno = "";

        switch($color){
            case "Rojo":
                $retorno = "iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQAQMAAAC6caSPAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAADUExURc0AAD4NOMkAAAAJcEhZcwAAASAAAAEgAKj/ZiUAAAAqSURBVHja7cExAQAAAMKg9U/tbwagAAAAAAAAAAAAAAAAAAAAAAAAAIA3T7AAAZRBGFwAAAAASUVORK5CYII=";
                break;
            case "Amarillo":
                $retorno = "iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQAQMAAAC6caSPAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAADUExURfPzDVHYPpAAAAAJcEhZcwAAASAAAAEgAKj/ZiUAAAAqSURBVHja7cExAQAAAMKg9U/tbwagAAAAAAAAAAAAAAAAAAAAAAAAAIA3T7AAAZRBGFwAAAAASUVORK5CYII=";
                break;
            case "Azul":
                $retorno = "iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQAQMAAAC6caSPAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAADUExURQBmzXEIT/YAAAAJcEhZcwAAASAAAAEgAKj/ZiUAAAAqSURBVHja7cExAQAAAMKg9U/tbwagAAAAAAAAAAAAAAAAAAAAAAAAAIA3T7AAAZRBGFwAAAAASUVORK5CYII=";
                break;
            case "Verde":
                $retorno = "iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQAQMAAAC6caSPAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAADUExURQCZAAf5DIkAAAAqSURBVHja7cExAQAAAMKg9U/tbwagAAAAAAAAAAAAAAAAAAAAAAAAAIA3T7AAAZRBGFwAAAAASUVORK5CYII=";
                break;
        }

        return $retorno;
    }

    ///Genera un desafío de Captcha
    public static function Obtener()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $respuesta = "";
        try {
            $key = getGUID();
            $color = getColor(); 
            $foto = getFoto($color);
            date_default_timezone_set("America/Argentina/Buenos_Aires");
            $fecha = date('Y-m-d H:i:s');

            // $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO captcha (key, color, fecha) 
            //                                                 VALUES (:key, :color, :fecha);");

            // $consulta->bindValue(':key', $key, PDO::PARAM_STR);
            // $consulta->bindValue(':color', $color, PDO::PARAM_STR);
            // $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);

            // $consulta->execute();

            $respuesta = array("estado" => "OK", "key" => "$key", "foto" => "$foto");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("estado" => "ERROR");
        }
        finally {
            return $respuesta;
        }
    }

    ///Valida un desafío de captcha
    public static function Validar($key, $color)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT Count(*) FROM captcha WHERE key = :key AND color = :color;");

            $consulta->bindValue(':key', $key, PDO::PARAM_STR);
            $consulta->bindValue(':color', $color, PDO::PARAM_STR);
            $consulta->execute();
            $resultado = $consulta->fetch();
            if($resultado != null && $resultado == 1){
                $respuesta = array("Estado" => "OK");
            }
            else{
                $respuesta = array("Estado" => "ERROR");
            }

        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }
        finally {
            return $respuesta;
        }
    }

}
?>