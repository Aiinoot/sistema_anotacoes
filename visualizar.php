<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'sistema_anotacoes';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT titulo, descricao, imagem, criado_em FROM anotacoes WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$stmt->bind_result($titulo, $descricao, $imagem_nome, $criado_em);

if (!$stmt->fetch()) {
    header("Location: index.php");
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Anotação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?php include 'header.php'; ?>
    
    <div class="card shadow-lg p-4">
        <h2 class="text-center"><?= htmlspecialchars($titulo) ?></h2>
        <p class="text-muted text-center">Criado em: <?= date("d/m/Y H:i:s", strtotime($criado_em)) ?></p>
        
        <div class="mt-4">
            <p class="fs-5"><?= nl2br(htmlspecialchars($descricao)) ?></p>
        </div>

        <?php if (!empty($imagem_nome) && file_exists("uploads/" . $imagem_nome)): ?>
            <div class="text-center mt-4">
                <img src="uploads/<?= htmlspecialchars($imagem_nome) ?>" class="img-fluid rounded" style="max-width: 500px;">
            </div>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="index.php" class="btn btn-primary">Voltar</a>
            <a href="index.php?editar=<?= $id ?>" class="btn btn-warning">Editar</a>
            <a href="index.php?excluir=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta anotação?')">Excluir</a>
        </div>
    </div>
</body>
</html>
