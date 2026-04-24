<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('reset:academic-data {--force : Run without confirmation}', function () {
    $tables = [
        'attendances',
        'fee_payments',
        'students',
        'subjects',
        'branches',
        'users',
    ];

    $this->warn('This will permanently delete academic data and reset IDs for selected tables.');
    $this->line('Tables: ' . implode(', ', $tables));

    if (!$this->option('force') && !$this->confirm('Do you want to continue?', false)) {
        $this->info('Operation cancelled.');
        return;
    }

    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->line("Skipped (not found): {$table}");
                continue;
            }

            DB::table($table)->truncate();
            $this->line("Truncated: {$table}");
        }
    } catch (\Throwable $exception) {
        $this->error('Reset failed: ' . $exception->getMessage());
        return;
    } finally {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    $this->call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
    $this->info('Academic data reset complete. Admin seed has been re-run successfully.');
})->purpose('Reset academic tables and re-seed default admin data');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
