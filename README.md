# De SnorBot

Middelpunt van de "Conversational UI" van geensnor.nl.

Live te bewonderen op: [https://t.me/geensnorbot](https://t.me/geensnorbot)

Documentatie: [https://geensnor.github.io/SnorBot/](https://geensnor.github.io/SnorBot/)

## Lijsten

De SnorBot maakt gebruik van allerlei handige lijsten. Die komen uit De Digitale Tuin:
[https://www.dedigitaletuin.nl/](https://www.dedigitaletuin.nl/)

## Settings

Bot heeft een config.php nodig om wat settings uit op te halen. Hernoem config_example.php naar config.php en vul de key's e.d.

## Testen

`composer test`

## Checken

`composer check`

# SnorBot Commando's Lijst

## **Informatie & Hulp**
- `/help` - Krijg hulpinformatie over de bot
- `/env` - Toon omgevingsinformatie
- `/chatid` - Toon de huidige chat ID

## **Weer & Milieu**
- `/weer` of `/weerbericht` of `/weersvoorspelling` of `/lekker weertje` - Krijg weersinformatie
- `/waarschuwing` of `/waarschuwingen` of `/code rood` of `/code geel` - Krijg weerswaarschuwingen
- `/temperatuur` of `/koud` of `/warm` of `/brr` - Krijg huidige temperatuur
- `/energie` of `/energiemix` of `/electriciteit` - Krijg informatie over hernieuwbare energie
- `/stroom` of `/stroomprijs` - Krijg huidige elektriciteitsprijzen

## **Cryptocurrency**
- `/bitcoin` of `/btc` - Krijg Bitcoin prijs
- `/eth` - Krijg Ethereum prijs
- `/crypto` - Krijg zowel Bitcoin als Ethereum prijzen
- `/ath` - Toon Bitcoin All Time High informatie

## **Nieuws & Informatie**
- `/nieuws` - Krijg laatste nieuws van NOS
- `/thuisarts` - Krijg laatste nieuws van thuisarts.nl
- `/wielrennieuws` - Krijg wielrennieuws
- `/hacker` - Krijg laatste Hacker News
- `/nieuwste post` of `/nieuwste bericht` - Krijg laatste blogpost van geensnor.nl
- `/random post` of `/random bericht` - Krijg willekeurige blogpost van geensnor.nl

## **Wielrennen & Sport**
- `/koers` of `/koersen` of `/wielrennen` - Krijg huidige wielerkoersen
- `/tourpoule` of `/tour` of `/poule` of `/stand poule` of `/stand tourpoule` of `/tussenstand` - Krijg Tour de France poule stand
- `/uitslag vandaag` of `/uitslag` of `/tourpoule vandaag` of `/etappe` of `/vandaag` of `/uitslag etappe` - Krijg uitslag van vandaag

## **Overheid & Politiek**
- `/kabinet` - Krijg laatste kabinetsnieuws
- `/geschenk` - Krijg laatste geschenk ontvangen door kamerleden
- `/activiteit` - Krijg activiteiten van vandaag in de Tweede Kamer

## **Datum & Tijd**
- `/dag van de` of `/het is vandaag` of `/dag` of `/dag van` of `/dagvan` - Krijg welke dag het vandaag is
- `/vandaag` of `/geschiedenis` of `/deze dag` - Krijg historische gebeurtenissen van vandaag
- `/week` - Krijg huidige weeknummer
- `/pi` of `/π` - Krijg waarde van pi
- `/is het al vijf uur` of `/is het al 5 uur` - Controleer of het 5 uur is
- `/1337` - Aftellen naar volgende 13:37

## **Vermakelijk & Leuk**
- `/xkcd` - Krijg willekeurige XKCD strip
- `/xkcd nieuwste` - Krijg laatste XKCD strip
- `/plaatje` of `/random plaatje` of `/vet plaatje` of `/kunst` of `/archillect` - Krijg willekeurige afbeelding van Archillect
- `/genereer wachtwoord` - Genereer willekeurig wachtwoord
- `/guid` - Genereer willekeurige GUID
- `/random snack` of `/snack` - Krijg willekeurige snack suggestie

## **Persoonlijk & Sociaal**
- `/verjaardag` of `/jarig` of `/verjaardagen` - Krijg verjaardagsinformatie (groepsspecifiek)
- `/voornaam` of `/naam` of `/babynaam` - Krijg willekeurige voornaam suggestie
- `/goedemorgen` of `/goede morgen` - Krijg ochtendoverzicht met weer, crypto, nieuws en activiteiten

## **Locatie & Advies**
- `/advies` - Vraag locatiegebaseerd advies (vereist locatiedeling)
- *Locatie delen* - Krijg advies in de buurt wanneer locatie wordt gedeeld

## **Hulpmiddelen**
- `/getal onder de [nummer]` - Genereer willekeurig nummer onder opgegeven waarde
- `/wiki [zoekterm]` - Zoek op Wikipedia
- `/sywert` of `/sywert van lienden` - Dagen sinds Sywert van Lienden's belofte

## **Winkelen & Aanbiedingen**
- `/winnen` of `/prijzenparade` - Krijg Tweakers December Prijzen Parade link

## **Dynamische Content (van JSON lijsten)**
- `/weetje` - Krijg willekeurig weetje
- `/nieuwste weetje` - Krijg laatste weetje
- `/haiku` - Krijg willekeurige haiku
- `/nieuwste haiku` of `/laatste haiku` - Krijg laatste haiku
- `/dooddoener` - Krijg willekeurige dooddoener
- `/politieke dooddoener` of `/debat` of `/oneliner` of `/clichee` of `/clichée` - Krijg politiek cliché
- `/verveeling` of `/verveel` of `/wat zal ik doen` of `/wat zal ik eens doen` - Krijg verveling bestrijder
- `/brabants` of `/alaaf` of `/brabant` of `/wa zedde gij` - Krijg Brabantse dialect uitdrukking

## **Conversatie Reacties**
De bot reageert ook op verschillende conversatie triggers zoals:
- Begroetingen (`hallo`, `hi`)
- Veelgebruikte zinnen (`hoe is het`, `dank je`, `sorry`)
- Reacties (`haha`, `lol`, `echt?`)
- Onderwerpen (`website`, `strava`, `git`, `recepten`, `eten`, etc.)

## **Speciale Functies**
- **Jaar vragen** (elk 4-cijferig jaar) - Krijg weekendinformatie voor dat jaar (groepsspecifiek)
- **Willekeurige reacties** - Als geen commando overeenkomt, geeft de bot een willekeurige reactie uit zijn database

## **Opmerkingen**
- Sommige commando's zijn groepsspecifiek (zoals verjaardagen en weekendinformatie)
- De bot ondersteunt zowel `/commando` als `commando` formaten
- Locatiegebaseerde functies vereisen locatiedeling toestemming
- Veel commando's geven informatie in Markdown formaat met links en opmaak

