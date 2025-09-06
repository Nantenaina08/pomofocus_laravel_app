<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">📋 Mes Tâches</h2>
        <button 
            wire:click="showCreateForm"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2"
        >
            <span>➕</span>
            <span>Nouvelle tâche</span>
        </button>
    </div>

    <!-- Formulaire de création/modification -->
    @if($showCreateForm)
        <div class="bg-white rounded-2xl shadow-xl p-6 animate-slide-down">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                {{ $editingTask ? 'Modifier la tâche' : 'Nouvelle tâche' }}
            </h3>
            
            <form wire:submit="{{ $editingTask ? 'updateTask' : 'createTask' }}">
                <div class="space-y-4">
                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                        <input 
                            type="text" 
                            wire:model="title"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: Réviser le chapitre 3"
                        >
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            wire:model="description"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Détails optionnels..."
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Pomodoros estimés -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pomodoros estimés *</label>
                            <select 
                                wire:model="estimated_pomodoros"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }} pomodoro{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('estimated_pomodoros') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Priorité -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priorité *</label>
                            <select 
                                wire:model="priority"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="1">🟢 Faible</option>
                                <option value="2">🟡 Moyenne</option>
                                <option value="3">🔴 Élevée</option>
                            </select>
                            @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button 
                        type="button"
                        wire:click="hideCreateForm"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200"
                    >
                        {{ $editingTask ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Liste des tâches -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 {{ $task->status === 'completed' ? 'border-green-500' : ($task->priority === 3 ? 'border-red-500' : ($task->priority === 2 ? 'border-yellow-500' : 'border-gray-300')) }} transition-all duration-200 hover:shadow-xl">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- En-tête de la tâche -->
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold {{ $task->status === 'completed' ? 'text-gray-500 line-through' : 'text-gray-800' }}">
                                {{ $task->title }}
                            </h3>
                            
                            <!-- Badge de priorité -->
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->priority === 3 ? 'bg-red-100 text-red-800' : ($task->priority === 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $task->priority_label }}
                            </span>
                            
                            <!-- Badge de statut -->
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $task->status === 'completed' ? '✅ Terminé' : '⏳ En cours' }}
                            </span>
                        </div>

                        <!-- Description -->
                        @if($task->description)
                            <p class="text-gray-600 text-sm mb-3">{{ $task->description }}</p>
                        @endif

                        <!-- Progression des pomodoros -->
                        <div class="mb-3">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-700">Progression:</span>
                                <span class="text-sm text-gray-600">{{ $task->completed_pomodoros }}/{{ $task->estimated_pomodoros }} pomodoros</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $task->progress_percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="text-xs text-gray-500">
                            Créé le {{ $task->created_at->format('d/m/Y à H:i') }}
                            @if($task->completed_at)
                                • Terminé le {{ $task->completed_at->format('d/m/Y à H:i') }}
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col space-y-2 ml-4">
                        @if($task->status !== 'completed')
                            <button 
                                wire:click="selectTaskForTimer({{ $task->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-1"
                                title="Sélectionner pour le timer"
                            >
                                <span>🍅</span>
                                <span>Focus</span>
                            </button>
                        @endif
                        
                        <button 
                            wire:click="toggleTaskStatus({{ $task->id }})"
                            class="{{ $task->status === 'completed' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                            title="{{ $task->status === 'completed' ? 'Marquer comme non terminé' : 'Marquer comme terminé' }}"
                        >
                            {{ $task->status === 'completed' ? '↩️' : '✅' }}
                        </button>
                        
                        <button 
                            wire:click="editTask({{ $task->id }})"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                            title="Modifier"
                        >
                            ✏️
                        </button>
                        
                        <button 
                            wire:click="deleteTask({{ $task->id }})"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                            title="Supprimer"
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Aucune tâche pour le moment</h3>
                <p class="text-gray-600 mb-4">Créez votre première tâche pour commencer à utiliser Pomofocus !</p>
                <button 
                    wire:click="showCreateForm"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                >
                    ➕ Créer ma première tâche
                </button>
            </div>
        @endforelse
    </div>

    <!-- JavaScript pour les notifications -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('task-created', (message) => {
                showToast(message, 'success');
            });
            
            Livewire.on('task-updated', (message) => {
                showToast(message, 'success');
            });
            
            Livewire.on('task-deleted', (message) => {
                showToast(message, 'success');
            });
            
            Livewire.on('task-selected', (taskId) => {
                showToast('Tâche sélectionnée pour le timer !', 'info');
            });
        });
        
        function showToast(message, type = 'info') {
            // Création d'un toast simple
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            }`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Animation d'entrée
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Suppression après 3 secondes
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>
</div>