<nav class="navbar navbar-expand-lg fixed-top glass-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="bi bi-pencil-square me-2"></i> Anotações+
        </a>


        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>


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


