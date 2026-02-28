<?php
session_start();
include "Includes/db.php";

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'] ?? '';
    $senha   = $_POST['senha'] ?? '';

    $stmt = $conn->prepare("
        SELECT senha, perfil 
        FROM usuarios 
        WHERE usuario = ?
    ");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($hash, $perfil);
        $stmt->fetch();

        if (password_verify($senha, $hash)) {

            // 🔐 LOGIN OK → cria sessão
            $_SESSION['logado']  = true;
            $_SESSION['perfil']  = $perfil;
            $_SESSION['usuario'] = $usuario;

            header("Location: movimentar.php");
            exit;
        } else {
            // ❌ senha errada
            $erro = "❌ Login inválido";
        }

    } else {
        // ❌ usuário não existe
        $erro = "❌ Login inválido";
    }
}
?>

<h2>Login</h2>

<form method="POST">
    <input name="usuario" placeholder="Usuário" required>
    <input name="senha" type="password" placeholder="Senha" required>
    <button>Entrar</button>
</form>

<p style="color:red"><?= $erro ?></p>
