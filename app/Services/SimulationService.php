<?php

namespace App\Services;

use App\Models\SimulationSession;
use App\Models\Option;
use App\Models\Step;
use Carbon\Carbon;

class SimulationService 
{
    public function processAnswer(string $sessionUuid, int $optionId)
    {
        $session = SimulationSession::where('uuid', $sessionUuid)->with('currentStep')->firstOrFail();
        $option = Option::findOrFail($optionId);

        $isTimeout = false;
        if ($session->currentStep->time_limit) {
            $startTime = $session->updated_at;
            $now = Carbon::now();

            $gracePeriod = 2;
            $time_limit_grace = $session->currentStep->time_limit + $gracePeriod;

            if ($now->diffInSeconds($startTime) > $time_limit_grace) {
                $isTimeout = true;
            }
        }

        if ($isTimeout) {
            $failStep = Step::where('slug', 'failed')->first();
            $this->updateSession($session, $failStep->id, 0, 'Время вышло! В реальности секунды решают всё.', false, true);
            
            return [
                'is_finished' => true,
                'is_victory' => false,
                'feedback' => 'Вы не успели принять решение! В условиях землетрясения промедление опасно.',
                'is_correct' => false,
                'next_step' => $failStep->load('options'),
                'total_score' => $session->fresh()->total_score
            ];
        }

        $nextStep = $option->next_step_id 
            ? Step::where('id', $option->next_step_id)->with('options')->first()
            : null;

        $isTerminalStep = $nextStep && in_array($nextStep->slug, ['failed', 'succeed']);
        $isFinalAction = is_null($option->next_step_id) || $isTerminalStep;

        $isVictory = ($nextStep && $nextStep->slug === 'succeed') ||
                     ($isFinalAction && $session->currentStep->slug === 'succeed');

        $this->updateSession($session, $option->next_step_id, $option->score_points, $option->text, $isVictory, $isFinalAction);

        return [
            'is_finished' => $isFinalAction,
            'is_victory' => $isVictory,
            'feedback' => $option->feedback,
            'is_correct' => $option->is_correct,
            'next_step' => $nextStep,
            'total_score' => $session->fresh()->total_score
        ];
    }

    public function updateSession($session, $nextStepId, $points, $chosenText, $isVictory = false, $isFinal = false) 
    {
        $log = $session->journey_log ?? [];
        $log[] = [
            'step' => $session->currentStep->slug,
            'answer' => $chosenText,
            'timestamp' => now()
        ];

        $session->update([
            'current_step_id' => $nextStepId,
            'total_score' => ($session->total_score ?? 0) + $points,
            'journey_log' => $log,
            // $session->completed_at for not rewriting completed_at, in case of lag query
            'completed_at' => $isFinal ? now() : $session->completed_at,
            'is_victory' => $isVictory,
        ]);
    }
}