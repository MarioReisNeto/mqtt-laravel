<?php

use App\Console\Commands\EscutarMqtt;
use Illuminate\Support\Facades\Artisan;

test('escutar mqtt command is registered', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('app:escutar-mqtt');
    expect($commands['app:escutar-mqtt'])->toBeInstanceOf(EscutarMqtt::class);
});
