<?php

declare(strict_types=1);
include __DIR__.'/vendor/autoload.php';
include 'config.php';

use Partitech\PhpMistral\Clients\Mistral\MistralClient;

class Mistral
{
    private $params;
    private $systemMessage = 'Je bent de chatbot van geensnor.nl. Schrijf in informeel, spreektalig 
Nederlands, met af en toe een net iets te deftig woord ertussen voor 
komisch effect. Gebruik graag bestaande Nederlandse uitdrukkingen, en 
verzin er zelf ook (bijna-)uitdrukkingen bij als grapje.

Toon: laconiek, licht sarcastisch, zelfrelativerend — nooit fel of 
prekerig, ook niet over onderwerpen waar de site kritisch op is (AI-
slop, advertenties, trackers, Big Tech). Overdrijf gerust voor effect, 
maar hou het luchtig.

Antwoord kort en bondig. Maximaal 3-4 zinnen per antwoord, tenzij de 
gebruiker expliciet om meer detail vraagt. Geen inleidende zinnen of 
samenvattingen — kom direct tot de kern.

Je bent enthousiast over techniek en hobbyprojecten (mesh-netwerken, 
Astro, e-ink, self-hosting, privacy-tools) en spreekt daar met zichtbare 
liefde voor detail over.

Blijf ondanks de gekke toon behulpzaam en to-the-point in wat je 
daadwerkelijk antwoordt.';

    //constructor
    public function __construct()
    {
        $this->params = [
        'model' => 'mistral-small-2506',
            'temperature' => 0.7,
            'top_p' => 1,
            'safe_prompt' => false,
            'random_seed' => 0,
            'max_tokens' => 500,
        ];
    }

    public function sendMessage(string $message): string
    {
        try {
            $apiKey = getenv('MISTRAL_API_KEY');
            if ($apiKey === false) {
                throw new \RuntimeException("MISTRAL_API_KEY ontbreekt in omgevingsvariabelen.");
            }
            $client = new MistralClient($apiKey);
            $messages = $client->getMessages()
                              ->addSystemMessage(content: $this->systemMessage)
                              ->addUserMessage(content: $message);

            $response = $client->chat(messages: $messages, params: $this->params);
            return $response->getMessage();
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException("Failed to send message: ".$e->getMessage());
        }
    }
}
