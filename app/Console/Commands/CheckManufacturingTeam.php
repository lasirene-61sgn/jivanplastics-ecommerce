<?php

namespace App\Console\Commands;

use App\Models\ManufacturingTeam;
use Illuminate\Console\Command;

class CheckManufacturingTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-manufacturing-team';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if manufacturing team exists in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teams = ManufacturingTeam::all();
        
        if ($teams->isEmpty()) {
            $this->info('No manufacturing teams found in database.');
            return;
        }
        
        $this->info('Found ' . $teams->count() . ' manufacturing team(s):');
        foreach ($teams as $team) {
            $this->line('ID: ' . $team->id . ' | Email: ' . $team->email . ' | Factory: ' . $team->factory_name);
        }
    }
}
