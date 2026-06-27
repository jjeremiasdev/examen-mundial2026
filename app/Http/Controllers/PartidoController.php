<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Seleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartidoController extends Controller
{
    public function __construct()
    {
        // Protege con JWT y restringe escritura solo a ADMIN
        $this->middleware('auth:api');
        $this->middleware('rol.auth')->only(['store', 'update', 'destroy']);
    }

    /**
     * Listar todos los partidos.
     * GET /api/partidos
     */
    public function index()
    {
        return response()->json(Partido::with(['seleccionLocal', 'seleccionVisitante'])->get(), 200);
    }

    /**
     * Obtener un partido específico.
     * GET /api/partidos/{id}
     */
    public function show($id)
    {
        $partido = Partido::with(['seleccionLocal', 'seleccionVisitante'])->find($id);
        if (!$partido) {
            return response()->json(['error' => 'Partido no encontrado'], 404);
        }
        return response()->json($partido, 200);
    }

    /**
     * Filtrar partidos por fase.
     * GET /api/partidos/fase/{fase}
     */
    public function filtrarPorFase($fase)
    {
        $faseUpper = strtoupper($fase);
        if (!in_array($faseUpper, ['GRUPOS', 'OCTAVOS', 'CUARTOS', 'SEMIFINAL', 'FINAL'])) {
            return response()->json(['error' => 'Fase inválida'], 400);
        }

        $partidos = Partido::with(['seleccionLocal', 'seleccionVisitante'])
            ->where('fase', $faseUpper)
            ->get();

        return response()->json($partidos, 200);
    }

    /**
     * Registrar un nuevo partido (Solo ADMIN).
     * POST /api/partidos
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seleccion_local_id' => 'required|integer|exists:selecciones,id', // Regla: Validar que exista
            'seleccion_visitante_id' => 'required|integer|exists:selecciones,id',
            'fecha' => 'required|date',
            'estadio' => 'required|string|max:255',
            'fase' => 'required|string|in:GRUPOS,OCTAVOS,CUARTOS,SEMIFINAL,FINAL', // Regla: Fases válidas
            'goles_local' => 'integer|min:0', // Regla: No negativos
            'goles_visitante' => 'integer|min:0',
            'estado' => 'string|in:PROGRAMADO,EN_JUEGO,FINALIZADO' // Regla: Estados válidos
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Regla: No jugar contra sí misma
        if ($request->seleccion_local_id == $request->seleccion_visitante_id) {
            return response()->json(['error' => 'Una selección no puede jugar contra sí misma.'], 422);
        }

        $data = $request->all();

        // Regla: Si ya se mandan goles, cambia automáticamente a FINALIZADO
        if ($request->has('goles_local') || $request->has('goles_visitante')) {
            $data['estado'] = 'FINALIZADO';
        }

        $partido = Partido::create($data);
        return response()->json(['message' => 'Partido registrado', 'data' => $partido], 201);
    }

    /**
     * Actualizar resultado/datos de un partido (Solo ADMIN).
     * PUT /api/partidos/{id}
     */
    public function update(Request $request, $id)
    {
        $partido = Partido::find($id);
        if (!$partido) {
            return response()->json(['error' => 'Partido no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'seleccion_local_id' => 'integer|exists:selecciones,id',
            'seleccion_visitante_id' => 'integer|exists:selecciones,id',
            'fecha' => 'date',
            'estadio' => 'string|max:255',
            'fase' => 'string|in:GRUPOS,OCTAVOS,CUARTOS,SEMIFINAL,FINAL',
            'goles_local' => 'integer|min:0',
            'goles_visitante' => 'integer|min:0',
            'estado' => 'string|in:PROGRAMADO,EN_JUEGO,FINALIZADO'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();

        // Validar contra sí misma en caso de que intenten actualizar los IDs de los equipos
        $local = $request->seleccion_local_id ?? $partido->seleccion_local_id;
        $visitante = $request->seleccion_visitante_id ?? $partido->seleccion_visitante_id;
        if ($local == $visitante) {
            return response()->json(['error' => 'Una selección no puede jugar contra sí misma.'], 422);
        }

        // Regla: Al registrar el resultado, cambia automáticamente a FINALIZADO
        if ($request->has('goles_local') || $request->has('goles_visitante')) {
            $data['estado'] = 'FINALIZADO';
        }

        $partido->update($data);
        return response()->json(['message' => 'Partido actualizado', 'data' => $partido], 200);
    }

    /**
     * Eliminar un partido (Solo ADMIN).
     * DELETE /api/partidos/{id}
     */
    public function destroy($id)
    {
        $partido = Partido::find($id);
        if (!$partido) {
            return response()->json(['error' => 'Partido no encontrado'], 404);
        }
        $partido->delete();
        return response()->json(['message' => 'Partido eliminado exitosamente'], 200);
    }

    /**
     * Tabla de posiciones por grupo
     * GET /api/grupos/{grupo}/tabla
     */
    public function tablaPosiciones($grupo)
    {
        // 1. Obtener las selecciones de ese grupo
        $selecciones = Seleccion::where('grupo', strtoupper($grupo))->get();

        $tabla = [];

        foreach ($selecciones as $seleccion) {
            $pj = 0; $pg = 0; $pe = 0; $pp = 0; $gf = 0; $gc = 0; $pts = 0;

            // Partidos de local finalizados
            $partidosLocal = Partido::where('seleccion_local_id', $seleccion->id)
                ->where('estado', 'FINALIZADO')
                ->get();

            foreach ($partidosLocal as $partido) {
                $pj++;
                $gf += $partido->goles_local;
                $gc += $partido->goles_visitante;

                if ($partido->goles_local > $partido->goles_visitante) {
                    $pg++; $pts += 3;
                } elseif ($partido->goles_local == $partido->goles_visitante) {
                    $pe++; $pts += 1;
                } else {
                    $pp++;
                }
            }

            // Partidos de visitante finalizados
            $partidosVisitante = Partido::where('seleccion_visitante_id', $seleccion->id)
                ->where('estado', 'FINALIZADO')
                ->get();

            foreach ($partidosVisitante as $partido) {
                $pj++;
                $gf += $partido->goles_visitante;
                $gc += $partido->goles_local;

                if ($partido->goles_visitante > $partido->goles_local) {
                    $pg++; $pts += 3;
                } elseif ($partido->goles_visitante == $partido->goles_local) {
                    $pe++; $pts += 1;
                } else {
                    $pp++;
                }
            }

            $dg = $gf - $gc; // Diferencia de goles

            $tabla[] = [
                'id' => $seleccion->id,
                'nombre' => $seleccion->nombre,
                'PJ' => $pj,
                'PG' => $pg,
                'PE' => $pe,
                'PP' => $pp,
                'GF' => $gf,
                'GC' => $gc,
                'DG' => $dg,
                'PTS' => $pts
            ];
        }

        // Regla: Ordenar por Puntos (PTS) descendente, Diferencia de Goles (DG) descendente, y Goles a Favor (GF) descendente
        usort($tabla, function ($a, $b) {
            if ($b['PTS'] !== $a['PTS']) {
                return $b['PTS'] <=> $a['PTS'];
            }
            if ($b['DG'] !== $a['DG']) {
                return $b['DG'] <=> $a['DG'];
            }
            return $b['GF'] <=> $a['GF'];
        });

        return response()->json($tabla, 200);
    }
}