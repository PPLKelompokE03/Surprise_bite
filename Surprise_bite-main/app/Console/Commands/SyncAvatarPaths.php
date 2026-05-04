<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncAvatarPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:sync-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan avatar files and sync avatar_path in database for users without avatar_path but with avatar files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning avatar files in storage/app/public/avatars...');

        $avatarDir = storage_path('app/public/avatars');
        
        if (!is_dir($avatarDir)) {
            $this->error('Avatar directory does not exist: ' . $avatarDir);
            return 1;
        }

        $synced = 0;
        $errors = 0;

        // Scan each user folder
        $userFolders = File::directories($avatarDir);

        foreach ($userFolders as $userFolder) {
            $userId = basename($userFolder);
            
            // Try to find user by ID or by name (for 'subway' etc)
            $user = User::where('id', $userId)->orWhere('name', $userId)->first();
            
            if (!$user) {
                $this->warn("User not found for folder: $userId");
                continue;
            }

            // If user already has avatar_path, skip
            if ($user->avatar_path) {
                $this->info("User {$user->id} ({$user->name}) already has avatar_path: {$user->avatar_path}");
                continue;
            }

            // Get the first avatar file in this folder
            $avatarFiles = File::files($userFolder);
            
            if (empty($avatarFiles)) {
                $this->warn("No avatar files found for user folder: $userId");
                continue;
            }

            // Use the first file
            $avatarFile = $avatarFiles[0];
            $relativePath = 'avatars/' . $userId . '/' . basename($avatarFile);

            // Verify file exists in storage
            if (!Storage::disk('public')->exists($relativePath)) {
                $this->error("File not found in storage for $relativePath");
                $errors++;
                continue;
            }

            // Update user with avatar_path
            try {
                $user->avatar_path = $relativePath;
                $user->save();
                $this->info("✓ Synced User {$user->id} ({$user->name}): $relativePath");
                $synced++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to sync User {$user->id}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->line('');
        $this->info("Sync complete: $synced users updated, $errors errors");

        return 0;
    }
}
