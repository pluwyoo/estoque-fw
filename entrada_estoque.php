<?php
session_start();

include "Includes/db.php";
include "Includes/auth.php";

exigePerfil(['admin','infra']);

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    die("Usuário não identificado");
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $material    = (int) ($_POST['material'] ?? 0);
    $quantidade  = (int) ($_POST['quantidade'] ?? 0);
    $observacao  = $_POST['observacao'] ?: 'Entrada manual';

    if ($material <= 0 || $quantidade <= 0) {
        $msg = "❌ Dados inválidos";
    } else {

        /* ===============================
           ATUALIZA ESTOQUE
        ================================ */
        $stmt = $conn->prepare("
            UPDATE materiais
            SET quantidade = quantidade + ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $quantidade, $material);
        $stmt->execute();

        /* ===============================
           HISTÓRICO
        ================================ */
        $stmt = $conn->prepare("
            INSERT INTO movimentacoes
            (material_id, tipo, quantidade, observacao, obra_id, usuario)
            VALUES (?, 'entrada', ?, ?, NULL, ?)
        ");
        $stmt->bind_param(
            "iiss",
            $material,
            $quantidade,
            $observacao,
            $usuario
        );
        $stmt->execute();

        $msg = "✅ Entrada registrada com sucesso!";
    }
}

$materiais = $conn->query("SELECT id, nome FROM materiais");
?>

<h1>📥 Entrada de Estoque</h1>
<p><?= $msg ?></p>

<form method="POST">
    <select name="material" required>
        <option value="">Selecione o material</option>
        <?php while ($m = $materiais->fetch_assoc()): ?>
            <option value="<?= $m['id'] ?>">
                <?= htmlspecialchars($m['nome']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="number" name="quantidade" min="1" required>
    <input type="text" name="observacao" placeholder="Ex: Compra fornecedor X">
    <button type="submit">Registrar Entrada</button>
</form>
