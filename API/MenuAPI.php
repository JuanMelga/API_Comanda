<?php
include_once("Entidades/Token.php");
include_once("Entidades/Menu.php");
class MenuApi extends Menu{  
    ///Registro de nuevas comidas
    public function RegistrarComida($request, $response, $args) {  
        $respuesta = "";      
        try {
            $parametros = $request->getParsedBody();
            $json = $request->getBody();
            $data = json_decode($json, true);
            $nombre = $data["nombre"];
            $descripcion = $data["descripcion"];
            $precio = $data["precio"];
            $id_sector = $data["id_sector"];
            $tiempo_promedio = $data["tiempo_promedio"];
            $fotos = $data["fotos"];              
    
            $respuesta = Menu::Registrar($nombre, $precio, $id_sector, $descripcion, $tiempo_promedio, $fotos);
        } 
        catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }
        finally {
            $newResponse = $response->withJson($respuesta,200);
            return $newResponse;
        }
    }

    ///Modificacion del menu
    public function ModificarComida($request, $response, $args){
        $parametros = $request->getParsedBody();
        $json = $request->getBody();
        $data = json_decode($json, true);
        $id = $data["id"];
        $nombre = $data["nombre"];
        $descripcion = $data["descripcion"];
        $precio = $data["precio"];
        $id_sector = $data["id_sector"];
        $tiempo_promedio = $data["tiempo_promedio"];        
        $fotos = $data["fotos"];                      

        // $respuesta = Menu::Modificar($id,$nombre,$precio,$sector);
        $newResponse = $response->withJson("OK",200);
        return $newResponse;
    }

    ///Lista el menú
    public function ListarMenu($request,$response,$args){
        $respuesta = Menu::Listar();
        $newResponse = $response->withJson($respuesta,200);
        return $newResponse;
    }

    ///Da de baja una comida
    public function BajaMenu($request,$response,$args){
        $id = $args["id"];
        $respuesta = Menu::Baja($id);
        $newResponse = $response->withJson($respuesta,200);
        return $newResponse;
    }
}