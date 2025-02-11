<?php
include 'db.php';

$erro = "";

if (isset($_POST['registrar'])) {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($usuario) || empty($senha) || empty($confirmar_senha)) {
        $erro = "Preencha todos os campos!";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem!";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $erro = "Usuário já existe! Escolha outro nome.";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
            $stmt->bind_param("ss", $usuario, $senha_hash);
            $stmt->execute();
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?php include 'header.php'; ?>
    <h2 class="text-center">Registrar</h2>
    <?php if (!empty($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Usuário</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmar Senha</label>
            <input type="password" name="confirmar_senha" class="form-control" required>
        </div>
        <button type="submit" name="registrar" class="btn btn-success">Registrar</button>
        <a href="login.php" class="btn btn-secondary">Voltar ao Login</a>
    </form>
</body>
</html>
