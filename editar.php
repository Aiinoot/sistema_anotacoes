
<?php
include 'index.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM anotacoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $anotacao = $result->fetch_assoc();
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $stmt = $conn->prepare("UPDATE anotacoes SET titulo = ?, descricao = ? WHERE id = ?");
    $stmt->bind_param("ssi", $titulo, $descricao, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Anotação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center mb-4">Editar Anotação</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $anotacao['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($anotacao['titulo']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3" required><?= htmlspecialchars($anotacao['descricao']) ?></textarea>
        </div>
        <button type="submit" name="editar" class="btn btn-success">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
```
