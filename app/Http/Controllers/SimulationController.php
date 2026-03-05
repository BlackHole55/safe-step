<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Step;
use App\Models\SimulationSession;
use App\Services\SimulationService;

class SimulationController extends Controller
{
    protected $simulationService;

    public function __construct(SimulationService $service)
    {
        $this->simulationService = $service;
    }

    public function start(Request $request)
    {
        $firstStep = Step::where('slug', 'intro')->with('options')->firstOrFail();

        // Hardcoded for now
        $maxPossibleScore = 90;

        $session = SimulationSession::create([
            'current_step_id' => $firstStep->id,
            'total_score' => 0,
            'journey_log' => [],
            'max_possible_score' => $maxPossibleScore
        ]);

        return response()->json([
            'session_uuid' => $session->uuid,
            'step' => $firstStep
        ]);
    }

    public function answer(Request $request)
    {
        $validated = $request->validate([
            'session_uuid' => 'required|uuid|exists:simulation_sessions,uuid',
            'option_id' => 'required|exists:options,id'
        ]);

        $result = $this->simulationService->processAnswer(
            $validated['session_uuid'],
            $validated['option_id']
        );

        return response()->json($result);
    }
}