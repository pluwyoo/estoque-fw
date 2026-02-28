<?php
include "Includes/db.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=estoque.csv');

$output = fopen("php://output", "w");

fputcsv($output, ['Material', 'Categoria', 'Quantidade', 'Mínimo']);

$res = $conn->query("
    SELECT nome, categoria, quantidade, minimo 
    FROM materiais
");

while ($row = $res->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
