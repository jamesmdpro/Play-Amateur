@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Calificaciones del Partido: {{ $partido->nombre }}</h1>

            @if($puedeCalificar)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Calificar Participantes</h2>

                    @if($partido->arbitro && auth()->id() !== $partido->arbitro->id)
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <h3 class="font-medium text-gray-800">Calificar Árbitro: {{ $partido->arbitro->name }}</h3>
                            <form action="{{ route('ratings.store') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="calificado_id" value="{{ $partido->arbitro->id }}">
                                <input type="hidden" name="partido_id" value="{{ $partido->id }}">
                                <input type="hidden" name="tipo" value="jugador_arbitro">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">Puntuación:</span>
                                    <select name="puntuacion" class="form-select rounded border-gray-300">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <div class="mt-2">
                                    <textarea name="comentario" placeholder="Comentario opcional" rows="2" class="w-full border-gray-300 rounded"></textarea>
                                </div>
                                <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">
                                    Calificar
                                </button>
                            </form>
                        </div>
                    @endif

                    @foreach($partido->jugadores as $jugador)
                        @if($jugador->id !== auth()->id())
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <h3 class="font-medium text-gray-800">Calificar Jugador: {{ $jugador->name }}</h3>
                                <form action="{{ route('ratings.store') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="calificado_id" value="{{ $jugador->id }}">
                                    <input type="hidden" name="partido_id" value="{{ $partido->id }}">
                                    <input type="hidden" name="tipo" value="{{ $partido->arbitro && auth()->id() === $partido->arbitro->id ? 'arbitro_jugador' : 'jugador_jugador' }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">Puntuación:</span>
                                        <select name="puntuacion" class="form-select rounded border-gray-300">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                    <div class="mt-2">
                                        <textarea name="comentario" placeholder="Comentario opcional" rows="2" class="w-full border-gray-300 rounded"></textarea>
                                    </div>
                                    <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">
                                        Calificar
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Calificaciones Recibidas</h2>
                <div class="space-y-4">
                    @foreach($ratings as $rating)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-gray-800">
                                        {{ $rating->calificador->name }} calificó a {{ $rating->calificado->name }}
                                    </div>
                                    <div class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $rating->tipo)) }}</div>
                                    @if($rating->comentario)
                                        <div class="text-sm text-gray-700 mt-1">{{ $rating->comentario }}</div>
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-800 mr-2">{{ $rating->puntuacion }}</span>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $rating->puntuacion ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection