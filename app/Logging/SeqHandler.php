<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Illuminate\Support\Facades\Http;

class SeqHandler extends AbstractProcessingHandler
{
    protected $serverUrl;
    protected $apiKey;

    public function __construct($serverUrl, $apiKey = null, $level = \Monolog\Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->serverUrl = rtrim($serverUrl, '/');
        $this->apiKey = $apiKey;
    }

    protected function write(LogRecord $record): void
    {
        // Preparar propiedades
        $properties = [
            'Application' => config('app.name'),
            'Environment' => config('app.env'),
        ];

        // Agregar contexto si existe
        if (!empty($record->context)) {
            foreach ($record->context as $key => $value) {
                $properties[$key] = is_string($value) ? $value : json_encode($value);
            }
        }

        // Formato correcto para Seq
        $event = [
            'Timestamp' => $record->datetime->format('Y-m-d\TH:i:s.uP'),
            'Level' => $record->level->getName(),
            'MessageTemplate' => $record->message,
            'Properties' => $properties,
        ];

        // Seq requiere un array de eventos en el campo 'Events'
        $data = [
            'Events' => [$event]
        ];

        $headers = ['Content-Type' => 'application/json'];

        if ($this->apiKey) {
            $headers['X-Seq-ApiKey'] = $this->apiKey;
        }

        try {
            Http::withHeaders($headers)
                ->post($this->serverUrl . '/api/events/raw', $data);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application
        }
    }
}
