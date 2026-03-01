<?php

test('Random snack API werkt nog lekker', function () {

    $response = json_decode(file_get_contents('https://europe-west1-speedy-realm-379713.cloudfunctions.net/generate-snack-v1'), true);
    expect($response["snack"])->not()->toBeEmpty();
    expect($response["price"])->not()->toBeEmpty();
});
