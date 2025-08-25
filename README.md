# De SnorBot

Middelpunt van de "Conversational UI" van [geensnor.nl](https://geensnor.nl).

Live te bewonderen op: [https://t.me/geensnorbot](https://t.me/geensnorbot)

Documentatie: [https://geensnor.github.io/SnorBot/](https://geensnor.github.io/SnorBot/)

## Lijsten

De SnorBot maakt gebruik van allerlei handige lijsten. Die komen uit De Digitale Tuin:  
[https://www.dedigitaletuin.nl/](https://www.dedigitaletuin.nl/)

## Settings

De bot heeft een `config.php` nodig om wat settings uit op te halen.  
Hernoem `config_example.php` naar `config.php` en vul de keys e.d. in.

## Testen

```sh
composer test
```

## Checken

```sh
composer check
```

## SnorBot Commando's Lijst

### Informatie & Hulp

- `/help` - Krijg hulpinformatie over de bot
- `/env` - Toon omgevingsinformatie
- `/chatid` - Toon de huidige chat ID

### Weer & Milieu

- `/weer`, `/weerbericht`, `/weersvoorspelling`, `/lekker weertje` - Krijg weersinformatie
- `/waarschuwing`, `/waarschuwingen`, `/code rood`, `/code geel` - Krijg weerswaarschuwingen
- `/temperatuur`, `/koud`, `/warm`, `/brr` - Krijg huidige temperatuur
- `/energie`, `/energiemix`, `/electriciteit` - Krijg informatie over hernieuwbare energie
- `/stroom`, `/stroomprijs` - Krijg huidige elektriciteitsprijzen

### Cryptocurrency

- `/bitcoin`, `/btc` - Krijg Bitcoin prijs
- `/eth` - Krijg Ethereum prijs
- `/crypto` - Krijg zowel Bitcoin als Ethereum prijzen
- `/ath` - Toon Bitcoin All Time High informatie

### Nieuws & Informatie

- `/nieuws` - Krijg laatste nieuws van NOS
- `/thuisarts` - Krijg laatste nieuws van thuisarts.nl
- `/wielrennieuws` - Krijg wielrennieuws
- `/hacker` - Krijg laatste Hacker News
- `/nieuwste post`, `/nieuwste bericht` - Krijg laatste blogpost van geensnor.nl
- `/random post`, `/random bericht` - Krijg willekeurige blogpost van geensnor.nl

### Wielrennen & Sport

- `/koers`, `/koersen`, `/wielrennen` - Krijg huidige wielerkoersen
- `/tourpoule`, `/tour`, `/poule`, `/stand poule`, `/stand tourpoule`, `/tussenstand` - Krijg Tour de France poule stand
- `/uitslag vandaag`, `/uitslag`, `/tourpoule vandaag`, `/etappe`, `/vandaag`, `/uitslag etappe` - Krijg uitslag van vandaag

### Overheid & Politiek

- `/kabinet` - Krijg laatste kabinetsnieuws
- `/geschenk` - Krijg laatste geschenk ontvangen door kamerleden
- `/activiteit` - Krijg activiteiten van vandaag in de Tweede Kamer

### Datum & Tijd

- `/dag van de`, `/het is vandaag`, `/dag`, `/dag van`, `/dagvan` - Krijg welke dag het vandaag is
- `/vandaag`, `/geschiedenis`, `/deze dag` - Krijg historische gebeurtenissen van vandaag
- `/week` - Krijg huidige weeknummer
- `/pi`, `/π` - Krijg waarde van pi
- `/is het al vijf uur`, `/is het al 5 uur` - Controleer of het 5 uur is
- `/1337` - Aftellen naar volgende 13:37

### Vermakelijk & Leuk

- `/xkcd` - Krijg willekeurige XKCD strip
- `/xkcd nieuwste` - Krijg laatste XKCD strip
- `/plaatje`, `/random plaatje`, `/vet plaatje`, `/kunst`, `/archillect` - Krijg willekeurige afbeelding van Archillect
- `/genereer wachtwoord` - Genereer willekeurig wachtwoord
- `/guid` - Genereer willekeurige GUID
- `/random snack`, `/snack` - Krijg willekeurige snack suggestie

### Persoonlijk & Sociaal

- `/verjaardag`, `/jarig`, `/verjaardagen` - Krijg verjaardagsinformatie (groepsspecifiek)
- `/goedemorgen`, `/goede morgen` - Krijg ochtendoverzicht met weer, crypto, nieuws en activiteiten

### Hulpmiddelen

- `/getal onder de [nummer]` - Genereer willekeurig nummer onder opgegeven waarde
- `/wiki [zoekterm]` - Zoek op Wikipedia
- `/sywert`, `/sywert van lienden` - Dagen sinds Sywert van Lienden's belofte

### Winkelen & Aanbiedingen

- `/winnen`, `/prijzenparade` - Krijg Tweakers December Prijzen Parade link

### Dynamische Content (van JSON lijsten)

- `/weetje` - Krijg willekeurig weetje
- `/nieuwste weetje` - Krijg laatste weetje
- `/haiku` - Krijg willekeurige haiku
- `/nieuwste haiku`, `/laatste haiku` - Krijg laatste haiku
- `/dooddoener` - Krijg willekeurige dooddoener
- `/politieke dooddoener`, `/debat`, `/oneliner`, `/clichee`, `/clichée` - Krijg politiek cliché
- `/verveeling`, `/verveel`, `/wat zal ik doen`, `/wat zal ik eens doen` - Krijg verveling bestrijder
- `/brabants`, `/alaaf`, `/brabant`, `/wa zedde gij` - Krijg Brabantse dialect uitdrukking

### Conversatie Reacties

De bot reageert ook op verschillende conversatie triggers zoals:

- Begroetingen (`hallo`, `hi`)
- Veelgebruikte zinnen (`hoe is het`, `dank je`, `sorry`)
- Reacties (`haha`, `lol`, `echt?`)
- Onderwerpen (`website`, `strava`, `git`, `recepten`, `eten`, etc.)

### Speciale Functies

- **Willekeurige reacties**: Als geen commando overeenkomt, geeft de bot een willekeurige reactie uit zijn database.

### Opmerkingen

- Sommige commando's zijn groepsspecifiek (zoals verjaardagen en weekendinformatie)
