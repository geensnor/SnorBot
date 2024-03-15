<?php

require_once 'utilities.php';

test('Unix timestamp naar nederlanse datum formatteren', function () {
    expect(getFormattedDate(DateTime::createFromFormat('Ymd', '20240226')))->toBe('26 februari');

});

test('Interval in dagen in het nederlands voor 1 dag', function () {
    expect(getFormattedIntervalDays('20240304', '20240305'))->toBe('1 dag');
});

test('Interval in dagen in het nederlands voor meerdere dagen', function () {
    expect(getFormattedIntervalDays('20240304', '20240307'))->toBe('3 dagen');
});

test('Wielrenkalender URL werkt', function () {
    $parsedCalendar = getParsedCalendar('https://www.wielerkrant.be/wielrennen/wielerkalender24.ics');
    expect($parsedCalendar[0]->summary)->not->toBeNull();
});
