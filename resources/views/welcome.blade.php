<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Amateur - Encuentra tu equipo</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-logo">⚽ Play Amateur</div>
            <div class="navbar-links">
                <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-background">
            <img src="{{ asset('images/players/jugador3.jpeg') }}" alt="Fútbol" class="hero-image">
        </div>
        <div class="hero-content">
            <h1 class="hero-title">Encuentra Tu Equipo</h1>
            <p class="hero-subtitle">Conecta con jugadores, canchas y árbitros en tu ciudad</p>
            <a href="{{ route('register') }}" class="hero-cta">Únete Ahora</a>
        </div>
    </section>

    <section class="section section-players">
        <div class="section-container">
            <div class="section-content">
                <div class="section-text">
                    <h2>¿Eres Jugador Amateur?</h2>
                    <p>
                        ¿No tienes equipo? ¡Somos tu solución! Regístrate y conecta con otros jugadores 
                        de tu nivel en tu ciudad. Participa en partidos organizados, forma equipos y 
                        disfruta del deporte que amas.
                    </p>
                    <ul class="benefits-list">
                        <li>Encuentra jugadores de tu nivel</li>
                        <li>Únete a partidos en tu zona</li>
                        <li>Crea tu perfil deportivo</li>
                        <li>Gestiona tu disponibilidad</li>
                    </ul>
                    <a href="{{ route('register') }}" class="hero-cta">Registrarme como Jugador</a>
                </div>
                <div class="section-image-placeholder">
                    <img src="{{ asset('images/players/jugador_1.jpeg') }}" alt="Jugadores en acción" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                </div>
            </div>
        </div>
    </section>

    <section class="section section-venues">
        <div class="section-container">
            <div class="section-content">
                <div class="section-image-placeholder">
                    <img src="{{ asset('images/venues/venue.jpeg') }}" alt="Jugadores en acción" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                </div>
                <div class="section-text">
                    <h2>Para Canchas y Árbitros</h2>
                    <p>
                        ¿Tienes una cancha que queda vacía? ¿Eres árbitro y buscas partidos? 
                        Únete a nuestra plataforma y maximiza tus ingresos.
                    </p>
                    <ul class="benefits-list">
                        <li>Programa espacios disponibles</li>
                        <li>Recibe pagos automáticos</li>
                        <li>Gestiona reservas fácilmente</li>
                        <li>Aumenta tus ingresos</li>
                    </ul>
                    <p style="margin-top: 20px;">
                        <strong>Árbitros:</strong> Pita partidos, gana dinero y mantente activo en el deporte.
                    </p>
                    <a href="{{ route('register') }}" class="hero-cta">Registrar Cancha/Árbitro</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section contact-section">
        <div class="section-container">
            <h2>Contáctanos</h2>
            <p style="font-size: 1.2rem; margin-bottom: 40px;">
                ¿Tienes preguntas? Estamos aquí para ayudarte
            </p>
            <div class="contact-info">
                <div class="contact-item">
                    <h3>📧 Email</h3>
                    <p>info@playamateur.com</p>
                </div>
                <div class="contact-item">
                    <h3>📱 Teléfono</h3>
                    <p>+57 300 123 4567</p>
                </div>
                <div class="contact-item">
                    <h3>📍 Ubicación</h3>
                    <p>Colombia</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 Play Amateur. Todos los derechos reservados.</p>
        <p>Conectando jugadores, canchas y árbitros</p>
    </footer>
</body>
</html>
