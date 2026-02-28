<?php
function exigePerfil(array $permitidos)
{
    if (!isset($_SESSION['logado'])) {
        header("Location: login.php");
        exit;
    }

    if (!in_array($_SESSION['perfil'], $permitidos)) {
        die("⛔ Acesso negado");
    }
}