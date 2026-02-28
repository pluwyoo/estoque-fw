<?php
session_start();

include "Includes/db.php";
include "Includes/auth.php";

/*
|--------------------------------------------------
| 1. CONTROLE DE ACESSO
|--------------------------------------------------
| Somente admin e infra podem movimentar estoque
*/
exigePerfil(['admin','infra']);

/*
|--------------------------------------------------
| 2. GARANTE QUE VEIO VIA POST
|--------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido");
}

/*
|--------------------------------------------------
| 3. CAPTURA E VALIDA DADOS
|--------------------------------------------------
*/
$material_id = isset($_POST['material_id']) ? (int) $_POST['material_id'] : 0;
$tipo        = $_POST['tipo'] ?? '';
$quantidade  = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;
$obra_id     = !empty($_POST['obra_id']) ? (int) $_POST['obra_id'] : null;
$obs         = $_POST['observacao'] ?? '';
$usuario     = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    die("Usuário não autenticado");
}

if ($material_id <= 0 || $quantidade <= 0 || !in_array($tipo, ['entrada','saida'])) {
    die("Dados inválidos");
}

/*
|--------------------------------------------------
| 4. BUSCA ESTOQUE ATUAL
|--------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT quantidade 
    FROM materiais 
    WHERE id = ?
");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$res = $stmt->get_result();
$mat = $res->fetch_assoc();

if (!$mat) {
    die("Material inexistente");
}

/*
|--------------------------------------------------
| 5. REGRAS DE NEGÓCIO
|--------------------------------------------------
*/
if ($tipo === 'saida') {

    // saída PRECISA ter obra
    if (!$obra_id) {
        die("Saída exige obra vinculada");
    }

    // não deixa estoque ficar negativo
    if ($quantidade > $mat['quantidade']) {
        die("Estoque insuficiente");
    }

    // debita estoque
    $stmt = $conn->prepare("
        UPDATE materiais
        SET quantidade = quantidade - ?
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $quantidade, $material_id);
    $stmt->execute();
}

if ($tipo === 'entrada') {

    // entrada NÃO tem obra
    $obra_id = null;

    $stmt = $conn->prepare("
        UPDATE materiais
        SET quantidade = quantidade + ?
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $quantidade, $material_id);
    $stmt->execute();
}

/*
|--------------------------------------------------
| 6. REGISTRA HISTÓRICO
|--------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO movimentacoes
        (material_id, tipo, quantidade, observacao, obra_id, usuario)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isisss",
    $material_id,
    $tipo,
    $quantidade,
    $obs,
    $obra_id,
    $usuario
);

$stmt->execute();

/*
|--------------------------------------------------
| 7. REDIRECIONA
|--------------------------------------------------
*/
header("Location: dashboard.php");
exit;
