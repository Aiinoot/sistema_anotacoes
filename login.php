<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            $_SESSION['usuario_id'] = $row['id'];
            header("Location: index.php");
            exit();
        } else {
            $erro = "Usuário ou senha incorretos!";
        }
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center">Login</h2>
    <?php if (isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Usuário</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Entrar</button>
        <a href="register.php" class="btn btn-secondary">Registrar-se</a>
    </form>
</body>
</html>