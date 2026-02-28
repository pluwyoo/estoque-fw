<?php
include "Includes/db.php";

$res = $conn->query("SELECT * FROM materiais ORDER BY nome");
?>

<h2>Materiais</h2>
<a href="material_form.php">➕ Novo Material</a>

<hr>

<?php while($m = $res->fetch_assoc()): ?>
<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
    <img src="assets/img/materiais/<?= $m['foto'] ?: 'default.png' ?>" width="80">
    <strong><?= $m['nome'] ?></strong><br>

    Categoria: <?= $m['categoria'] ?><br>
    Quantidade: <?= $m['quantidade'] ?><br>
    Mínimo: <?= $m['minimo'] ?><br>

    <?php if ($m['quantidade'] <= $m['minimo']): ?>
        <span style="color:red">⚠️ Baixo estoque</span><br>
    <?php endif; ?>

    <a href="material_form.php?id=<?= $m['id'] ?>">✏️ Editar</a> |
    <a href="material_delete.php?id=<?= $m['id'] ?>" onclick="return confirm('Excluir?')">🗑️ Excluir</a>
</div>
<?php endwhile; ?>
