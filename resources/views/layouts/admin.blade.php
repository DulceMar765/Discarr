<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel de Administración')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            font-family: system-ui, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #0d6efd;
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 24px 0;
        }

        .sidebar h4 {
            font-weight: 600;
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 12px 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
            background-color: #fff;
            min-height: 100vh;
        }

        .logout {
            margin-top: auto;
            padding: 0 24px;
        }

        .logout .btn {
            width: 100%;
            text-align: left;
            color: #fff;
            padding-left: 0;
        }

        .logout .btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .logout i {
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4><i class="bi bi-speedometer2"></i> Admin Discarr</h4>

        <!-- Dashboard redirige normal -->
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-layout-text-window-reverse"></i> Dashboard
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('employee.index') }}'); return false;">
            <i class="bi bi-person-badge-fill"></i> Empleados
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('supplier.index') }}'); return false;">
            <i class="bi bi-truck"></i> Proveedores
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('categories.index') }}'); return false;">
            <i class="bi bi-tags-fill"></i> Categorías
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('customer.index') }}'); return false;">
            <i class="bi bi-person-circle"></i> Clientes
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('material.index') }}'); return false;">
            <i class="bi bi-box-seam"></i> Material
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('/admin/appointments'); return false;">
            <i class="bi bi-calendar-check"></i> Reservaciones
        </a>
        <a class="nav-link" href="{{ route('admin.projects.index') }}">
            <i class="bi bi-folder-fill"></i> Proyectos
        </a>
        <a class="nav-link" href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;">
             <i class="bi bi-calendar-plus"></i> Vacaciones
        </a>


        <div class="logout mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-white">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="admin-content">
        @hasSection('content')
            @yield('content') <!-- Renderiza contenido estático -->
        @else
            @yield('main-content') <!-- Renderiza contenido dinámico -->
        @endif
    </div>

    <!-- Bootstrap y JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- AJAX para cargar secciones -->
    <script>
        function loadAdminSection(url) {
            $('#admin-content').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Limpiar cualquier script previo
                    $('#admin-content').empty();
                    
                    // Insertar el nuevo contenido
                    $('#admin-content').html(response);
                    
                    // Ejecutar scripts después de cargar el contenido
                    setTimeout(function() {
                        try {
                            // Inicializar calendario si existe la función
                            if (typeof initCalendar === 'function') {
                                initCalendar();
                            }
                            
                            // Ejecutar otros scripts que puedan necesitar inicialización
                            // Aquí se pueden añadir más inicializaciones si es necesario
                        } catch (e) {
                            console.error('Error al inicializar scripts:', e);
                        }
                    }, 300);
                },
                error: function(xhr) {
                    $('#admin-content').html('<div class="alert alert-danger">Error al cargar el contenido.</div>');
                }
            });
        }
    </script>
</body>
</html>
