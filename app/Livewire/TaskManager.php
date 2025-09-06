<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Livewire\Attributes\Rule;

class TaskManager extends Component
{
    public $tasks;
    public $showCreateForm = false;
    public $editingTask = null;
    
    #[Rule('required|string|max:255')]
    public $title = '';
    
    #[Rule('nullable|string')]
    public $description = '';
    
    #[Rule('required|integer|min:1|max:10')]
    public $estimated_pomodoros = 1;
    
    #[Rule('required|integer|min:1|max:3')]
    public $priority = 1;

    public function mount()
    {
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $this->tasks = Task::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function showCreateForm()
    {
        $this->showCreateForm = true;
        $this->resetForm();
    }

    public function hideCreateForm()
    {
        $this->showCreateForm = false;
        $this->editingTask = null;
        $this->resetForm();
    }

    public function createTask()
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'priority' => $this->priority,
            'status' => 'pending'
        ]);

        $this->hideCreateForm();
        $this->loadTasks();
        
        $this->dispatch('task-created', 'Tâche créée avec succès !');
    }

    public function editTask($taskId)
    {
        $this->editingTask = Task::find($taskId);
        $this->title = $this->editingTask->title;
        $this->description = $this->editingTask->description;
        $this->estimated_pomodoros = $this->editingTask->estimated_pomodoros;
        $this->priority = $this->editingTask->priority;
        $this->showCreateForm = true;
    }

    public function updateTask()
    {
        $this->validate();

        $this->editingTask->update([
            'title' => $this->title,
            'description' => $this->description,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'priority' => $this->priority,
        ]);

        $this->hideCreateForm();
        $this->loadTasks();
        
        $this->dispatch('task-updated', 'Tâche mise à jour avec succès !');
    }

    public function deleteTask($taskId)
    {
        Task::find($taskId)->delete();
        $this->loadTasks();
        
        $this->dispatch('task-deleted', 'Tâche supprimée avec succès !');
    }

    public function toggleTaskStatus($taskId)
    {
        $task = Task::find($taskId);
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        
        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null
        ]);

        $this->loadTasks();
    }

    public function selectTaskForTimer($taskId)
    {
        $this->dispatch('task-selected', $taskId);
    }

    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->estimated_pomodoros = 1;
        $this->priority = 1;
    }

    public function render()
    {
        return view('livewire.task-manager');
    }
}
