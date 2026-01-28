<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Partido;
use App\Models\Inscripcion;
use App\Models\Notificacion;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidoCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected $cancha;
    protected $jugador1;
    protected $jugador2;
    protected $jugador3;
    protected $arbitro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cancha = User::factory()->create([
            'rol' => 'cancha',
            'name' => 'Cancha Test',
            'email' => 'cancha@test.com',
            'wallet' => 0,
        ]);

        $this->jugador1 = User::factory()->create([
            'rol' => 'jugador',
            'name' => 'Jugador 1',
            'email' => 'jugador1@test.com',
            'wallet' => 100000,
            'posicion' => 'ataque',
            'nivel' => 2,
        ]);

        $this->jugador2 = User::factory()->create([
            'rol' => 'jugador',
            'name' => 'Jugador 2',
            'email' => 'jugador2@test.com',
            'wallet' => 100000,
            'posicion' => 'defensa',
            'nivel' => 3,
        ]);

        $this->jugador3 = User::factory()->create([
            'rol' => 'jugador',
            'name' => 'Jugador 3',
            'email' => 'jugador3@test.com',
            'wallet' => 100000,
            'posicion' => 'medio',
            'nivel' => 1,
        ]);

        $this->arbitro = User::factory()->create([
            'rol' => 'arbitro',
            'name' => 'Árbitro Test',
            'email' => 'arbitro@test.com',
            'wallet' => 0,
        ]);
    }

    public function test_cancha_puede_crear_partido_sin_arbitro()
    {
        $response = $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Amistoso',
                'descripcion' => 'Partido de prueba sin árbitro',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Central',
                'cupos_totales' => 14,
                'cupos_suplentes' => 4,
                'costo' => 200000,
                'con_arbitro' => false,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'partido' => [
                    'id',
                    'nombre',
                    'descripcion',
                    'fecha_hora',
                    'ubicacion',
                    'cupos_totales',
                    'cupos_suplentes',
                    'costo',
                    'con_arbitro',
                    'costo_por_jugador',
                    'creador_id',
                ]
            ]);

        $this->assertDatabaseHas('partidos', [
            'nombre' => 'Partido Amistoso',
            'con_arbitro' => false,
            'creador_id' => $this->cancha->id,
        ]);

        $partido = Partido::where('nombre', 'Partido Amistoso')->first();
        $this->assertEqualsWithDelta(200000 / 18, $partido->costo_por_jugador, 0.01);
    }

    public function test_cancha_puede_crear_partido_con_arbitro()
    {
        $response = $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Profesional',
                'descripcion' => 'Partido de prueba con árbitro',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Principal',
                'cupos_totales' => 14,
                'cupos_suplentes' => 4,
                'costo' => 200000,
                'con_arbitro' => true,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('partidos', [
            'nombre' => 'Partido Profesional',
            'con_arbitro' => true,
        ]);

        $partido = Partido::where('nombre', 'Partido Profesional')->first();
        $this->assertEqualsWithDelta((200000 + 100000) / 18, $partido->costo_por_jugador, 0.01);
    }

    public function test_notificaciones_se_envian_a_jugadores_cuando_se_crea_partido()
    {
        $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Test Notificaciones',
                'descripcion' => 'Test de notificaciones',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Test',
                'cupos_totales' => 14,
                'cupos_suplentes' => 4,
                'costo' => 200000,
                'con_arbitro' => false,
            ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->jugador1->id,
            'tipo' => 'partido_disponible',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->jugador2->id,
            'tipo' => 'partido_disponible',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->jugador3->id,
            'tipo' => 'partido_disponible',
        ]);
    }

    public function test_notificaciones_se_envian_a_arbitros_cuando_partido_requiere_arbitro()
    {
        $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Con Árbitro',
                'descripcion' => 'Test notificación árbitro',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Test',
                'cupos_totales' => 14,
                'cupos_suplentes' => 4,
                'costo' => 200000,
                'con_arbitro' => true,
            ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->arbitro->id,
            'tipo' => 'partido_requiere_arbitro',
        ]);

        $notificacion = Notificacion::where('user_id', $this->arbitro->id)
            ->where('tipo', 'partido_requiere_arbitro')
            ->first();

        $this->assertNotNull($notificacion);
        $this->assertStringContainsString('Partido Con Árbitro', $notificacion->mensaje);
    }

    public function test_jugador_puede_inscribirse_en_partido()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Inscripción',
            'descripcion' => 'Test inscripción',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'abierto',
        ]);

        $response = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('inscripciones', [
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador1->id,
            'equipo' => 1,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);
    }

    public function test_jugador_se_inscribe_como_suplente_cuando_equipo_esta_lleno()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Suplente',
            'descripcion' => 'Test suplente',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'abierto',
        ]);

        for ($i = 0; $i < 7; $i++) {
            Inscripcion::create([
                'partido_id' => $partido->id,
                'jugador_id' => User::factory()->create(['rol' => 'jugador', 'wallet' => 100000])->id,
                'equipo' => 1,
                'es_suplente' => false,
                'estado' => 'inscrito',
            ]);
        }

        $response = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $response->assertStatus(201);

        $inscripcion = Inscripcion::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugador1->id)
            ->first();

        $this->assertTrue($inscripcion->es_suplente);
    }

    public function test_jugador_no_puede_inscribirse_dos_veces_en_mismo_partido()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Duplicado',
            'descripcion' => 'Test duplicado',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'abierto',
        ]);

        $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $response = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Ya estás inscrito en este partido',
            ]);
    }

    public function test_jugador_con_sancion_no_puede_inscribirse()
    {
        $this->jugador1->sanciones()->create([
            'partido_id' => null,
            'numero_sancion' => 1,
            'dias_suspension' => 7,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(7),
            'monto_reactivacion' => 15000,
            'motivo' => 'Test sanción',
            'activa' => true,
        ]);

        $partido = Partido::create([
            'nombre' => 'Partido Test Sanción',
            'descripcion' => 'Test sanción',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'abierto',
        ]);

        $response = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_multiples_jugadores_pueden_inscribirse_en_diferentes_equipos()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Equipos',
            'descripcion' => 'Test equipos',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'abierto',
        ]);

        $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $this->actingAs($this->jugador2, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 2,
            ]);

        $this->actingAs($this->jugador3, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ]);

        $this->assertDatabaseHas('inscripciones', [
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador1->id,
            'equipo' => 1,
        ]);

        $this->assertDatabaseHas('inscripciones', [
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador2->id,
            'equipo' => 2,
        ]);

        $this->assertDatabaseHas('inscripciones', [
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador3->id,
            'equipo' => 1,
        ]);
    }

    public function test_partido_calcula_costo_por_jugador_correctamente_sin_arbitro()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Costo',
            'descripcion' => 'Test costo',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 10,
            'cupos_suplentes' => 2,
            'costo' => 120000,
            'con_arbitro' => false,
            'creador_id' => $this->cancha->id,
        ]);

        $partido->calcularCostoPorJugador();

        $this->assertEquals(10000, $partido->costo_por_jugador);
    }

    public function test_partido_calcula_costo_por_jugador_correctamente_con_arbitro()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Costo Árbitro',
            'descripcion' => 'Test costo árbitro',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 10,
            'cupos_suplentes' => 2,
            'costo' => 120000,
            'con_arbitro' => true,
            'creador_id' => $this->cancha->id,
        ]);

        $partido->calcularCostoPorJugador();

        $this->assertEqualsWithDelta((120000 + 100000) / 12, $partido->costo_por_jugador, 0.01);
    }

    public function test_partido_muestra_cupos_disponibles_correctamente()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Cupos',
            'descripcion' => 'Test cupos',
            'fecha_hora' => now()->addDays(7),
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
        ]);

        $this->assertEquals(14, $partido->cuposDisponibles());
        $this->assertEquals(4, $partido->cuposSuplentesDisponibles());

        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador1->id,
            'equipo' => 1,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);

        $this->assertEquals(13, $partido->cuposDisponibles());

        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador2->id,
            'equipo' => 1,
            'es_suplente' => true,
            'estado' => 'inscrito',
        ]);

        $this->assertEquals(13, $partido->cuposDisponibles());
        $this->assertEquals(3, $partido->cuposSuplentesDisponibles());
    }

    public function test_flujo_completo_creacion_partido_con_arbitro_e_inscripciones()
    {
        $response = $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Completo',
                'descripcion' => 'Test flujo completo',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Principal',
                'cupos_totales' => 14,
                'cupos_suplentes' => 4,
                'costo' => 200000,
                'con_arbitro' => true,
            ]);

        $response->assertStatus(201);
        $partido = Partido::where('nombre', 'Partido Completo')->first();

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->arbitro->id,
            'tipo' => 'partido_requiere_arbitro',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->jugador1->id,
            'tipo' => 'partido_disponible',
        ]);

        $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ])
            ->assertStatus(201);

        $this->actingAs($this->jugador2, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 2,
            ])
            ->assertStatus(201);

        $this->actingAs($this->jugador3, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ])
            ->assertStatus(201);

        $this->assertEquals(3, $partido->inscripciones()->count());
        $this->assertEquals(11, $partido->cuposDisponibles());
    }

    public function test_flujo_completo_creacion_partido_sin_arbitro_e_inscripciones()
    {
        $response = $this->actingAs($this->cancha, 'sanctum')
            ->postJson('/api/partidos', [
                'nombre' => 'Partido Sin Árbitro',
                'descripcion' => 'Test flujo sin árbitro',
                'fecha_hora' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'ubicacion' => 'Cancha Secundaria',
                'cupos_totales' => 10,
                'cupos_suplentes' => 2,
                'costo' => 120000,
                'con_arbitro' => false,
            ]);

        $response->assertStatus(201);
        $partido = Partido::where('nombre', 'Partido Sin Árbitro')->first();

        $notificacionesArbitro = Notificacion::where('user_id', $this->arbitro->id)
            ->where('tipo', 'partido_requiere_arbitro')
            ->where('data->partido_id', $partido->id)
            ->count();

        $this->assertEquals(0, $notificacionesArbitro);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $this->jugador1->id,
            'tipo' => 'partido_disponible',
        ]);

        $this->actingAs($this->jugador1, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ])
            ->assertStatus(201);

        $this->actingAs($this->jugador2, 'sanctum')
            ->postJson("/api/partidos/{$partido->id}/inscribirse", [
                'equipo' => 1,
            ])
            ->assertStatus(201);

        $this->assertEquals(2, $partido->inscripciones()->count());
        $this->assertEquals(8, $partido->cuposDisponibles());
        $this->assertEquals(10000, $partido->costo_por_jugador);
    }

    public function test_jugador_puede_calificar_dos_jugadores_maximo_por_partido()
    {
        $partido = Partido::create([
            'nombre' => 'Partido Test Ratings',
            'descripcion' => 'Test ratings limit',
            'fecha_hora' => now()->addDays(-1), // Match in the past
            'ubicacion' => 'Cancha Test',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 200000,
            'con_arbitro' => false,
            'costo_por_jugador' => 11111.11,
            'creador_id' => $this->cancha->id,
            'estado' => 'finalizado', // Match already finished
        ]);

        // Register players
        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador1->id,
            'equipo' => 1,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);

        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador2->id,
            'equipo' => 1,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);

        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugador3->id,
            'equipo' => 2,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);

        // Create additional players for rating
        $jugador4 = User::factory()->create([
            'rol' => 'jugador',
            'name' => 'Jugador 4',
            'email' => 'jugador4@test.com',
            'wallet' => 100000,
        ]);

        Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $jugador4->id,
            'equipo' => 2,
            'es_suplente' => false,
            'estado' => 'inscrito',
        ]);

        // Jugador1 rates Jugador2 (first rating)
        $response1 = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson('/api/ratings', [
                'calificado_id' => $this->jugador2->id,
                'partido_id' => $partido->id,
                'tipo' => 'jugador_jugador',
                'puntuacion' => 4,
                'comentario' => 'Buen partido',
            ]);

        $response1->assertStatus(200);

        // Jugador1 rates Jugador3 (second rating)
        $response2 = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson('/api/ratings', [
                'calificado_id' => $this->jugador3->id,
                'partido_id' => $partido->id,
                'tipo' => 'jugador_jugador',
                'puntuacion' => 5,
                'comentario' => 'Excelente',
            ]);

        $response2->assertStatus(200);

        // Jugador1 tries to rate Jugador4 (third rating - should fail)
        $response3 = $this->actingAs($this->jugador1, 'sanctum')
            ->postJson('/api/ratings', [
                'calificado_id' => $jugador4->id,
                'partido_id' => $partido->id,
                'tipo' => 'jugador_jugador',
                'puntuacion' => 3,
                'comentario' => 'Regular',
            ]);

        $response3->assertStatus(403)
            ->assertJson([
                'message' => 'Ya has alcanzado el límite de calificaciones para este partido (máximo 2)',
            ]);

        // Verify only 2 ratings were created
        $this->assertEquals(2, Rating::where('partido_id', $partido->id)
            ->where('calificador_id', $this->jugador1->id)
            ->where('tipo', 'jugador_jugador')
            ->count());
    }
}
