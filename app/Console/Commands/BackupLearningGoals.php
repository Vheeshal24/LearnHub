<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\LearningGoal;
use Illuminate\Support\Facades\File;

class BackupLearningGoals extends Command
{
    protected $signature = 'backup:learning-goals';
    protected $description = 'Backup Learning Goals daily';

    public function handle()
    {
        $backupDir = storage_path('backups/learning_goals');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $timestamp = now()->format('Y-m-d_H-i-s');
        $file = $backupDir . "/learning_goals_{$timestamp}.json";
        $goals = LearningGoal::all();
        File::put($file, $goals->toJson(JSON_PRETTY_PRINT));
        $this->info("Learning Goals backed up to: {$file}");
        $allFiles = collect(File::files($backupDir))
            ->sortByDesc(fn($f) => $f->getCTime())
            ->values();

        foreach ($allFiles->slice(1) as $oldFile) {
            File::delete($oldFile);
        }

        $this->info("Old backups removed, keeping latest only.");
    }
}
