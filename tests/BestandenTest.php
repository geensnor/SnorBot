<?php

test('Antwoorden JSON ziet er mooi uit', function (): void {
    $antwoordenArray = json_decode(file_get_contents('snorBotAntwoorden.json'));
    expect($antwoordenArray)->toBeArray();
});
