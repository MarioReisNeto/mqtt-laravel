<?php

namespace App\Console\Commands;

use App\Models\TelemetriaSemaforo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Throwable;

class EscutarMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:escutar-mqtt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para escutar mqtt';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        $server = Config::get('app.mqttServer');
        $port = Config::get('app.mqttpPort');
        $clientId = Config::get('app.mqttpClienteId');
        $topic = 'unifebe/01/01';

        $this->info("Conectando ao broker MQTT {$server}:{$port}...");

        while(true) {

            try {
                $mqtt = new MqttClient($server, $port, $clientId);
                $mqtt->connect();

                $this->info('Conectado com sucesso!');
                $this->info("Inscrito no tópico: {$topic}");
                $this->info('Aguardando mensagens... (Pressione Ctrl+C para encerrar)');

                $mqtt->subscribe($topic, function (string $topic, string $message): void {
                    $this->info("Mensagem recebida no tópico [{$topic}]: {$message}");

                    try {
                        $jsonMsg = json_decode($message);

                        if(isset($jsonMsg->codigo) && isset($jsonMsg->cor) && isset($jsonMsg->status)) {
                            $telemetria = new TelemetriaSemaforo();
                            $telemetria->codigo_semaforo = $jsonMsg->codigo;
                            $telemetria->data = date('Y-m-d H:i:s');
                            $telemetria->cor = $jsonMsg->cor;
                            $telemetria->status = $jsonMsg->status;
                            $telemetria->save();
                        }

                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }

                }, 0);

                $mqtt->loop(true);

                $mqtt->disconnect();
            } catch (MqttClientException|Throwable $e) {
                $this->error('Erro no cliente MQTT: ' . $e->getMessage());
            }

            sleep(20);
            $this->info("Tentando reconectar...");

        }

        return self::SUCCESS;
    }
}
