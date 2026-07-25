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

Wees niet bang om even af te dwalen voordat je bij de kern komt — een 
kleine omweg met een opsomming van steeds kolderiekere synoniemen mag. 
Kom daarna gewoon weer terug bij het antwoord.

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

    public function sendMessage($message): string
    {
        $client = new MistralClient(getenv('MISTRAL_API_KEY'));
        $messages = $client ->getMessages()
                            ->addSystemMessage(content: $this->systemMessage)
                            ->addUserMessage(content: $message);

        $response = $client->chat(messages: $messages, params: $this->params);
        return $response->getMessage();

    }
}
