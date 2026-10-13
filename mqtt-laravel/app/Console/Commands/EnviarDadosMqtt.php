<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Throwable;

class EnviarDadosMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar-dados-mqtt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia dados para o tópico MQTT especificado';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $server = 'broker.hivemq.com';
        $port = 1883;
        $clientId = 'laravel-publisher-'.uniqid();
        $topic = 'unifebe/01/01';

        $payload = json_encode([
            'ns' => 's0001',
            'estado' => 'amarelo',
            'msg' => 'Boa noite',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $this->info("Conectando ao broker MQTT {$server}:{$port}...");

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect();

            $this->info("Publicando mensagem no tópico: {$topic}");
            $mqtt->publish($topic, $payload, 0);

            $this->info("Mensagem enviada com sucesso:\n{$payload}");

            $mqtt->disconnect();
        } catch (MqttClientException|Throwable $e) {
            $this->error('Erro ao enviar dados MQTT: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
