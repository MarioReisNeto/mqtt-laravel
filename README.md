MQTT Laravel — Controle de Semáforo via API
Projeto desenvolvido em PHP com Laravel para controle de semáforos inteligentes via protocolo MQTT, com integração à API pública ViaCEP.

 Sobre o Projeto
Este projeto foi desenvolvido como trabalho acadêmico na UNIFEBE, com o objetivo de demonstrar a integração entre uma API REST e o protocolo de comunicação MQTT, simulando o controle de um semáforo inteligente conectado à internet (IoT).

A API permite enviar configurações de tempo, comandar a cor ativa do semáforo e consultar endereços por CEP — tudo via requisições HTTP que se comunicam com dispositivos finais através de um broker MQTT público.

 Tecnologias Utilizadas
Tecnologia	Versão	Uso
PHP	8.2+	Linguagem principal
Laravel	12.x	Framework da API REST
php-mqtt/client	2.3	Biblioteca de comunicação MQTT
MQTTX	Desktop	Cliente MQTT para testes
Postman	—	Teste dos endpoints da API
SQLite	—	Banco de dados local
ViaCEP	API pública	Consulta de endereços por CEP
Inteligência Artificial utilizada
Este projeto contou com o auxílio do Kiro, uma IA desenvolvida pela AWS para desenvolvimento de software.

O Kiro foi utilizado para:
Revisão e análise do código existente
Implementação das melhorias de validação nos endpoints
Tratamento de erros na consulta de CEP

 Como Funciona
Postman ──POST──► Laravel API ──MQTT publish──► broker.emqx.io ──► MQTTX / Dispositivo
O cliente (Postman ou qualquer frontend) faz uma requisição HTTP para a API Laravel
A API valida os dados recebidos
A API conecta no broker MQTT público (broker.emqx.io)
Publica a mensagem no tópico correspondente ao semáforo
O dispositivo final (ou MQTTX nos testes) recebe a mensagem em tempo real
 Endpoints da API
Enviar configurações do semáforo
POST /api/semaforo/configurar
Body (JSON):

{
    "codigo": "123",
    "tempos": {
        "verde": 30,
        "amarelo": 5,
        "vermelho": 25
    }
}
Tópico MQTT publicado: unifebe/semaforo/123/config

Comandar a cor do semáforo
POST /api/semaforo/comandar
Body (JSON):

{
    "codigo": "123",
    "cor": "verde"
}
Valores aceitos para cor: vermelho, amarelo, verde

Tópico MQTT publicado: unifebe/semaforo/123/comando

Consultar endereço por CEP
GET /api/cep/{cep}
Exemplo:

GET /api/cep/88350000
Resposta:

{
    "cep": "88350-000",
    "logradouro": "...",
    "bairro": "...",
    "localidade": "Brusque",
    "uf": "SC"
}
Estrutura dos Arquivos Principais
mqtt-laravel/
├── routes/
│   └── api.php                          # Definição das rotas
├── app/
│   └── Http/
│       └── Controllers/
│           └── SemaforoController.php   # Lógica dos endpoints
├── config/
│   └── mqtt.php                         # Configurações do broker MQTT
└── .env                                 # Variáveis de ambiente
Configuração do Ambiente
Clone o repositório e instale as dependências:

composer install
Copie o arquivo de ambiente:

cp .env.example .env
php artisan key:generate
Configure o broker MQTT no .env:

MQTT_HOST=broker.emqx.io
MQTT_PORT=1883
Execute as migrations:

php artisan migrate
Inicie o servidor:

php artisan serve
 Como Testar
Inicie o servidor com php artisan serve
Abra o MQTTX e conecte em broker.emqx.io:1883
Inscreva-se no tópico unifebe/# no MQTTX
No Postman, adicione o header Accept: application/json
Dispare as requisições e observe as mensagens chegando no MQTTX em tempo real

 Autor
Desenvolvido por Mario Reis
UNIFEBE — Universidade de Brusque
