<?php

namespace App\Services;

use App\Models\SimulationSession;
use App\Models\Option;
use App\Models\Step;
use Carbon\Carbon;

class SuminationService 
{
    public function processAnswer(string $sessionUuid, int $optionId)
    {
        $session = SimulationSession::where('uuid', $sessionUuid)->with('currentStep')->firstOrFail();
        $option = Option::findOrFail($optionId);

        $isTimeout = false;
        if ($session->currentStep->time_limit) {
            $startTime = $session->updated_at;
            $now = Carbon::now();

            if ($now->diffInSeconds($startTime) > $session->currentStep->time_limit) {
                $isTimeout = true;
            }
        }

        if ($isTimeout) {
            $failStep = Step::where('slug', 'failed')->first();
            $this->updateSession($session, $failStep->id, 0, 'Время вышло! В реальности секунды решают всё.');
            
            return [
                'feedback' => 'Вы не успели принять решение! В условиях землетрясения промедление опасно.',
                'is_correct' => false,
                'next_step' => $failStep->load('options')
            ];
        }

        $nextStep = Step::where('id', $option->next_step_id)->with('options')->first();
        $this->updateSession($session, $nextStep->id, $option->score_points, $option->text);

        return [
            'feedback' => $option->feedback,
            'is_correct' => $option->is_correct,
            'next_step' => $nextStep
        ];
    }

    public function updateSession($session, $nextStepId, $points, $chosenText) 
    {
        $log = $session->journey_log ?? [];
        $log[] = [
            'step' => $session->currentStep->slug,
            'anwer' => $chosenText,
            'timestamp' => now()
        ];

        $session->update([
            'current_step_id' => $nextStepId,
            'total_score' => $session->total_scores + $points,
            'journey_log' => $log
        ]);
    }
}