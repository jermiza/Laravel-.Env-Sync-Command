# EnvSync Command

A Laravel Artisan command to synchronize environment variables between .env files.

## Installation

1. Copy the `EnvSync.php` file to `app/Console/Commands`.
2. Register the command in `app/Console/Kernel.php`.

## Usage

Run the command to synchronize environment variables:

```bash
php artisan env:sync .env .env.staging






Detailed Comments Explanation:
Class and Command Setup:

The command is defined with a signature (env:sync {source} {target}) and a description.
The handle method is the entry point of the command execution.
File Existence Checks:

Checks if the source and target files exist using File::exists($source) and File::exists($target).
Outputs an error message and returns if either file is missing.
Parsing Environment Files:

parseEnvFile($file) method reads the environment file line by line and converts it into an associative array ($env).
Only lines containing an = are considered valid environment variable declarations.
Synchronization Logic:

Compares the source and target environment variables.
For each variable in the source, checks if it is missing in the target.
Prompts the user to add missing variables to the target file.
Backup Creation:

backupFile($file) method creates a timestamped backup of the target file before any changes are made.
Informs the user about the backup creation.
Writing Back to Target File:

writeEnvFile($file, $env) method converts the associative array back into .env file format and writes it to the target file.
