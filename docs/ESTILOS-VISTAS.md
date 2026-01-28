# Guía de Estilos y Estructura de Vistas

## 📋 Índice
- [Introducción](#introducción)
- [Paleta de Colores](#paleta-de-colores)
- [Estructura de Layouts](#estructura-de-layouts)
- [Vistas por Rol](#vistas-por-rol)
- [Componentes Reutilizables](#componentes-reutilizables)
- [Convenciones de Código](#convenciones-de-código)

---

## 🎨 Introducción

Este documento describe la estructura de estilos y vistas implementadas en el sistema. Todos los módulos deben seguir estas convenciones para mantener la consistencia visual y de código en toda la aplicación.

---

## 🎨 Paleta de Colores

### Colores Principales (Verde)
```css
--primary-green: #1a5f3f;      /* Verde oscuro principal */
--secondary-green: #2d8659;    /* Verde medio */
--light-green: #3ba76d;        /* Verde claro */
--accent-green: #4ecb8f;       /* Verde acento/resaltado */
--bg-light: #f0f7f4;           /* Fondo claro */
--text-dark: #1a3a2e;          /* Texto oscuro */
```

### Colores Secundarios
```css
/* Amarillo (Tarjetas Amarillas) */
--warning-yellow: #f59e0b;
--warning-yellow-light: #fbbf24;

/* Rojo (Tarjetas Rojas) */
--danger-red: #dc2626;
--danger-red-light: #ef4444;

/* Púrpura (Puntualidad) */
--purple: #8b5cf6;
--purple-light: #a78bfa;

/* Índigo (Posiciones) */
--indigo: #6366f1;
--indigo-light: #8b5cf6;
```

---

## 🏗️ Estructura de Layouts

### Layout Base: `layouts/jugador.blade.php`

Todos los layouts de roles (jugador, cancha, árbitro, admin) siguen la misma estructura:

```
┌─────────────────────────────────────┐
│         SIDEBAR (Fixed)             │
│  - Logo                             │
│  - Menú de navegación               │
│  - Enlaces por rol                  │
└─────────────────────────────────────┘
         │
         ├─────────────────────────────┐
         │    TOP NAVBAR               │
         │  - Toggle menú              │
         │  - Usuario                  │
         │  - Logout                   │
         ├─────────────────────────────┤
         │    CONTENT AREA             │
         │  - @yield('content')        │
         │                             │
         └─────────────────────────────┘
```

### Características del Layout

#### Sidebar
- **Ancho**: 250px (desktop), colapsable en móvil
- **Fondo**: Gradiente verde (`linear-gradient(180deg, #1a5f3f 0%, #2d8659 100%)`)
- **Posición**: Fixed, altura completa (100vh)
- **Sombra**: `4px 0 15px rgba(26, 95, 63, 0.2)`

#### Top Navbar
- **Fondo**: Gradiente blanco (`linear-gradient(135deg, #fff 0%, #f8fffe 100%)`)
- **Borde inferior**: 2px solid del color accent-green
- **Padding**: 18px 30px
- **Sombra**: `0 2px 10px rgba(26, 95, 63, 0.08)`

#### Content Area
- **Margen izquierdo**: 250px (ajustable cuando sidebar colapsa)
- **Fondo**: `#f0f7f4` (bg-light)
- **Padding**: 30px

---

## 👥 Vistas por Rol

### 1. Jugador (`resources/views/jugador/`)

**Layout**: `layouts.jugador`

**Vistas disponibles**:
- `dashboard.blade.php` - Panel principal del jugador
- `perfil.blade.php` - Perfil del jugador
- `crear-encuentro.blade.php` - Crear nuevo encuentro
- `partidos.blade.php` - Lista de partidos
- `partidos-disponibles.blade.php` - Partidos disponibles para inscripción

**Menú de navegación**:
```php
- Dashboard (bi-speedometer2)
- Mi Perfil (bi-person-circle)
- Crear Encuentro (bi-plus-circle-fill)
- Partidos (bi-trophy-fill)
- Estadísticas (bi-bar-chart-line-fill)
```

**Ruta del layout**: `@extends('layouts.jugador')`

---

### 2. Cancha (`resources/views/cancha/`)

**Layout**: `layouts.cancha`

**Vistas disponibles**:
- `dashboard.blade.php` - Panel principal de la cancha
- `perfil.blade.php` - Perfil de la cancha
- `crear-encuentro.blade.php` - Crear encuentro en la cancha
- `partidos.blade.php` - Partidos de la cancha
- `equipos-conformados.blade.php` - Equipos conformados

**Menú de navegación**:
```php
- Dashboard (bi-speedometer2)
- Mi Cancha (bi-building)
- Crear Encuentro (bi-plus-circle-fill)
- Partidos (bi-trophy-fill)
- Equipos (bi-people-fill)
```

**Ruta del layout**: `@extends('layouts.cancha')`

---

### 3. Árbitro (`resources/views/arbitro/`)

**Layout**: `layouts.arbitro`

**Vistas disponibles**:
- `dashboard.blade.php` - Panel principal del árbitro
- `crear-encuentro.blade.php` - Crear encuentro como árbitro
- `partidos-disponibles.blade.php` - Partidos disponibles para arbitrar

**Menú de navegación**:
```php
- Dashboard (bi-speedometer2)
- Mi Perfil (bi-person-circle)
- Crear Encuentro (bi-plus-circle-fill)
- Partidos Disponibles (bi-calendar-check)
```

**Ruta del layout**: `@extends('layouts.arbitro')`

---

### 4. Admin (`resources/views/admin/`)

**Layout**: `layouts.app` (genérico) o crear `layouts.admin`

**Vistas disponibles**:
- `dashboard.blade.php` - Panel de administración
- `recargas.blade.php` - Gestión de recargas

**Nota**: Se recomienda crear un layout específico `layouts.admin` siguiendo la misma estructura que los otros roles.

---

## 🧩 Componentes Reutilizables

### Tarjetas de Estadísticas (`.stat-card`)

```html
<div class="stat-card [green|light-green|accent|yellow|red]">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="stat-icon me-3">
                <i class="bi bi-[icono]"></i>
            </div>
            <div>
                <p class="stat-value">{{ $valor }}</p>
                <p class="stat-label">Etiqueta</p>
            </div>
        </div>
    </div>
</div>
```

**Variantes de color**:
- `.green` - Verde oscuro (partidos jugados, principal)
- `.light-green` - Verde claro (goles, positivo)
- `.accent` - Verde acento (destacados)
- `.yellow` - Amarillo (tarjetas amarillas)
- `.red` - Rojo (tarjetas rojas)

**Estilos CSS**:
```css
.stat-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s;
    background: #fff;
    box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    background: rgba(255,255,255,0.2);
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 0;
}
```

---

### Títulos de Sección (`.section-title`)

```html
<h2 class="section-title">
    <i class="bi bi-[icono] me-2"></i>
    Título de la Sección
</h2>
```

**Estilos CSS**:
```css
.section-title {
    color: #1a5f3f;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #4ecb8f;
}
```

---

### Tarjetas de Partido (`.partido-card`)

```html
<div class="partido-card">
    <div class="card-body">
        <!-- Contenido del partido -->
    </div>
</div>
```

**Estilos CSS**:
```css
.partido-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    border-left: 4px solid #3ba76d;
}

.partido-card:hover {
    box-shadow: 0 6px 20px rgba(26, 95, 63, 0.15);
    transform: translateX(5px);
}
```

---

### Botones Principales

```html
<button class="btn-inscribir">Inscribirse</button>
```

**Estilos CSS**:
```css
.btn-inscribir {
    background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
    border: none;
    color: white;
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s;
    font-weight: 500;
}

.btn-inscribir:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(59, 167, 109, 0.4);
}
```

---

### Sistema de Calificación con Estrellas

```html
<div class="d-flex">
    @for($i = 1; $i <= 5; $i++)
        <i class="bi bi-star-fill {{ $i <= round($promedio) ? 'star-rating' : 'star-rating empty' }}"></i>
    @endfor
</div>
```

**Estilos CSS**:
```css
.star-rating {
    color: #fbbf24;
}

.star-rating.empty {
    color: rgba(255,255,255,0.3);
}
```

---

## 📝 Convenciones de Código

### 1. Estructura de Archivos Blade

```blade
@extends('layouts.[rol]')

@section('title', 'Título de la Página')

@section('content')
<style>
    /* Estilos específicos de la vista */
</style>

<div class="container-fluid">
    <!-- Contenido de la vista -->
</div>
@endsection
```

### 2. Nomenclatura de Clases CSS

- **Componentes**: `.nombre-componente` (kebab-case)
- **Modificadores**: `.nombre-componente.modificador`
- **Estados**: `.nombre-componente:hover`, `.nombre-componente.active`

**Ejemplos**:
```css
.stat-card { }
.stat-card.green { }
.stat-card:hover { }
.sidebar-menu .nav-link.active { }
```

### 3. Uso de Bootstrap Icons

Todos los iconos utilizan la librería **Bootstrap Icons**:

```html
<i class="bi bi-nombre-icono"></i>
```

**Iconos comunes**:
- Dashboard: `bi-speedometer2`
- Perfil: `bi-person-circle`
- Partidos: `bi-trophy-fill`
- Estadísticas: `bi-bar-chart-line-fill`
- Crear: `bi-plus-circle-fill`
- Goles: `bi-bullseye`
- Tarjetas: `bi-card-text`, `bi-x-octagon-fill`
- Estrellas: `bi-star-fill`

### 4. Grid System (Bootstrap 5)

Usar el sistema de grid de Bootstrap para layouts responsivos:

```html
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <!-- Contenido -->
    </div>
</div>
```

**Breakpoints**:
- `col-` - Extra small (<576px)
- `col-sm-` - Small (≥576px)
- `col-md-` - Medium (≥768px)
- `col-lg-` - Large (≥992px)
- `col-xl-` - Extra large (≥1200px)

### 5. Espaciado Consistente

Usar las clases de utilidad de Bootstrap para espaciado:

```html
<!-- Margin -->
<div class="mb-4">  <!-- margin-bottom: 1.5rem -->
<div class="mt-3">  <!-- margin-top: 1rem -->

<!-- Padding -->
<div class="p-4">   <!-- padding: 1.5rem -->
<div class="py-3">  <!-- padding-y: 1rem -->

<!-- Gap (para flexbox/grid) -->
<div class="row g-4">  <!-- gap: 1.5rem -->
```

### 6. Transiciones y Animaciones

Todas las transiciones deben ser suaves y consistentes:

```css
transition: all 0.3s;
```

**Efectos hover comunes**:
```css
/* Elevación */
transform: translateY(-5px);

/* Desplazamiento lateral */
transform: translateX(5px);

/* Escala */
transform: scale(1.05);

/* Sombra */
box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
```

---

## 🔄 Implementación en Nuevos Módulos

### Checklist para Nuevas Vistas

1. **Seleccionar el layout correcto**
   ```blade
   @extends('layouts.[jugador|cancha|arbitro|admin]')
   ```

2. **Definir el título de la página**
   ```blade
   @section('title', 'Título - Rol')
   ```

3. **Incluir estilos específicos** (si es necesario)
   ```blade
   <style>
       /* Estilos específicos */
   </style>
   ```

4. **Usar componentes reutilizables**
   - Tarjetas de estadísticas (`.stat-card`)
   - Títulos de sección (`.section-title`)
   - Tarjetas de partido (`.partido-card`)
   - Botones principales (`.btn-inscribir`)

5. **Mantener la paleta de colores**
   - Usar variables CSS definidas
   - Respetar los gradientes establecidos

6. **Aplicar el sistema de grid**
   - Usar `container-fluid` o `container`
   - Implementar `row` y `col-*`
   - Aplicar gaps consistentes (`g-4`)

7. **Agregar iconos apropiados**
   - Usar Bootstrap Icons
   - Mantener consistencia con iconos existentes

8. **Implementar efectos hover**
   - Transiciones suaves (0.3s)
   - Elevación o desplazamiento
   - Cambios de sombra

---

## 📱 Responsividad

### Breakpoints Principales

```css
/* Mobile First */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .main-content {
        margin-left: 0;
    }
}

/* Tablet */
@media (min-width: 768px) and (max-width: 992px) {
    /* Ajustes para tablet */
}

/* Desktop */
@media (min-width: 992px) {
    /* Ajustes para desktop */
}
```

### Clases Responsivas de Bootstrap

```html
<!-- Ocultar en móvil -->
<span class="d-none d-md-inline">Texto</span>

<!-- Columnas responsivas -->
<div class="col-12 col-md-6 col-lg-4">
    <!-- Contenido -->
</div>
```

---

## 🎯 Ejemplos de Implementación

### Ejemplo 1: Vista de Estadísticas

```blade
@extends('layouts.jugador')

@section('title', 'Estadísticas - Jugador')

@section('content')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s;
        background: #fff;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
    }
    
    .stat-card.green {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-bar-chart-line-fill me-2"></i>
                Estadísticas
            </h1>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card green">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <p class="stat-value">{{ $partidos }}</p>
                            <p class="stat-label">Partidos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🚀 Próximos Pasos

1. **Crear layout específico para Admin** (`layouts/admin.blade.php`)
2. **Estandarizar todas las vistas de Admin** para usar el nuevo layout
3. **Crear componentes Blade reutilizables** para tarjetas y elementos comunes
4. **Implementar un archivo CSS global** con todas las clases reutilizables
5. **Documentar nuevos componentes** a medida que se crean

---

## 📞 Contacto y Soporte

Para dudas o sugerencias sobre los estilos y estructura de vistas, contactar al equipo de desarrollo.

---

**Última actualización**: {{ date('Y-m-d') }}
**Versión**: 1.0
