<?php
include_once("DB/AccesoDatos.php");
class Pedido
{
    public $codigo;
    public $estado;
    public $mesa;
    public $descripcion;
    public $id_menu;
    public $sector;
    public $nombre_cliente;
    public $nombre_mozo;
    public $id_mozo;
    public $id_encargado;
    public $hora_inicial;
    public $hora_entrega_estimada;
    public $hora_entrega_real;
    public $fecha;
    public $importe;
    public $es_delivery;
    public $direccion_delivery;
    public $fire_mail_delivery;
    public $fire_mail_cliente;

    ///Registra un nuevo pedido
    public static function Registrar($id_mesa, $id_menu, $id_mozo, $nombre_cliente, $es_delivery, $direccion_delivery, $fire_mail_cliente)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $respuesta = "";
        try {
            $codigo = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);

            date_default_timezone_set("America/Argentina/Buenos_Aires");
            $fecha = date('Y-m-d');
            $hora_inicial = date('H:i');

            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO pedido (codigo, id_estado_pedidos, fecha, hora_inicial,
                                                            id_mesa, id_menu, id_mozo, nombre_cliente, es_delivery, direccion_delivery, fire_mail_cliente)
                                                            VALUES (:codigo, 1, :fecha, :hora_inicial,
                                                            :id_mesa, :id_menu, :id_mozo, :nombre_cliente, :es_delivery, :direccion_delivery, :fire_mail_cliente);");

            $consulta->bindValue(':id_menu', $id_menu, PDO::PARAM_INT);
            $consulta->bindValue(':id_mozo', $id_mozo, PDO::PARAM_INT);
            $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':nombre_cliente', $nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $consulta->bindValue(':hora_inicial', $hora_inicial, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':es_delivery', $es_delivery, PDO::PARAM_INT);
            $consulta->bindValue(':direccion_delivery', $direccion_delivery, PDO::PARAM_STR);
            $consulta->bindValue(':fire_mail_cliente', $fire_mail_cliente, PDO::PARAM_STR);
            $respuesta = $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedido registrado correctamente.");
        } catch (Exception $e) {

            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {

            return $respuesta;
        }
    }

    ///Autorizar todos los pedidos.
    public static function AutorizarTodos($id_mozo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido p 
                                                            INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                            SET p.id_mozo = :id_mozo
                                                            WHERE id_mozo = 0 AND ep.descripcion = 'Pendiente'");

            $consulta->bindValue(':id_mozo', $id_mozo, PDO::PARAM_STR);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedidos autorizados correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Cancelar pedido.
    public static function Cancelar($codigo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET id_estado_pedidos = 5 WHERE codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedido cancelado correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Listado completo de pedidos
    public static function ListarTodos()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector");

            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Pedido por codigo.
    public static function ObtenerPorCodigo($codigo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        WHERE p.codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Listado completo de pedidos por fecha
    public static function ListarTodosPorFecha($fecha)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                        WHERE p.fecha = :fecha");
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Listado de pedidos por mesa. No muestra cancelados ni finalizados.
    public static function ListarPorMesa($mesa)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                        WHERE p.id_mesa = :mesa AND ep.descripcion NOT IN ('Cancelado','Finalizado')");
            $consulta->bindValue(':mesa', $mesa, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Listado de pedidos por sector. No muestra cancelados ni finalizados.
    public static function ListarActivosPorSector($sector, $id_empleado)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            switch ($sector) {
                    //Si es socio los lista a todos.
                case "Socio":
                    $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                                me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                                em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                                p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                                p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                                FROM pedido p
                                                                INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                                INNER JOIN menu me ON me.id = p.id_menu
                                                                INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                                INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                                WHERE ep.descripcion NOT IN ('Cancelado','Finalizado')");
                    break;
                    //Si es mozo lista los de ese mozo.
                case "Mozo":
                    $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                            me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                            em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                            p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery
                                                            FROM pedido p
                                                            INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                            INNER JOIN menu me ON me.id = p.id_menu
                                                            INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                            INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                            WHERE p.id_mozo = :id_mozo AND ep.descripcion NOT IN ('Cancelado','Finalizado')");
                    $consulta->bindValue(':id_mozo', $id_empleado, PDO::PARAM_STR);
                    break;
                    //Para los demás lista por sector.
                default:
                    $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                            me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                            em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                            p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery
                                                            FROM pedido p
                                                            INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                            INNER JOIN menu me ON me.id = p.id_menu
                                                            INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                            INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                            WHERE te.descripcion = :sector AND ep.descripcion NOT IN ('Cancelado','Finalizado','Entregado','Listo para Servir')");
                    $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
                    break;
            }

            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Listado de pedidos cancelados.
    public static function ListarCancelados()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                        WHERE ep.descripcion = 'Cancelado'");
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Uno de los empleados toma el pedido para prepararlo, agregando un tiempo estimado de preparación.
    public static function TomarPedido($codigo, $id_encargado, $minutosEstimadosDePreparacion)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $time = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
            $time->add(new DateInterval('PT' . $minutosEstimadosDePreparacion . 'M'));

            $hora_entrega_estimada = $time->format('H:i');

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET id_estado_pedidos = 2, id_encargado = :id_encargado,
                                                            hora_entrega_estimada = :hora_entrega_estimada WHERE codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':hora_entrega_estimada', $hora_entrega_estimada, PDO::PARAM_STR);
            $consulta->bindValue(':id_encargado', $id_encargado, PDO::PARAM_INT);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedido tomado correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Se informa que el pedido está listo para servir.
    public static function InformarCambioEstado($codigo, $estado, $id_mozo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $time = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
            $hora_entrega_real = $time->format('H:i');

            if ($estado == "Listo para Servir") {
                $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido
                                                                 SET id_estado_pedidos = 3, hora_entrega_real = :hora_entrega_real
                                                                 WHERE codigo = :codigo");

                $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                $consulta->bindValue(':hora_entrega_real', $hora_entrega_real, PDO::PARAM_STR);
            } else {
                $consulta_id_estado = $objetoAccesoDato->RetornarConsulta("SELECT id_estado_pedidos FROM estado_pedidos WHERE descripcion = :estado");
                $consulta_id_estado->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta_id_estado->execute();
                $resultado = $consulta_id_estado->fetch()[0];

                if ($id_mozo == 0) {
                    $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET id_estado_pedidos = :estado WHERE codigo = :codigo");
                    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', $resultado, PDO::PARAM_STR);
                } else {
                    $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido
                                                                     SET id_estado_pedidos = :estado,
                                                                     id_mozo = :mozo
                                                                     WHERE codigo = :codigo");
                    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', $resultado, PDO::PARAM_STR);
                    $consulta->bindValue(':mozo', $id_mozo, PDO::PARAM_STR);
                }
            }
            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedido actualizado.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Se informa que el pedido fue entregado a la mesa.
    public static function Servir($codigo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET id_estado_pedidos = 4
                                                            WHERE codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedido servido correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Devuelve el tiempo restante
    public static function TiempoRestante($codigo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.hora_entrega_estimada as estimacion, ep.descripcion as estado FROM pedido p
                                                            INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                            WHERE p.codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();
            $pedido = $consulta->fetch();

            if ($pedido["estado"] == 'En Preparacion') {
                $time = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
                $hora_entrega = new DateTime($pedido['estimacion'], new DateTimeZone('America/Argentina/Buenos_Aires'));
                if ($time > $hora_entrega) {
                    $resultado = "Pedido retrasado.";
                } else {
                    $intervalo = $time->diff($hora_entrega);
                    $resultado = $intervalo->format('%H:%I:%S');
                }
            } else {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "El pedido se encuentra " . $pedido["estado"]);
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Finaliza los pedidos de la mesa
    public static function Finalizar($codigoMesa)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET id_estado_pedidos = 6
                                                            WHERE id_estado_pedidos <> 5 AND id_mesa = :codigo");

            $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Pedidos de la mesa finalizados correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Lo más vendido
    public static function MasVendido()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.id_menu, m.nombre, count(p.id_menu) as cantidad_ventas FROM pedido p INNER JOIN menu m
                                                            on m.id = p.id_menu GROUP BY(id_menu) HAVING count(p.id_menu) =
                                                            (SELECT MAX(sel.cantidad_ventas) FROM
                                                            (SELECT count(p.id_menu) as cantidad_ventas FROM pedido p GROUP BY(id_menu)) sel);");

            $consulta->execute();

            $respuesta = $consulta->fetchAll();
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Lo más vendido
    public static function MenosVendido()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.id_menu, m.nombre, count(p.id_menu) as cantidad_ventas FROM pedido p INNER JOIN menu m
                                                            on m.id = p.id_menu GROUP BY(id_menu) HAVING count(p.id_menu) =
                                                            (SELECT MIN(sel.cantidad_ventas) FROM
                                                            (SELECT count(p.id_menu) as cantidad_ventas FROM pedido p GROUP BY(id_menu)) sel);");

            $consulta->execute();

            $respuesta = $consulta->fetchAll();
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    //Lista los pedidos fuera del tiempo estipulado.
    public static function ListarFueraDelTiempoEstipulado()
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        em.nombre_empleado as nombre_mozo, p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        INNER JOIN empleado em ON em.ID_empleado = p.id_mozo
                                                        WHERE p.hora_entrega_estimada < p.hora_entrega_real");

            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    ///Pedido por cliente.
    public static function ObtenerPedidosCliente($nombre_cliente, $es_delivery)
    {
        $resultado = "";
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente, p.direccion_delivery
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        WHERE p.nombre_cliente = :nombre_cliente AND p.es_delivery = :es_delivery");

            $consulta->bindValue(':nombre_cliente', $nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':es_delivery', $es_delivery, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    ///Pedido por cliente.
    public static function ObtenerPedidosDelivery($fire_mail_delivery)
    {
        $resultado = "";
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT p.codigo, ep.descripcion as estado, p.id_mesa as mesa,
                                                        me.nombre as descripcion, p.id_menu, te.descripcion as sector, p.nombre_cliente,
                                                        p.id_mozo, p.id_encargado, p.hora_inicial, p.hora_entrega_estimada,
                                                        p.hora_entrega_real, p.fecha, me.precio as importe, p.es_delivery, p.direccion_delivery,
                                                        p.fire_mail_delivery, p.fire_mail_cliente
                                                        FROM pedido p
                                                        INNER JOIN estado_pedidos ep ON ep.id_estado_pedidos = p.id_estado_pedidos
                                                        INNER JOIN menu me ON me.id = p.id_menu
                                                        INNER JOIN tipoempleado te ON te.id_tipo_empleado = me.id_sector
                                                        WHERE (p.fire_mail_delivery = :fire_mail_delivery OR p.fire_mail_cliente = :fire_mail_delivery2)");

            $consulta->bindValue(':fire_mail_delivery', $fire_mail_delivery, PDO::PARAM_STR);
            $consulta->bindValue(':fire_mail_delivery2', $fire_mail_delivery, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function ActualizarDelivery($fire_mail_delivery, $codigo)
    {
        try {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedido SET fire_mail_delivery = :fire_mail_delivery WHERE codigo = :codigo");

            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':fire_mail_delivery', $fire_mail_delivery, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = array("Estado" => "OK", "Mensaje" => "Delivery actualizado correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }
}
