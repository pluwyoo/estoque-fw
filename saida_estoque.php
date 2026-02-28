<?php
include "Includes/db.php";

$erro = '';
$msg = '';

if ($_POST) {
    $material = $_POST['material'];
    $quantidade = $_POST['quantidade'];
    $motivo = $_POST['motivo'] ?: 'Saída manual';

    $res = $conn->query("
        SELECT quantidade FROM materiais WHERE id = $material
    ");
    $estoque = $res->fetch_assoc()['quantidade'];

    if ($quantidade > $estoque) {
        $erro = "❌ Estoque insuficiente!";
    } else {
        // subtrai
        $stmt = $conn->prepare("
            UPDATE materiais
            SET quantidade = quantidade - ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $quantidade, $material);
        $stmt->execute();

        // histórico
        $stmt = $conn->prepare("
            INSERT INTO estoque_movimentacoes
            (material_id, tipo, quantidade, motivo)
            VALUES (?, 'saida', ?, ?)
        ");
        $stmt->bind_param("iis", $material, $quantidade, $motivo);
        $stmt->execute();

        $msg = "✅ Saída registrada!";
    }
}

$materiais = $conn->query("SELECT id, nome FROM materiais");
?>

<h1>📤 Saída de Estoque</h1>

<?php if ($erro): ?><p style="color:red"><?= $erro ?></p><?php endif; ?>
<?php if ($msg): ?><p><?= $msg ?></p><?php endif; ?>

<form method="POST">
    <select name="material" required>
        <?php while ($m = $materiais->fetch_assoc()): ?>
            <option value="<?= $m['id'] ?>"><?= $m['nome'] ?></option>
        <?php endwhile; ?>
    </select>

    <input type="number" name="quantidade" min="1" required>
    <input type="text" name="motivo" placeholder="Ex: Obra Jardim Vertical X">
    <button>Registrar Saída</button>
</form>
