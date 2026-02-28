<?php
include "Includes/db.php";
$res = $conn->query("
SELECT 
    m.nome,
    mv.tipo,
    mv.quantidade,
    mv.observacao,
    mv.data_movimentacao
FROM movimentacoes mv
JOIN materiais m ON m.id = mv.material_id
ORDER BY mv.data_movimentacao DESC
");
?>

<h1>📚 Histórico de Movimentações</h1>

<table border="1" cellpadding="8">
<tr>
    <th>Material</th>
    <th>Tipo</th>
    <th>Quantidade</th>
    <th>Observação</th>
    <th>Data</th>
</tr>

<?php while ($h = $res->fetch_assoc()): ?>
<tr>
    <td><?= $h['nome'] ?></td>
    <td><?= strtoupper($h['tipo']) ?></td>
    <td><?= $h['quantidade'] ?></td>
    <td><?= $h['observacao'] ?></td>
    <td><?= date('d/m/Y H:i', strtotime($h['data_movimentacao'])) ?></td>
</tr>
<?php endwhile; ?>
</table>
