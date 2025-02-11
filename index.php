<?php
session_start();

// Conexão com o banco
$host = 'localhost';
$user = 'root'; // Altere conforme seu ambiente
$password = ''; // Altere conforme seu ambiente
$database = 'sistema_anotacoes';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Criar anotação
if (isset($_POST['criar'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $usuario_id = $_SESSION['usuario_id']; // Pega o usuário logado

    $stmt = $conn->prepare("INSERT INTO anotacoes (usuario_id, titulo, descricao) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $titulo, $descricao);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Excluir anotação
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $usuario_id = $_SESSION['usuario_id'];

    // Certifique-se de que o usuário só pode excluir suas próprias anotações
    $stmt = $conn->prepare("DELETE FROM anotacoes WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Buscar anotações do usuário logado
$stmt = $conn->prepare("SELECT * FROM anotacoes WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Anotações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?php include 'header.php'; ?>
    <h2 class="text-center mb-4">Sistema de Anotações</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" name="criar" class="btn btn-primary">Criar Anotação</button>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['titulo']) ?></td>
                <td><?= htmlspecialchars($row['descricao']) ?></td>
                <td><?= $row['criado_em'] ?></td>
                <td>
                    <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="?excluir=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
