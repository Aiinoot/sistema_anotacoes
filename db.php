
<?php

$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'sistema_anotacoes';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Criar tabela de usuários se não existir
$sqlUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
)";
$conn->query($sqlUsuarios);

// Criar tabela de anotações se não existir
$sqlAnotacoes = "CREATE TABLE IF NOT EXISTS anotacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    favoritos BOOLEAN DEFAULT FALSE;
    imagem VARCHAR(255) NULL;
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)";
$conn->query($sqlAnotacoes);
?>