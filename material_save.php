<?php
include "Includes/db.php";

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'];
$categoria = $_POST['categoria'];
$quantidade = $_POST['quantidade'];
$minimo = $_POST['minimo'];

$foto = null;

if (!empty($_FILES['foto']['name'])) {
    $foto = time() . "_" . $_FILES['foto']['name'];
    move_uploaded_file(
        $_FILES['foto']['tmp_name'],
        "assets/img/materiais/" . $foto
    );
}

if ($id) {
    $sql = "UPDATE materiais SET nome=?, categoria=?, quantidade=?, minimo=?";
    if ($foto) $sql .= ", foto='$foto'";
    $sql .= " WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $nome, $categoria, $quantidade, $minimo, $id);
} else {
    $stmt = $conn->prepare("
        INSERT INTO materiais (nome, categoria, quantidade, minimo, foto)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssiis", $nome, $categoria, $quantidade, $minimo, $foto);
}

$stmt->execute();
header("Location: materiais.php");
