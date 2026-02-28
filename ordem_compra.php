<?php
include "Includes/db.php";

$res = $conn->query("
    SELECT 
        o.id,
        m.nome,
        o.quantidade_sugerida,
        o.status,
        o.criado_em
    FROM ordem_compra o
    JOIN materiais m ON m.id = o.material_id
    ORDER BY o.criado_em DESC
");
?>

<h1>🧾 Ordens de Compra</h1>

<a href="ordem_compra_gerar.php">⚙️ Gerar Ordens Automaticamente</a>

<hr>

<table border="1" cellpadding="8">
<tr>
    <th>Material</th>
    <th>Quantidade</th>
    <th>Status</th>
    <th>Ação</th>
</tr>

<?php while ($o = $res->fetch_assoc()): ?>
<tr>
    <td><?= $o['nome'] ?></td>
    <td><?= $o['quantidade_sugerida'] ?></td>
    <td><?= $o['status'] ?></td>
    <td>
        <?php if ($o['status'] == 'pendente'): ?>
            <a href="ordem_compra_status.php?id=<?= $o['id'] ?>&status=aprovada">✅ Aprovar</a>
        <?php elseif ($o['status'] == 'aprovada'): ?>
            <a href="ordem_compra_status.php?id=<?= $o['id'] ?>&status=concluida">📦 Concluir</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
