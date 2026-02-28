<?php
include "Includes/db.php";

$sql = "SELECT * FROM materiais WHERE quantidade <= minimo";
$res = $conn->query($sql);
?>

<h2>Materiais em Falta</h2>

<?php while($m = $res->fetch_assoc()): ?>
<p>
⚠️ <?= $m['nome'] ?> (<?= $m['quantidade'] ?> unidades)
</p>
<?php endwhile; ?>
