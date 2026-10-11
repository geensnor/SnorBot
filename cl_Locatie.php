<?php

declare(strict_types=1);

/**
 * Locatie
 *
 */
final readonly class Locatie
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    /** Afstand in km (haversine) */
    public function afstandTot(Locatie $andere): float
    {
        $r = 6371;
        $dLat = deg2rad($andere->latitude - $this->latitude);
        $dLon = deg2rad($andere->longitude - $this->longitude);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($this->latitude)) * cos(deg2rad($andere->latitude)) * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

/**
 * Hotspot van Geensnor. Geladen uit een GeoJSON-bestand.
 *
 */

final readonly class Hotspot
{
    public function __construct(
        public Locatie $locatie,
        public string $naam,
        public string $categorie,
        public string $omschrijving,
        public string $plaatsnaam,
        public bool $vermijden = false,
        public ?string $afbeelding = null,
        public ?string $website = null,
        public ?\DateTimeImmutable $bezochtOp = null,
        public ?\DateTimeImmutable $gewijzigdOp = null,
    ) {
    }

    /** @param array<string, mixed> $feature Eén GeoJSON Feature uit het schema */
    public static function vanFeature(array $feature): self
    {
        $p = $feature['properties'];
        [$lon, $lat] = $feature['geometry']['coordinates']; // GeoJSON: lon eerst!

        return new self(
            locatie: new Locatie((float) $lat, (float) $lon),
            naam: $p['name'],
            categorie: $p['category'],
            omschrijving: $p['description'],
            plaatsnaam: $p['placeName'],
            vermijden: $p['avoid'] ?? false,
            afbeelding: $p['image'] ?? null,
            website: $p['website'] ?? null,
            bezochtOp: isset($p['visitedAt']) ? new \DateTimeImmutable($p['visitedAt']) : null,
            gewijzigdOp: isset($p['updatedAt']) ? new \DateTimeImmutable($p['updatedAt']) : null,
        );
    }
}

/**
 * Hele lijst van hotspots.
 *
 */
final readonly class HotspotCollectie
{
    /** @param list<Hotspot> $hotspots */
    public function __construct(
        public string $naam,
        private array $hotspots,
    ) {
    }

    public static function vanBestand(string $pad): self
    {
        $data = json_decode(file_get_contents($pad), true, flags: JSON_THROW_ON_ERROR);

        return new self(
            $data['name'],
            array_map(Hotspot::vanFeature(...), $data['features']),
        );
    }

    /**
     * De $aantal dichtstbijzijnde hotspots, gesorteerd op afstand.
     *
     * @return list<LocatieBezoek>
     */
    public function dichtstbijzijnde(Locatie $huidig, int $aantal = 5): array
    {
        $bezoeken = array_map(
            fn (Hotspot $h) => LocatieBezoek::vanuit($huidig, $h),
            $this->hotspots
        );

        usort($bezoeken, fn ($a, $b) => $a->afstand <=> $b->afstand);

        return array_slice($bezoeken, 0, $aantal);
    }

    /**
     * De random hotspot met afstand.
     *
     *
     */
    public function geefRandom(): Hotspot
    {
        return $this->hotspots[array_rand($this->hotspots)];
    }
}

final readonly class LocatieBezoek
{
    public function __construct(
        public Hotspot $hotspot,
        public float $afstand,
    ) {
    }

    public static function vanuit(
        Locatie $huidig,
        Hotspot $hotspot,
    ): self {
        return new self(
            $hotspot,
            $huidig->afstandTot($hotspot->locatie),
        );
    }
}
