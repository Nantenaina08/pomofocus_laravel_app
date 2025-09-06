<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'type',
        'duration_minutes',
        'started_at',
        'completed_at',
        'is_completed'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'work' => 'Travail',
            'short_break' => 'Pause courte',
            'long_break' => 'Pause longue',
            default => 'Travail'
        };
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'completed_at' => now(),
            'is_completed' => true
        ]);

        // Si c'est une session de travail, incrémenter le compteur de la tâche
        if ($this->type === 'work' && $this->task) {
            $this->task->increment('completed_pomodoros');
            
            // Marquer la tâche comme terminée si tous les pomodoros sont faits
            if ($this->task->completed_pomodoros >= $this->task->estimated_pomodoros) {
                $this->task->markAsCompleted();
            }
        }
    }
}
