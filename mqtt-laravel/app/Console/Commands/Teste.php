<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class Teste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:teste';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando de teste';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new User();
        $user->name = "Teste";
        $user->email = "teste@teste";
        $user->password = "teste@teste";
        $user->save();
    }
}
