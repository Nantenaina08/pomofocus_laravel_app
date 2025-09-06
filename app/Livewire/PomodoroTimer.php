<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\PomodoroSession;
use Livewire\Component;
use Livewire\Attributes\On;

class PomodoroTimer extends Component
{
    public $currentTask = null;
    public $currentSession = null;
    public $timeRemaining = 1500; // 25 minutes en secondes
    public $isRunning = false;
    public $sessionType = 'work'; // work, short_break, long_break
    public $completedPomodoros = 0;
    public $showBreakOptions = false;

    public $sessionDurations = [
        'work' => 1500,        // 25 minutes
        'short_break' => 300,   // 5 minutes
        'long_break' => 900,    // 15 minutes
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Charger les paramètres depuis la session ou base de données
        $this->completedPomodoros = session('completed_pomodoros', 0);
    }

    public function selectTask($taskId)
    {
        $this->currentTask = Task::find($taskId);
        $this->reset(['currentSession', 'isRunning']);
        $this->timeRemaining = $this->sessionDurations[$this->sessionType];
    }

    public function startTimer()
    {
        if (!$this->currentSession) {
            $this->createNewSession();
        }
        
        $this->isRunning = true;
        $this->dispatch('timer-started');
    }

    public function pauseTimer()
    {
        $this->isRunning = false;
        $this->dispatch('timer-paused');
    }

    public function resetTimer()
    {
        $this->isRunning = false;
        $this->timeRemaining = $this->sessionDurations[$this->sessionType];
        $this->currentSession = null;
        $this->dispatch('timer-reset');
    }

    public function skipSession()
    {
        $this->completeCurrentSession();
        $this->showBreakSelection();
    }

    private function createNewSession()
    {
        $this->currentSession = PomodoroSession::create([
            'task_id' => $this->currentTask?->id,
            'type' => $this->sessionType,
            'duration_minutes' => $this->sessionDurations[$this->sessionType] / 60,
            'started_at' => now(),
        ]);
    }

    private function completeCurrentSession()
    {
        if ($this->currentSession) {
            $this->currentSession->markAsCompleted();
            
            if ($this->sessionType === 'work') {
                $this->completedPomodoros++;
                session(['completed_pomodoros' => $this->completedPomodoros]);
            }
        }
    }

    public function showBreakSelection()
    {
        $this->showBreakOptions = true;
    }

    public function selectBreakType($type)
    {
        $this->sessionType = $type;
        $this->timeRemaining = $this->sessionDurations[$type];
        $this->showBreakOptions = false;
        $this->currentSession = null;
        $this->dispatch('break-selected', $type);
    }

    public function selectWorkSession()
    {
        $this->sessionType = 'work';
        $this->timeRemaining = $this->sessionDurations['work'];
        $this->showBreakOptions = false;
        $this->currentSession = null;
    }

    #[On('timer-tick')]
    public function handleTimerTick()
    {
        if ($this->isRunning && $this->timeRemaining > 0) {
            $this->timeRemaining--;
            
            if ($this->timeRemaining === 0) {
                $this->handleTimerComplete();
            }
        }
    }

    private function handleTimerComplete()
    {
        $this->isRunning = false;
        $this->completeCurrentSession();
        
        // Déterminer le prochain type de session
        if ($this->sessionType === 'work') {
            $this->showBreakSelection();
        } else {
            $this->selectWorkSession();
        }
        
        $this->dispatch('timer-complete', [
            'type' => $this->sessionType,
            'message' => $this->getCompletionMessage()
        ]);
    }

    private function getCompletionMessage(): string
    {
        return match($this->sessionType) {
            'work' => 'Session de travail terminée ! Temps pour une pause.',
            'short_break' => 'Pause courte terminée ! Retour au travail.',
            'long_break' => 'Pause longue terminée ! Prêt pour une nouvelle session.',
            default => 'Session terminée !'
        };
    }

    public function getFormattedTimeProperty()
    {
        $minutes = floor($this->timeRemaining / 60);
        $seconds = $this->timeRemaining % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getProgressPercentageProperty()
    {
        $totalDuration = $this->sessionDurations[$this->sessionType];
        return (($totalDuration - $this->timeRemaining) / $totalDuration) * 100;
    }

    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}
