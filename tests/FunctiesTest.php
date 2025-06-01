<?php

include 'functies.php';

test('Bitcoin prijs', function (): void {
    expect(getBitcoinPrice())->toBeString();
});

test('Vandaag van wikipedia', function (): void {
    $event = getVandaag();
    expect($event->year)->toBeString();
    expect($event->content)->toBeString();
});

test('YAML parser', function (): void {
    $yamlString = file_get_contents("tests/fixtures/test.yaml");
    $outputParseYaml = parseYaml($yamlString);
    expect($outputParseYaml[0]->jaar)->toBe("2016");
});
