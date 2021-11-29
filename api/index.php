<?php
include "conexao.php";
$con=Conexao::getConexao();

if($_SERVER['REQUEST_METHOD'] == "GET") {
    if(isset($_REQUEST['timestamp']) and !empty($_REQUEST['timestamp'])) {
        $timestamp = $_REQUEST['timestamp'];
        $sql="SELECT * FROM message WHERE timestamp>$timestamp";
        if ($resultado=$con->query($sql)) {
            $return["status"] = "ok";
            $return["rows"]=$resultado->rowCount();
            $messages=null;
            while($item=$resultado->fetch(PDO::FETCH_ASSOC)) {
                $messages[]=$item;
            }
            $return["msg"] = $messages;
            echo json_encode($return);
            exit;
        }
    } 
} else if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(file_get_contents("php://input") != null and !empty(file_get_contents("php://input"))) {
        if($request = json_decode(file_get_contents("php://input"), true)) {
            if(isset($request["nick"]) && isset($request["message"])) {
                $timestamp = time() * 1000;
                $nick = $request["nick"];
                $message = $request["message"];
                $sql = "INSERT INTO message(message, nick, timestamp) VALUES (\"".$message."\",\"".$nick."\",".$timestamp.")";
                if($con->query($sql)) {
                    $request["timestamp"] = $timestamp;
                    $return["request"] = $request;
                    $return["status"] = "ok";
                } else {
                    $return["status"] = "err";
                    $return["err"] = "Erro ao inserir dados no banco de dados";
                }

            } else {
                $return["status"] = "err";
                $return["err"] = "Nem todos os parametros foram enviados";
            }

        } else {
            $return["status"] = "err";
            $return["err"] = "Request não está em JSON";
        }
    } else {
        $return["status"] = "err";
        $return["err"] = "Request está vazia";
    }
    echo json_encode($return);
} else if($_SERVER['REQUEST_METHOD'] == "PUT") {
    echo "{'status': 'PUT'}";
} else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
    echo "{'status': 'DELETE'}";
}