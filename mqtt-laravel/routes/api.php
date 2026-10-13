<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SemaforoController;

// 1. Enviar configurações do semáforo via MQTT
Route::post('/semaforo/configurar', [SemaforoController::class, 'configurarSemaforo']);

// 2. Comandar o semáforo via MQTT (Cor desejada)
Route::post('/semaforo/comandar', [SemaforoController::class, 'comandarSemaforo']);

// 3. Consultar um CEP no site VIA CEP
Route::get('/cep/{cep}', [SemaforoController::class, 'consultarCep']);