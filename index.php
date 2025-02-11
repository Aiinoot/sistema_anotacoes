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

if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

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

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("DELETE FROM anotacoes WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .card-note {
            transition: transform 0.2s ease-in-out;
        }
        .card-note:hover {
            transform: scale(1.02);
        }
        .text-truncate {
            max-width: 100%;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .img-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <h2 class="text-center mb-4 fw-bold">Anotações+</h2>

        <div class="card shadow p-4">
            <form method="POST" enctype="multipart/form-data">
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
                        <img src="uploads/<?= htmlspecialchars($imagem_nome) ?>" class="img-preview mt-2">
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="salvar" class="btn btn-success">Salvar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </div>
            </form>
        </div>

        <h3 class="mt-4">Minhas Anotações</h3>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card card-note shadow">
                        <?php if (!empty($row['imagem']) && file_exists("uploads/" . $row['imagem'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['imagem']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['titulo']) ?></h5>
                            <p class="card-text text-truncate"><?= htmlspecialchars($row['descricao']) ?></p>
                            <small class="text-muted"><?= date("d/m/Y H:i", strtotime($row['criado_em'])) ?></small>
                            <div class="d-flex gap-2 mt-3">
                                <a href="visualizar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                <a href="index.php?editar=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="?excluir=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
