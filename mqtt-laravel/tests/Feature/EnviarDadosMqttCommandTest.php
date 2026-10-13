<?php

use App\Console\Commands\EnviarDadosMqtt;
use Illuminate\Support\Facades\Artisan;

test('enviar dados mqtt command is registered', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('app:enviar-dados-mqtt');
    expect($commands['app:enviar-dados-mqtt'])->toBeInstanceOf(EnviarDadosMqtt::class);
});
