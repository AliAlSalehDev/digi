<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\App;
use App\Http\Controllers\WebSocketMealController;

class StartWebSocketServer extends Command
{
    protected $signature = 'websocket:serve';
    protected $description = 'Start the WebSocket server for meal planner';

    public function handle()
    {
        $this->info('Starting WebSocket server on localhost:8080...');
        
        $app = new App('localhost', 8080, '0.0.0.0');
        $app->route('/meal-planner', new WebSocketMealController, ['*']);
        
        $this->info('WebSocket server started successfully!');
        $this->info('Connect to: ws://localhost:8080/meal-planner');
        
        $app->run();
    }
}