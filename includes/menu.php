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

            <!-- Navegación - Visible solo en desktop -->
            <nav class="d-none d-md-block ms-auto">
                <ul class="d-flex mb-0">
                    <li class="mx-3">
                        <a href="/habitoo/home/usuarios/index.php"
                           class="nav-enlace-bonis <?= $paginaActual == 'index.php' ? 'active' : '' ?>">
                            <i class="bi bi-house-door-fill icono-menu me-2"></i>Inicio
                        </a>
                    </li>
                 
                    <li class="mx-3">
                        <a href="/habitoo/home/productividad/index.php"
                           class="nav-enlace-bonis <?= str_contains($paginaActual, 'productividad') ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart-line-fill icono-menu me-2"></i>Productividad
                        </a>
                    </li>
                    <li class="mx-3">
                        <a href="/habitoo/home/insignias/index.php"
                           class="nav-enlace-bonis <?= str_contains($paginaActual, 'insignias') ? 'active' : '' ?>">
                            <i class="bi bi-award-fill icono-menu me-2"></i>Insignias
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Iconos - visibles siempre -->
            <div class="user-icons-bonis dropdown d-flex align-items-center">

                <!-- Menú hamburguesa (móvil) -->
                <i class="bi bi-list menu-icon-bonis d-md-none me-3"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                </i>

                <!-- Dropdown hamburguesa (móvil) -->
                <ul class="dropdown-menu dropdown-menu-end d-md-none animate__fadeIn">
                    <li>
                        <a class="dropdown-item <?= $paginaActual == 'index.php' ? 'active' : '' ?>"
                           href="/habitoo/home/usuarios/index.php">
                            <i class="bi bi-house-door-fill icono-menu me-2"></i>Inicio
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?= str_contains($paginaActual, 'productividad') ? 'active' : '' ?>"
                           href="/habitoo/home/productividad/index.php">
                            <i class="bi bi-bar-chart-line-fill icono-menu me-2"></i>Productividad
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?= str_contains($paginaActual, 'insignias') ? 'active' : '' ?>"
                           href="/habitoo/home/insignias/index.php">
                            <i class="bi bi-award-fill icono-menu me-2"></i>Insignias
                        </a>
                    </li>
                </ul>

                <!-- Icono perfil -->
                <i class="bi bi-person-circle profile-icon-bonis"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                </i>

                <!-- Dropdown perfil -->
                <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn">
                    <li>
                        <a class="dropdown-item" href="/habitoo/home/perfil.php">
                            <i class="bi bi-person-fill icono-menu me-2"></i>Mi perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/habitoo/logout.php">
                            <i class="bi bi-box-arrow-right icono-menu me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>

            </div>

        </div>
    </div>
</header>
