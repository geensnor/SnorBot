<?php

declare(strict_types=1);

/**
 * Een klasse die de schandalen lijst bevat.
 */

class schandalenLijst
{
    private array $schandalen;

    public function __construct(string $jsonLokatie)
    {
        $this->schandalen = json_decode(file_get_contents($jsonLokatie));
    }

    /**
     * Factory maakt van een stdClass object een Schandaal object.
     *
     * @return schandaal
     */
    private function maakSchandaal(stdClass $data): schandaal
    {
        return new schandaal(
            $data->datum,
            $data->schandaal,
            $data->bron,
            $data->url,
        );
    }
    /**
     * Haalt een random schandaal op.
     *
     * @return schandaal|null Geeft een schandaal terug of null als er geen schandalen zijn
     */
    public function getWillekeurigSchandaal(): ?schandaal
    {
        if (empty($this->schandalen)) {
            return null;
        }

        $willekeurigeIndex = array_rand($this->schandalen);
        return $this->maakSchandaal($this->schandalen[$willekeurigeIndex]);
    }

    /**
     * Haalt het laatste schandaal op.
     *
     * @return schandaal|null Geeft een schandaal terug of null als er geen schandalen zijn
     */
    public function getLaatsteSchandaal(): ?schandaal
    {
        if (empty($this->schandalen)) {
            return null;
        }
        return $this->maakSchandaal($this->schandalen[0]);
    }
}

/**
 * Een klasse van een schandaal.
 */
class schandaal
{
    public function __construct(
        string $datum,
        string $schandaal,
        string $bron,
        string $url,
    ) {
        $this->datum = $datum;
        $this->schandaal = $schandaal;
        $this->bron = $bron;
        $this->url = $url;
    }
    public readonly string $datum;
    public readonly string $schandaal;
    public readonly string $bron;
    public readonly string $url;
}

/**
 * Een klasse die de tekstversie van een schandaal bevat.
 */

class schandaalTekst
{
    public static function geefTekstRandom(schandaal $schandaal): string
    {
        return "Schandaal van {$schandaal->datum}:\n\n{$schandaal->schandaal}\n\nbron: [{$schandaal->bron}]({$schandaal->url})";
    }

    public static function geefTekstLaatste(schandaal $schandaal): string
    {
        return "Het meest recente Meta schandaal van {$schandaal->datum}\n\n{$schandaal->schandaal}\n\nbron: [{$schandaal->bron}]({$schandaal->url})";
    }

}
