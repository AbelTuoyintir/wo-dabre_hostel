<?php
// Create a console command to fix existing records
// app/Console/Commands/FixLargeUploads.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;

class FixLargeUploads extends Command
{
    protected $signature = 'fix:large-uploads';
    protected $description = 'Handle existing large file uploads';
    
    public function handle()
    {
        $rooms = Room::whereNotNull('video_path')->get();
        
        foreach ($rooms as $room) {
            // Check if video exists and is large
            if (Storage::exists($room->video_path)) {
                $size = Storage::size($room->video_path);
                
                if ($size > 1024 * 1024 * 100) { // > 100MB
                    $this->info("Found large video: {$room->video_path} ({$size} bytes)");
                    
                    // Option 1: Compress
                    // Option 2: Move to different storage
                    // Option 3: Mark for manual review
                }
            }
        }
    }
}