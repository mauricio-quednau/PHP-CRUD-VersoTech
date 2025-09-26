<?php
header('Content-Type: application/json');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if (!$input || !isset($input['funcao'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida']);
    exit;
}

$funcao = $input['funcao'];
$dados = $input;

// Executar a função no controller
require_once __DIR__ . '/../controllers/ColorController.php';
$controller = new ColorController();
if (method_exists($controller, $funcao)) {
    $resultado = $controller->$funcao($dados);
    echo json_encode($resultado);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Função não encontrada']);
}