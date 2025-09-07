<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pomofocus - Technique Pomodoro</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body {
            background: linear-gradient(135deg, #667e8a 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen py-8">
        <!-- En-tête -->
        <header class="text-center mb-12">
            <div class="max-w-4xl mx-auto px-4">
                <h1 class="text-5xl font-bold text-white mb-4">
                     Pomofocus
                </h1>
                <p class="text-xl text-white/90 max-w-2xl mx-auto">
                    Boostez votre productivité avec la technique Pomodoro. 
                    Concentrez-vous 25 minutes, puis prenez une pause bien méritée !
                </p>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Timer Pomodoro -->
                <div class="space-y-6">
                    <div>
                        <livewire:pomodoro-timer />
                    </div>
                    
                    <!-- Instructions -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                        <h3 class="text-lg font-semibold mb-4">Comment utiliser Pomofocus ?</h3>
                        <ol class="space-y-2 text-sm">
                            <li class="flex items-start space-x-2">
                                <span class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">1</span>
                                <span>Créez une tâche dans la liste à droite</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">2</span>
                                <span>Cliquez sur "Focus" pour sélectionner la tâche</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">3</span>
                                <span>Démarrez le timer et concentrez-vous 25 minutes</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">4</span>
                                <span>Prenez une pause de 5 minutes (ou 15 minutes après 4 pomodoros)</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">5</span>
                                <span>Répétez jusqu'à terminer votre tâche !</span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Gestionnaire de tâches -->
                <div>
                    <livewire:task-manager />
                </div>
            </div>
        </div>

        <!-- Statistiques du jour -->
        <div class="max-w-4xl mx-auto px-4 mt-12">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-4"> Statistiques du jour</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ session('completed_pomodoros', 0) }}</div>
                        <div class="text-sm opacity-75">Pomodoros terminés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ \App\Models\Task::where('status', 'completed')->whereDate('completed_at', today())->count() }}</div>
                        <div class="text-sm opacity-75">Tâches terminées</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ session('completed_pomodoros', 0) * 25 }}</div>
                        <div class="text-sm opacity-75">Minutes de focus</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ \App\Models\Task::where('status', 'pending')->count() }}</div>
                        <div class="text-sm opacity-75">Tâches restantes</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @livewireScripts
    
    <script>
        // Communication entre les composants
        document.addEventListener('livewire:init', () => {
            Livewire.on('task-selected', (taskId) => {
                
                Livewire.dispatch('select-task', { taskId: taskId });
            });
        });
    </script>
</body>
</html>