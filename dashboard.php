<?php
session_start();

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}
include "Includes/db.php";

/*
|--------------------------------------------------------------------------
| 1. CAPTURA DO FILTRO
|--------------------------------------------------------------------------
*/
$categoriaFiltro = $_GET['categoria'] ?? '';

/*
|--------------------------------------------------------------------------
| 2. BUSCAR CATEGORIAS PARA O SELECT
|--------------------------------------------------------------------------
*/
$listaCategorias = $conn->query("
    SELECT DISTINCT categoria
    FROM materiais
    WHERE categoria IS NOT NULL AND categoria != ''
");

/*
|--------------------------------------------------------------------------
| 3. CONDIÇÃO DINÂMICA PARA SQL
|--------------------------------------------------------------------------
*/
$where = "";
$bind = [];

if ($categoriaFiltro) {
    $where = "WHERE categoria = ?";
    $bind[] = $categoriaFiltro;
}

/*
|--------------------------------------------------------------------------
| 4. TOTAL DE MATERIAIS
|--------------------------------------------------------------------------
*/
if ($categoriaFiltro) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) total
        FROM materiais
        WHERE categoria = ?
    ");
    $stmt->bind_param("s", $categoriaFiltro);
    $stmt->execute();
    $totalMateriais = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $totalMateriais = $conn->query("
        SELECT COUNT(*) total FROM materiais
    ")->fetch_assoc()['total'];
}

/*
|--------------------------------------------------------------------------
| 5. BAIXO ESTOQUE
|--------------------------------------------------------------------------
*/
if ($categoriaFiltro) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) total
        FROM materiais
        WHERE quantidade <= minimo AND categoria = ?
    ");
    $stmt->bind_param("s", $categoriaFiltro);
    $stmt->execute();
    $baixoEstoque = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $baixoEstoque = $conn->query("
        SELECT COUNT(*) total
        FROM materiais
        WHERE quantidade <= minimo
    ")->fetch_assoc()['total'];
}

/*
|--------------------------------------------------------------------------
| 6. DADOS PARA O GRÁFICO
|--------------------------------------------------------------------------
*/
$categorias = [];
$quantidades = [];

if ($categoriaFiltro) {
    $stmt = $conn->prepare("
        SELECT categoria, SUM(quantidade) total
        FROM materiais
        WHERE categoria = ?
        GROUP BY categoria
    ");
    $stmt->bind_param("s", $categoriaFiltro);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query("
        SELECT categoria, SUM(quantidade) total
        FROM materiais
        GROUP BY categoria
    ");
}

while ($row = $res->fetch_assoc()) {
    $categorias[] = $row['categoria'] ?: 'Sem categoria';
    $quantidades[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Estoque</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>📊 Dashboard - Estoque INFRA</h1>

<form method="GET">
    <label>Filtrar por categoria:</label>
    <select name="categoria" onchange="this.form.submit()">
        <option value="">Todas</option>
        <?php while ($cat = $listaCategorias->fetch_assoc()): ?>
            <option value="<?= $cat['categoria'] ?>"
                <?= $categoriaFiltro == $cat['categoria'] ? 'selected' : '' ?>>
                <?= $cat['categoria'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<hr>

<div style="display:flex; gap:30px; margin:20px 0">
    <div style="padding:15px; border:1px solid #ccc">
        <h3>📦 Total de Materiais</h3>
        <strong><?= $totalMateriais ?></strong>
    </div>

    <div style="padding:15px; border:1px solid #ccc">
        <h3>⚠️ Baixo Estoque</h3>
        <strong><?= $baixoEstoque ?></strong>
    </div>

    <div style="padding:15px; border:1px solid #ccc">
        <h3>🗂️ Categorias</h3>
        <strong><?= count($categorias) ?></strong>
    </div>
</div>
<a href="exportar_csv.php">⬇️ Exportar Excel</a>
<hr>

<h3>📈 Quantidade por Categoria</h3>
<canvas id="graficoCategoria" width="400"></canvas>

<script>
new Chart(document.getElementById('graficoCategoria'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($categorias) ?>,
        datasets: [{
            label: 'Quantidade total',
            data: <?= json_encode($quantidades) ?>
        }]
    }
});
</script>

</body>
</html>
