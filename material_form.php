<?php
include "Includes/db.php";

$id = $_GET['id'] ?? null;
$material = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM materiais WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $material = $stmt->get_result()->fetch_assoc();
}
?>

<h2><?= $id ? "Editar" : "Novo" ?> Material</h2>

<form method="POST" action="material_save.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $material['id'] ?? '' ?>">

    <input name="nome" placeholder="Nome" required
           value="<?= $material['nome'] ?? '' ?>"><br>

    <input name="categoria" placeholder="Categoria"
           value="<?= $material['categoria'] ?? '' ?>"><br>

    <input type="number" name="quantidade" placeholder="Quantidade"
           value="<?= $material['quantidade'] ?? 0 ?>"><br>

    <input type="number" name="minimo" placeholder="Estoque mínimo"
           value="<?= $material['minimo'] ?? 0 ?>"><br>

    <input type="file" name="foto"><br>

    <button>Salvar</button>
</form>
