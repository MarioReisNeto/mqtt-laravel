<?php

return [

    /* Configurações de conexão com o broker MQTT. Os valores são lidos
    a partir das variáveis de ambiente definidas no arquivo .env.
    */

    'host' => env('MQTT_HOST', 'broker.emqx.io'),

    'port' => (int) env('MQTT_PORT', 1883),

];
