    <?php

return [
    'driver' => 'argon2i',

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
    ],

    'argon' => [
        'memory' => 65536,
        'threads' => 3,
        'time' => 4,
    ],
];