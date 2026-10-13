<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\MqttClient;

class SemaforoController extends Controller
{
    // 1. Enviar configurações do semáforo via MQTT
    public function configurarSemaforo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'tempos' => 'required|array',
            'tempos.verde'    => 'required|integer|min:1',
            'tempos.amarelo'  => 'required|integer|min:1',
            'tempos.vermelho' => 'required|integer|min:1',
        ]);

        $codigo = $request->input('codigo');
        $tempos = $request->input('tempos');

        $payload = json_encode([
            'codigo' => $codigo,
            'tempos' => $tempos,
        ]);

        $server   = config('mqtt.host');
        $port     = config('mqtt.port');
        $clientId = 'laravel_config_' . uniqid();

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect();
            $mqtt->publish("unifebe/semaforo/{$codigo}/config", $payload, 0);
            $mqtt->disconnect();

            return response()->json([
                'status'        => 'sucesso',
                'mensagem'      => 'Configuração enviada via MQTT!',
                'dados_enviados' => json_decode($payload),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'erro',
                'mensagem' => 'Falha ao conectar no MQTT: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 2. Comandar o semáforo via MQTT (Cor desejada)
    public function comandarSemaforo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cor'    => 'required|string|in:vermelho,amarelo,verde',
        ]);

        $codigo = $request->input('codigo');
        $cor    = $request->input('cor');

        $payload = json_encode([
            'codigo' => $codigo,
            'cor'    => $cor,
        ]);

        $server   = config('mqtt.host');
        $port     = config('mqtt.port');
        $clientId = 'laravel_comando_' . uniqid();

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect();
            $mqtt->publish("unifebe/semaforo/{$codigo}/comando", $payload, 0);
            $mqtt->disconnect();

            return response()->json([
                'status'        => 'sucesso',
                'mensagem'      => "Comando de cor ({$cor}) enviado via MQTT!",
                'dados_enviados' => json_decode($payload),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'erro',
                'mensagem' => 'Falha ao conectar no MQTT: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 3. Consultar CEP no site Via CEP
    public function consultarCep($cep)
    {
        // Valida formato do CEP (8 dígitos numéricos)
        $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return response()->json([
                'status'  => 'erro',
                'mensagem' => 'CEP inválido. Informe 8 dígitos numéricos.',
            ], 422);
        }

        // Consulta a API do Via CEP
        $response = Http::get("https://viacep.com.br/ws/{$cepLimpo}/json/");

        if (! $response->successful()) {
            return response()->json([
                'status'  => 'erro',
                'mensagem' => 'Não foi possível consultar o CEP.',
            ], 400);
        }

        $data = $response->json();

        // O ViaCEP retorna {"erro": "true"} com status 200 quando o CEP não existe
        if (isset($data['erro'])) {
            return response()->json([
                'status'  => 'erro',
                'mensagem' => 'CEP não encontrado.',
            ], 404);
        }

        return response()->json($data);
    }
}