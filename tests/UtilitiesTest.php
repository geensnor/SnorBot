<?php

require_once 'utilities.php';

test('Naar nederlanse datum formatteren', function () {
    expect(getFormattedDate('20240304'))->toBe('4 maart');
});

test('Interval in dagen in het nederlands voor 1 dag', function () {
    expect(getFormattedIntervalDays('20240304', '20240305'))->toBe('1 dag');
});

test('Interval in dagen in het nederlands voor meerdere dagen', function () {
    expect(getFormattedIntervalDays('20240304', '20240307'))->toBe('3 dagen');
});
