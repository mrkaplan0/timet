<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users except admin users
        $users = User::where('role', 'user')->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No regular users found. Please run UserSeeder first.');
            return;
        }

        // Create time entries for each user
        foreach ($users as $user) {
            // Create 15-25 random time entries for each user over the last 30 days
            $entryCount = rand(7, 20);
            
            for ($i = 0; $i < $entryCount; $i++) {
                // Random date within the last 30 days
                $date = Carbon::now()->subDays(rand(0, 30));
                
                // Random start time between 8 AM and 5 PM
                $startHour = rand(8, 17);
                $startMinute = rand(0, 59);
                $startTime = $date->copy()->setTime($startHour, $startMinute);
                
                // Random work duration between 1-8 hours
                $workHours = rand(1, 8);
                $workMinutes = rand(0, 59);
                $endTime = $startTime->copy()->addHours($workHours)->addMinutes($workMinutes);
                
                // Calculate total hours
                $totalHours = round($startTime->diffInMinutes($endTime) / 60, 2);
                
                // Random work notes
                $notes = [
                    'Working on project development',
                    'Meeting with client',
                    'Code review and testing',
                    'Database optimization',
                    'Feature implementation',
                    'Bug fixes and debugging',
                    'Documentation update',
                    'Team collaboration',
                    'Research and planning',
                    'Quality assurance testing',
                    null // Some entries without notes
                ];
                
                TimeEntry::create([
                    'user_id' => $user->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_hours' => $totalHours,
                    'note' => $notes[array_rand($notes)],
                ]);
            }
        }
        
        $this->command->info('Time entries created successfully for all users!');
    }
}
