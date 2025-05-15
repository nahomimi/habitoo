<?php
    $paginaActual = basename($_SERVER['PHP_SELF']);
?>

<header class="header-bonis py-4">
    <div class="container-header-bonis">
        <div class="d-flex justify-content-between align-items-center">

            <!-- Logo -->
            <a href="http://localhost/habitoo/index.php">
                <img src="http://localhost/habitoo/assets/img/logito.png" alt="Habitoo Logo" width="170">
            </a>

            <!-- Navegación - Visible en desktop -->
            <nav class="d-none d-md-block ms-auto">
                <ul class="d-flex mb-0">
                    <li class="mx-3">
                        <a href="/habitoo/home/usuarios/index.php"
                           class="nav-enlace-bonis <?= $paginaActual == 'index.php' ? 'active' : '' ?>">
                            Inicio
                        </a>
                    </li>
                    <li class="mx-3">
                        <a href="/habitoo/home/usuarios/registrar.php"
                           class="nav-enlace-bonis <?= $paginaActual == 'registrar.php' ? 'active' : '' ?>">
                            <i class="bi bi-person-plus me-2"></i>Agregar usuario
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Iconos de usuario -->
            <div class="user-icons-bonis dropdown">
                <i class="bi bi-list menu-icon-bonis d-md-none me-3"></i>
                <i class="bi bi-person-circle profile-icon-bonis"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                </i>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/habitoo/logout.php">Cerrar sesión</a></li>
                </ul>
            </div>

        </div>
    </div>
</header>
