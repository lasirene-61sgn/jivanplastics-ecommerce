<?php

namespace App\Console\Commands;

use App\Models\ManufacturingTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestManufacturingTeamPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-manufacturing-team-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test manufacturing team password verification';

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
        
        $this->info('Found manufacturing team with email: ' . $email);
        $this->info('Stored password hash: ' . $team->password);
        
        if (Hash::check($password, $team->password)) {
            $this->info('Password verification: SUCCESS');
        } else {
            $this->error('Password verification: FAILED');
        }
    }
}
