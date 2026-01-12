<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jugador Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 0px;
            --primary-green: #1a5f3f;
            --secondary-green: #2d8659;
            --light-green: #3ba76d;
            --accent-green: #4ecb8f;
            --bg-light: #f0f7f4;
            --text-dark: #1a3a2e;
        }

        body {
            overflow-x: hidden;
            background: var(--bg-light);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(26, 95, 63, 0.2);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-logo {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.1);
        }

        .sidebar-logo img {
            max-width: 150px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 14px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .sidebar-menu .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar-menu .nav-link.active {
            background: var(--accent-green);
            color: #fff;
            box-shadow: 0 4px 10px rgba(78, 203, 143, 0.3);
        }

        .sidebar-menu .nav-link i {
            font-size: 1.3rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: var(--bg-light);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-navbar {
            background: linear-gradient(135deg, #fff 0%, #f8fffe 100%);
            padding: 18px 30px;
            box-shadow: 0 2px 10px rgba(26, 95, 63, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--accent-green);
        }

        .menu-toggle {
            background: var(--light-green);
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            background: var(--secondary-green);
            transform: scale(1.05);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-menu .user-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .logout-btn {
            background: var(--primary-green);
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: var(--secondary-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(26, 95, 63, 0.3);
        }

        .content-wrapper {
            padding: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .overlay.show {
            display: block;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="overlay" id="overlay"></div>
    
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/page/logo.png') }}" alt="Logo">
        </div>
        
        <nav class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('jugador.dashboard') ? 'active' : '' }}" href="{{ route('jugador.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('jugador.perfil') ? 'active' : '' }}" href="{{ route('jugador.perfil') }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <main class="main-content" id="mainContent">
        <div class="top-navbar">
            <button class="menu-toggle" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="user-menu">
                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
        
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');
        const overlay = document.getElementById('overlay');
        
        menuToggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
