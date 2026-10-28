<?php

return [
    'prefix' => 'trv',
    'token' => 'Xx4cK6o0uAIR3NELjUjxFSaccPS8I8rBIwfhL3Zvg1oikZP6DvsBdf61PwbsDvQrxWNIvMlOrrQW1HLSN7XD2vq2uMyzZ3lnOtXyHk0MDkrWLMPT00HwmQbJ',
    'version' => '1.0.0',
    'start_point_url' => '/trv/trivia',
    'description' => 'Trivia Game',
    'creators_name' => 'Kaspars Labais',
    'creators_url' => 'https://github.com/KasparsLabais',
    'image_url' => '/vendor/trivia-game/images/trivia-logo-alt.png',
    // Public base URL used for player join links / QR codes. Leave null to use
    // the app's own URL (APP_URL / current host); set GAME_JOIN_URL to a short
    // share domain (e.g. https://is-a.gay) in production.
    'join_url' => env('GAME_JOIN_URL'),
];