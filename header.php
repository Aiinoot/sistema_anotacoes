<nav class="navbar navbar-expand-lg fixed-top glass-navbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="bi bi-pencil-square me-2"></i> Anotações+
        </a>

        <!-- Botão Responsivo -->
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Itens do Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white">👋 Olá, <strong><?= htmlspecialchars($_SESSION['nome']); ?></strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger logout-btn" href="logout.php">Sair</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light me-2" href="login.php">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="register.php">Registrar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Estilos personalizados -->
<style>
body {
    padding-top: 80px; /* Ajusta a altura do header */
}


/* Navbar Glassmorphism */
.glass-navbar {
    background: rgba(25, 25, 25, 0.85);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 10px 0;
}

/* Botão do menu responsivo */
.custom-toggler {
    border: none;
}

.custom-toggler:focus {
    box-shadow: none;
}

/* Estilo dos botões */
.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.logout-btn {
    border-radius: 20px;
    padding: 8px 16px;
    transition: 0.3s;
}

.logout-btn:hover {
    background: rgb(255, 100, 100);
    border-color: transparent;
}

/* Logo estilizado */
.navbar-brand {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    color: #fff !important;
}

.navbar-brand i {
    font-size: 1.7rem;
    color: #f8f9fa;
}
</style>
