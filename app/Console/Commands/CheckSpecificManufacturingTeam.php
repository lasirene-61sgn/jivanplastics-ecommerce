<?php

namespace App\Console\Commands;

use App\Models\ManufacturingTeam;
use Illuminate\Console\Command;

class CheckSpecificManufacturingTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-specific-manufacturing-team {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check specific manufacturing team details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $team = ManufacturingTeam::where('email', $email)->first();
        
        if (!$team) {
            $this->error('No manufacturing team found with email: ' . $email);
            return;
        }
        
        $this->info('Found manufacturing team:');
        $this->line('ID: ' . $team->id);
        $this->line('Email: ' . $team->email);
        $this->line('Factory Name: ' . $team->factory_name);
        $this->line('Contact Person: ' . $team->contact_person);
        $this->line('Phone: ' . $team->phone);
        $this->line('Is Active: ' . ($team->is_active ? 'Yes' : 'No'));
        $this->line('Created At: ' . $team->created_at);
    }
}
