<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelemetriaSemaforo extends Model
{
    public $timestamps = false;
    public $table = 'telemetria_semaforo';
    public $primaryKey = ['codigo_semaforo', 'data'];
    public $incrementing = false;
}
