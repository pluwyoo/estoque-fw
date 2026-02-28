<?php
include "Includes/db.php";

// buscar materiais críticos
$res = $conn->query("
    SELECT id, quantidade, minimo
    FROM materiais
    WHERE quantidade <= minimo
");

while ($m = $res->fetch_assoc()) {
    $sugerido = ($m['minimo'] * 2) - $m['quantidade'];

    // evita duplicar ordem pendente
    $check = $conn->prepare("
        SELECT id FROM ordem_compra
        WHERE material_id = ? AND status = 'pendente'
    ");
    $check->bind_param("i", $m['id']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("
            INSERT INTO ordem_compra (material_id, quantidade_sugerida)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $m['id'], $sugerido);
        $stmt->execute();
    }
}

header("Location: ordem_compra.php");
