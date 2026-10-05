<?php

declare(strict_types=1);
include __DIR__.'/vendor/autoload.php';
include 'config.php';

use Partitech\PhpMistral\Clients\Mistral\MistralClient;
use Partitech\PhpMistral\Exceptions\MistralClientException;

class Mistral
{
    private $params;
    private string $systemMessage;

    /** @var int maximale aantal pogingen bij rate limit */
    private const MAX_RETRIES = 4;

    /** @var int minimale wachttijd tussen pogingen (seconden), verdubbelt per poging */
    private const BACKOFF_BASE = 2;

    public function __construct()
    {
        $this->params = [
            'model' => 'ministral-8b-2512',
            'temperature' => 0.7,
            'top_p' => 1,
            'safe_prompt' => false,
            'random_seed' => 0,
            'max_tokens' => 500,
        ];

        $this->systemMessage = '...'; // jouw bestaande systeemtekst
    }

    public function sendMessage(string $message): string
    {
        try {
            $apiKey = getenv('MISTRAL_API_KEY');
            if (!$apiKey) {
                return "Geen API key gevonden";
            }

            $client = new MistralClient($apiKey);
            $messages = $client->getMessages()
                              ->addSystemMessage(content: $this->systemMessage)
                              ->addUserMessage(content: $message);

            return $this->chatWithRetry($client, $messages);
        } catch (\InvalidArgumentException $e) {
            return "Fout: ".$e->getMessage();
        } catch (MistralClientException $e) {
            error_log('Mistral API fout: '.$e->getMessage());
            return "Zucht, ik zit even op mijn limiet. Probeer het over een paar tellen nog 's — zelfs een snor moet af en toe uitpuffen.";
        }
    }

    private function chatWithRetry(MistralClient $client, $messages): string
    {
        $attempt = 0;
        while (true) {
            try {
                $response = $client->chat(messages: $messages, params: $this->params);
                return $response->getMessage();
            } catch (MistralClientException $e) {
                $attempt++;
                $msg = $e->getMessage();
                $isRateLimit = str_contains($msg, 'rate_limited')
                    || str_contains($msg, '"raw_status_code":429')
                    || str_contains($msg, 'Rate limit exceeded');

                if (!$isRateLimit || $attempt >= self::MAX_RETRIES) {
                    throw $e;
                }
                sleep(self::BACKOFF_BASE * (2 ** ($attempt - 1))); // 2s, 4s, 8s
            }
        }
    }
}
