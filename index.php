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

// Criar diretório de uploads se não existir
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Editar anotação
$editando = false;
$titulo = "";
$descricao = "";
$id_editar = null;
$imagem_nome = null;
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $stmt = $conn->prepare("SELECT titulo, descricao, imagem FROM anotacoes WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id_editar, $_SESSION['usuario_id']);
    $stmt->execute();
    $stmt->bind_result($titulo, $descricao, $imagem_nome);
    if ($stmt->fetch()) {
        $editando = true;
    }
    $stmt->close();
}

// Criar ou atualizar anotação
if (isset($_POST['salvar'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $usuario_id = $_SESSION['usuario_id'];
    
    if (!empty($_FILES['imagem']['name'])) {
        $imagem_extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem_nome = time() . '.' . $imagem_extensao;
        $caminho_imagem = "uploads/" . $imagem_nome;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_imagem);
    }
    
    if ($editando) {
        $stmt = $conn->prepare("UPDATE anotacoes SET titulo = ?, descricao = ?, imagem = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("sssii", $titulo, $descricao, $imagem_nome, $id_editar, $usuario_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO anotacoes (usuario_id, titulo, descricao, imagem) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $titulo, $descricao, $imagem_nome);
    }
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Excluir anotação
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $usuario_id = $_SESSION['usuario_id'];
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

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="id" value="<?= $id_editar ?>">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($titulo) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3" required><?= htmlspecialchars($descricao) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagem (opcional)</label>
            <input type="file" name="imagem" class="form-control">
            <?php if ($imagem_nome): ?>
                <img src="uploads/<?= htmlspecialchars($imagem_nome) ?>" width="100" class="img-thumbnail mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" name="salvar" class="btn btn-primary">Salvar Anotação</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Imagem</th>
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
                    <?php if (!empty($row['imagem']) && file_exists("uploads/" . $row['imagem'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['imagem']) ?>" width="100" class="img-thumbnail">
                    <?php else: ?>
                        <span>Sem imagem</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="?excluir=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>