<?php
include_once("Entidades/Token.php");
include_once("Entidades/Pedido.php");
class PedidoApi extends Pedido
{
    ///Registro de nuevos pedidos.
    public function RegistrarPedido($request, $response, $args)
    {
        $respuesta = "";
        try {
            $parametros = $request->getParsedBody();


            $id_mesa = $parametros["id_mesa"];
            $id_menu  = $parametros["id_menu"];
            $nombre_cliente = $parametros["cliente"];
            $es_delivery = $parametros["es_delivery"];
            $direccion_delivery = $parametros["direccion_delivery"];
            $fire_mail_cliente = $parametros["fire_mail_cliente"];
            $id_mozo = $parametros["id_mozo"];

            $respuesta = Pedido::Registrar($id_mesa, $id_menu, $id_mozo, $nombre_cliente, $es_delivery, $direccion_delivery, $fire_mail_cliente);
        } catch (Exception $ex) {
            $mensaje = $ex->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            $newResponse = $response->withJson($respuesta, 200);
            return $newResponse;
        }
    }

    ///Lista todos los pedidos
    public function ListarTodosLosPedidos($request, $response, $args)
    {
        $respuesta = Pedido::ListarTodos();
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lista todos los pedidos por Fecha
    public function ListarTodosLosPedidosPorFecha($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fecha = $parametros["fecha"];
        $respuesta = Pedido::ListarTodosPorFecha($fecha);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lista todos los pedidos por mesa
    public function ListarTodosLosPedidosPorMesa($request, $response, $args)
    {
        $mesa = $args["codigoMesa"];
        $respuesta = Pedido::ListarPorMesa($mesa);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lista todos los pedidos activos. Muestra los que correspondan según el perfil.
    public function ListarPedidosActivos($request, $response, $args)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $id_empleado = $payload->id;
        $sector = $payload->tipo;
        $respuesta = Pedido::ListarActivosPorSector($sector, $id_empleado);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lista todos los pedidos cancelados
    public function ListarTodosLosPedidosCancelados($request, $response, $args)
    {
        $respuesta = Pedido::ListarCancelados();
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Autorizar todos los pedidos.
    public function AutorizarTodos($request, $response, $args)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $id_mozo = $payload->id;
        $respuesta = Pedido::AutorizarTodos($id_mozo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Cancela un pedido.
    public function CancelarPedido($request, $response, $args)
    {
        $codigo = $args["codigo"];
        $respuesta = Pedido::Cancelar($codigo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    //Uno de los empleados toma el pedido para prepararlo, agregando un tiempo estimado de preparación.
    public function TomarPedidoPendiente($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $parametros["codigo"];
        $minutosEstimados = $parametros["minutosEstimados"];
        $payload = $request->getAttribute("payload")["Payload"];
        $id_encargado = $payload->id;
        $respuesta = Pedido::TomarPedido($codigo, $id_encargado, $minutosEstimados);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Se informa que el pedido está listo para servir.
    public function InformarPedidoCambioEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $parametros["codigo"];
        $estado = $parametros["estado"];
        $id_mozo = $parametros["id_mozo"];
        $respuesta = Pedido::InformarCambioEstado($codigo, $estado, $id_mozo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Devuelve el tiempo restante
    public function TiempoRestantePedido($request, $response, $args)
    {
        $codigo = $args["codigoPedido"];
        $respuesta = Pedido::TiempoRestante($codigo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Servir Pedido
    public function ServirPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $parametros["codigo"];
        $respuesta = Pedido::Servir($codigo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lo menos vendido
    public function LoMenosVendido($request, $response, $args)
    {
        $respuesta = Pedido::MenosVendido();
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    ///Lo más vendido
    public function LoMasVendido($request, $response, $args)
    {
        $respuesta = Pedido::MasVendido();
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    //Lista los pedidos fuera del tiempo estipulado.
    public function ListarPedidosFueraDelTiempoEstipulado($request, $response, $args)
    {
        $respuesta = Pedido::ListarFueraDelTiempoEstipulado();
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    //Lista los pedidos de un cliente
    public function GetPedidosCliente($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre_cliente = $parametros["nombre_cliente"];
        $es_delivery = $parametros["es_delivery"];
        $respuesta = Pedido::ObtenerPedidosCliente($nombre_cliente, $es_delivery);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    //Lista los pedidos de un cliente
    public function GetPedidosDelivery($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fire_mail_delivery = $parametros["fire_mail_delivery"];
        $respuesta = Pedido::ObtenerPedidosDelivery($fire_mail_delivery);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }

    public function UpdateDelivery($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fire_mail_delivery = $parametros["fire_mail_delivery"];
        $codigo = $parametros["codigo"];
        $respuesta = Pedido::ActualizarDelivery($fire_mail_delivery, $codigo);
        $newResponse = $response->withJson($respuesta, 200);
        return $newResponse;
    }
}
