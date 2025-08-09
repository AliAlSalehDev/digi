<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call("optimiz:clear");
        $this->info("optimiz clear successfully");
        $this->call("migrate:fresh");
        $this->info("fresh database migrated successfully");
        $this->call("db:seed");
        $this->info("data seeded successfully");
    }
}
