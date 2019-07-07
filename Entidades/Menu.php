<?php
include_once("DB/AccesoDatos.php");
class Menu
{
    public $id;
    public $precio;
    public $nombre;
    public $sector;
    public $id_sector;
    public $descripcion;
    public $tiempo_promedio;

    ///Registra una nueva comida al menu
    public static function Registrar($nombre, $precio, $id_sector, $descripcion, $tiempo_promedio, $fotos)
    {
        $respuesta = "";
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            //$objetoAccesoDato->IniciarTrasaccion();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT MAX(id) FROM menu;");
            $consulta->execute();
            $ult_id = $consulta->fetch();
            $id = 0;
            if ($ult_id != null) {
                $id = $ult_id[0] + 1;
            }
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO menu (id, nombre, precio, id_sector, descripcion, tiempo_promedio)
                                                                VALUES (:id, :nombre, :precio, :id_sector, :descripcion, :tiempo_promedio);");
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':id_sector', $id_sector, PDO::PARAM_INT);
            $consulta->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_promedio', $tiempo_promedio, PDO::PARAM_INT);
            $consulta->execute();

            foreach ($fotos as $foto) {
                $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO fotos_menu (idMenu, base64) VALUES (:idMenu, :base64);");
                $consulta->bindValue(':idMenu', $id, PDO::PARAM_INT);
                $consulta->bindValue(':base64', $foto, PDO::PARAM_INT);
                $consulta->execute();
            }

            $respuesta = array("Estado" => "OK", "Mensaje" => "Registrado correctamente.");
            //$objetoAccesoDato->Commit();
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            //$objetoAccesoDato->Rollback();
        } finally {
            return $respuesta;
        }
    }

    ///Modifica el menu
    public static function Modificar($id, $nombre, $precio, $sector)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $respuesta = "";
        try {
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT ID_tipo_empleado FROM tipoempleado WHERE Descripcion = :sector AND Estado = 'A';");

            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
            $id_sector = $consulta->fetch();

            if ($id_sector != null) {
                $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE menu SET nombre = :nombre, precio = :precio, id_sector = :id_sector
                                                                WHERE id = :id;");

                $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':id_sector', $id_sector[0], PDO::PARAM_INT);

                $consulta->execute();

                $respuesta = array("Estado" => "OK", "Mensaje" => "Modificado correctamente.");
            } else {
                $respuesta = array("Estado" => "ERROR", "Mensaje" => "Debe ingresar un sector valido");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Listado completo del menu
    public static function Listar()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id, m.nombre, m.precio, m.id_sector, te.Descripcion as sector, 
                                                            m.descripcion, m.tiempo_promedio, min(fm.idFoto), fm.base64 as foto
                                                            FROM menu m 
                                                            INNER JOIN tipoempleado te ON te.ID_tipo_empleado = m.id_sector
                                                            LEFT JOIN fotos_menu fm ON fm.idMenu = m.id
                                                            GROUP BY id, m.nombre, m.precio, m.id_sector, te.Descripcion, m.descripcion, 
                                                            m.tiempo_promedio;");

            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Menu");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Baja de comida.
    public static function Baja($id)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM menu WHERE id = :id");

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);

            $consulta->execute();

            $consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM fotos_menu WHERE idMenu = :id");

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Eliminado correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }
}
