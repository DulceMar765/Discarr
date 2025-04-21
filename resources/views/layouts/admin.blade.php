<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #0d6efd;
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 20px 0;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 30px;
        }

        .nav-link {
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }

        .card i {
            font-size: 2rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4><i class="bi bi-speedometer2"></i> Admin Discarr</h4>
        <a class="nav-link"                     href="{{ route('employee.index') }}">          
  <i class="bi bi-person-badge-fill"></i> Empleados
        </a>
        <a class="nav-link"   href="{{ route('supplier.index') }}">               <i class="bi bi-truck"></i> Proveedores
        </a>
        <a class="nav-link" href="{{ route('categories.index') }}">             <i class="bi bi-tags-fill"></i> Categorías
        </a>
        <a class="nav-link mt-auto" href="{{ route('logout') }}">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4">Inicio del Panel de Administración</h1>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge-fill me-3"></i>
                        <div>
                            <h5 class="mb-0">Gestión de Empleados</h5>
                            <small><a href="{{ route('employee.index') }}">Ir a empleados</a></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck me-3"></i>
                        <div>
                            <h5 class="mb-0">Gestión de Proveedores</h5>
                            <small><a href="{{ route('supplier.index') }}">Ir a proveedores</a></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-tags-fill me-3"></i>
                        <div>
                            <h5 class="mb-0">Gestión de Categorías</h5>
                            <small><a href="{{ route('categories.index') }}">Ir a categorías</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <div class="alert alert-primary text-center" role="alert">
            Bienvenido al sistema de administración del negocio. Usa el panel lateral para navegar por las secciones disponibles.
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>