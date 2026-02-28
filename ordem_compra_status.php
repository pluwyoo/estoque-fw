<?php
include "Includes/db.php";

$id     = (int) ($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

$permitidos = ['aprovada', 'concluida'];

if (!$id || !in_array($status, $permitidos)) {
    die("Ação inválida");
}

/* ===============================
   BUSCA ORDEM
================================ */
$res = $conn->query("
    SELECT * FROM ordem_compra 
    WHERE id = $id
");

$ordem = $res->fetch_assoc();

if (!$ordem) {
    die("Ordem não encontrada");
}

/* ===============================
   SE CONCLUIR → ENTRA NO ESTOQUE
================================ */
if ($status === 'concluida' && $ordem['status'] !== 'concluida') {

    $material_id = $ordem['material_id'];
    $quantidade  = $ordem['quantidade_sugerida'];

    // Atualiza estoque
    $conn->query("
        UPDATE materiais
        SET quantidade = quantidade + $quantidade
        WHERE id = $material_id
    ");

    // Registra movimentação
    $conn->query("
        INSERT INTO movimentacoes 
            (material_id, tipo, quantidade, observacao)
        VALUES
            ($material_id, 'entrada', $quantidade, 'Entrada via ordem de compra')
    ");
}

/* ===============================
   ATUALIZA STATUS
================================ */
$conn->query("
    UPDATE ordem_compra
    SET status = '$status'
    WHERE id = $id
");

header("Location: ordem_compra.php");
exit;
