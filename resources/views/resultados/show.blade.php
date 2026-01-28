@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Resultado del Partido: {{ $partido->nombre }}</h1>

            @if($partido->resultado)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <h2 class="text-lg font-semibold mb-2">Equipo 1</h2>
                        <div class="text-4xl font-bold text-blue-600">{{ $partido->resultado->marcador_equipo1 }}</div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-lg font-semibold mb-2">Equipo 2</h2>
                        <div class="text-4xl font-bold text-green-600">{{ $partido->resultado->marcador_equipo2 }}</div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Eventos del Partido</h3>
                    <div class="space-y-2">
                        @foreach($partido->resultado->eventos as $evento)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                <div>
                                    <span class="font-medium">{{ $evento->jugador->name }}</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ ucfirst(str_replace('_', ' ', $evento->tipo)) }}</span>
                                    @if($evento->descripcion)
                                        <span class="text-sm text-gray-500 ml-2">({{ $evento->descripcion }})</span>
                                    @endif
                                </div>
                                <span class="text-sm font-mono">{{ $evento->minuto }}'</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($puedeValidar && $partido->resultado->estado === 'pendiente_validacion')
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Validar Resultado</h3>
                        <form action="{{ route('resultados.validar', $partido->resultado->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="aceptar" value="1" class="form-radio">
                                    <span class="ml-2">Aceptar resultado</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="aceptar" value="0" class="form-radio">
                                    <span class="ml-2">Rechazar resultado</span>
                                </label>
                            </div>
                            <div>
                                <label for="notas" class="block text-sm font-medium text-gray-700">Notas (opcional)</label>
                                <textarea name="notas" id="notas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                                Enviar Validación
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-6">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($partido->resultado->estado === 'validado') bg-green-100 text-green-800
                        @elseif($partido->resultado->estado === 'rechazado') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $partido->resultado->estado)) }}
                    </span>
                </div>
            @else
                <p class="text-gray-600">El resultado aún no ha sido registrado por el árbitro.</p>
            @endif
        </div>
    </div>
</div>
@endsection