<?php

namespace App\Console\Commands;

use App\Models\ManufacturingTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetManufacturingTeamPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-manufacturing-team-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset manufacturing team password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $team = ManufacturingTeam::where('email', $email)->first();
        
        if (!$team) {
            $this->error('No manufacturing team found with email: ' . $email);
            return;
        }
        
        $team->password = $password;
        $team->save();
        
        $this->info('Password reset successfully for: ' . $email);
    }
}
