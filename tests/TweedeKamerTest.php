<?php

include 'cl_TweedeKamer.php';

include 'utilities.php';

test('Geschenken Tweede Kamer ophalen', function () {

    $tk = new TweedeKamer();
    $tkObject = $tk->getGeschenk();

    expect($tkObject)->toBeObject();

});
