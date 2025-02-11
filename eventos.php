<?php
include 'db.php';
$eventos = [];
$result = $conn->query("SELECT titulo, criado_em FROM anotacoes");

while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'title' => $row['titulo'],
        'start' => $row['criado_em']
    ];
}

echo json_encode($eventos);
?>