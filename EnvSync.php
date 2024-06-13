<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EnvSync extends Command
{
    // Define the command signature and description.
    protected $signature = 'env:sync {source} {target}';
    protected $description = 'Synchronize environment variables between .env files';

    // Main method that handles the command execution.
    public function handle()
    {
        // Retrieve the source and target file paths from the command arguments.
        $source = $this->argument('source');
        $target = $this->argument('target');

        // Check if the source file exists.
        if (!File::exists($source)) {
            $this->error("Source file ($source) does not exist.");
            return;
        }

        // Check if the target file exists.
        if (!File::exists($target)) {
            $this->error("Target file ($target) does not exist.");
            return;
        }

        // Parse the source and target .env files into associative arrays.
        $sourceEnv = $this->parseEnvFile($source);
        $targetEnv = $this->parseEnvFile($target);

        $this->info("Comparing $source with $target...");

        // Iterate over each environment variable in the source file.
        foreach ($sourceEnv as $key => $value) {
            // Check if the target file is missing the current environment variable.
            if (!array_key_exists($key, $targetEnv)) {
                $this->warn("Missing $key in $target");

                // Prompt the user to add the missing environment variable to the target file.
                if ($this->confirm("Do you want to add $key to $target?")) {
                    $targetEnv[$key] = $value;
                }
            }
        }

        // Create a backup of the target file before making changes.
        $this->backupFile($target);

        // Write the updated environment variables back to the target file.
        $this->writeEnvFile($target, $targetEnv);

        $this->info("Synchronization complete.");
    }

    // Method to parse an .env file into an associative array.
    protected function parseEnvFile($file)
    {
        // Read all lines from the .env file.
        $lines = File::lines($file);
        $env = [];

        // Process each line to extract environment variables.
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[$key] = trim($value);
            }
        }

        return $env;
    }

    // Method to create a backup of the specified file.
    protected function backupFile($file)
    {
        // Generate a backup file name with a timestamp.
        $backupFile = $file . '.backup.' . now()->format('Y-m-d_H-i-s');

        // Copy the original file to the backup file.
        File::copy($file, $backupFile);
        $this->info("Backup created: $backupFile");
    }

    // Method to write environment variables back to a file.
    protected function writeEnvFile($file, $env)
    {
        $content = '';

        // Convert the associative array back to a .env file format.
        foreach ($env as $key => $value) {
            $content .= "$key=$value\n";
        }

        // Write the content to the file.
        File::put($file, $content);
    }
}
