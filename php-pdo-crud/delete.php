<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

verify_csrf_or_die();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('ID inválido.');
}

$pdo = getPDO();

try {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        exit('Usuário não encontrado.');
    }

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro ao deletar usuário: ' . $e->getMessage());
}
