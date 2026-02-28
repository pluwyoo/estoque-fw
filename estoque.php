<?php
session_start();
include "Includes/db.php";
include "Includes/auth.php";

/* 🔒 TODOS podem acessar */
exigePerfil(['admin','infra','leitura']);

$materiais = $conn->query("
    SELECT id, nome 
    FROM materiais 
    ORDER BY nome
");

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<h3>📦 Movimentar Estoque</h3>

<?php if ($msg): ?>
    <p><?= $msg ?></p>
<?php endif; ?>

<form action="movimentar.php" method="POST">

    <select name="material_id" required>
        <option value="">Selecione o material</option>
        <?php while ($m = $materiais->fetch_assoc()): ?>
            <option value="<?= $m['id'] ?>">
                <?= htmlspecialchars($m['nome']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="tipo" required>
        <option value="">Tipo</option>
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
    </select>

    <input type="number" name="quantidade" min="1" required>
    <input type="text" name="observacao">

    <?php if ($_SESSION['perfil'] !== 'leitura'): ?>
        <!-- 🔥 SÓ ADMIN E INFRA -->
        <button type="submit">Registrar</button>
    <?php else: ?>
        <p>🔒 Seu perfil é somente leitura</p>
    <?php endif; ?>

</form>
