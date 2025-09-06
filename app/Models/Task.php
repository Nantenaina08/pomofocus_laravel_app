<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'estimated_pomodoros',
        'completed_pomodoros',
        'priority',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function pomodoroSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class);
    }

    public function completedSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class)->where('is_completed', true);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->estimated_pomodoros == 0) {
            return 0;
        }
        return min(100, ($this->completed_pomodoros / $this->estimated_pomodoros) * 100);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            1 => 'Faible',
            2 => 'Moyenne',
            3 => 'Élevée',
            default => 'Faible'
        };
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
}
