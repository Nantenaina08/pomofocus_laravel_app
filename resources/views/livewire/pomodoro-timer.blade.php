<div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-md mx-auto">
    <!-- En-tête du timer -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            {{ $sessionType === 'work' ? '🍅 Focus Time' : ($sessionType === 'short_break' ? '☕ Pause Courte' : '🌟 Pause Longue') }}
        </h2>
        
        @if($currentTask)
            <p class="text-gray-600 text-sm bg-gray-100 rounded-lg px-3 py-1 inline-block">
                📋 {{ $currentTask->title }}
            </p>
        @endif
    </div>

    <!-- Affichage du temps -->
    <div class="relative mb-8">
        <!-- Cercle de progression -->
        <div class="relative w-64 h-64 mx-auto">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <!-- Cercle de fond -->
                <circle cx="50" cy="50" r="45" stroke="#f3f4f6" stroke-width="8" fill="none"/>
                <!-- Cercle de progression -->
                <circle 
                    cx="50" cy="50" r="45" 
                    stroke="{{ $sessionType === 'work' ? '#ef4444' : '#10b981' }}" 
                    stroke-width="8" 
                    fill="none"
                    stroke-dasharray="282.7"
                    stroke-dashoffset="{{ 282.7 - (282.7 * $this->progressPercentage / 100) }}"
                    class="transition-all duration-1000 ease-linear"
                />
            </svg>
            
            <!-- Temps au centre -->
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-4xl font-mono font-bold text-gray-800">
                    {{ $this->formattedTime }}
                </span>
            </div>
        </div>
    </div>

    <!-- Contrôles du timer -->
    <div class="flex justify-center space-x-4 mb-6">
        @if(!$isRunning)
            <button 
                wire:click="startTimer"
                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-200 flex items-center space-x-2"
            >
                <span>▶️</span>
                <span>{{ $currentSession ? 'Reprendre' : 'Démarrer' }}</span>
            </button>
        @else
            <button 
                wire:click="pauseTimer"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-200 flex items-center space-x-2"
            >
                <span>⏸️</span>
                <span>Pause</span>
            </button>
        @endif

        <button 
            wire:click="resetTimer"
            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-200 flex items-center space-x-2"
        >
            <span></span>
            <span>Reset</span>
        </button>

        <button 
            wire:click="skipSession"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-200 flex items-center space-x-2"
        >
            <span>⏭️</span>
            <span>Skip</span>
        </button>
    </div>

    <!-- Sélection du type de pause -->
    @if($showBreakOptions)
        <div class="bg-gray-50 rounded-xl p-6 mb-6 animate-fade-in">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Choisissez votre pause :</h3>
            <div class="flex space-x-3 justify-center">
                <button 
                    wire:click="selectBreakType('short_break')"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                >
                    ☕ Courte (5min)
                </button>
                <button 
                    wire:click="selectBreakType('long_break')"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                >
                    🌟 Longue (15min)
                </button>
                <button 
                    wire:click="selectWorkSession"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                >
                    🍅 Continuer
                </button>
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex justify-center space-x-6 text-sm">
            <div class="text-center">
                <div class="text-2xl font-bold text-red-500">{{ $completedPomodoros }}</div>
                <div class="text-gray-600">Pomodoros</div>
            </div>
            @if($currentTask)
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-500">{{ $currentTask->completed_pomodoros }}/{{ $currentTask->estimated_pomodoros }}</div>
                    <div class="text-gray-600">Tâche</div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // JavaScript pour le timer
    document.addEventListener('livewire:init', () => {
        let timerInterval;
        
        Livewire.on('timer-started', () => {
            startClientTimer();
        });
        
        Livewire.on('timer-paused', () => {
            clearInterval(timerInterval);
        });
        
        Livewire.on('timer-reset', () => {
            clearInterval(timerInterval);
        });
        
        Livewire.on('timer-complete', (event) => {
            clearInterval(timerInterval);
            showNotification(event.message);
            playNotificationSound();
        });
        
        function startClientTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                @this.call('handleTimerTick');
            }, 1000);
        }
        
        function showNotification(message) {
            if (Notification.permission === 'granted') {
                new Notification('Pomofocus', {
                    body: message,
                    icon: '/favicon.ico'
                });
            }
        }
        
        function playNotificationSound() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(() => {
                // Son non disponible
            });
        }
        
        // Demander la permission pour les notifications
        if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    });
</script>
