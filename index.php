<?php
session_start();

// Se NÃO estiver logado → manda pro login
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// Se estiver logado → manda pro sistema
header("Location: dashboard.php");
exit;
