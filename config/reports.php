<?php

use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Institution;
use App\Models\Program;

return [

    'attestation' => [
        'class'     => Attestation::class,
        'fillables' => (new Attestation())->getFillable(),
        'includes'  => [
            'institution.staff.user',
            'program',
            'cap.fedCap'
        ],
    ],
    'cap' => [
        'class'     => Cap::class,
        'fillables' => (new Cap())->getFillable(),
        'includes'  => [
            'institution',
            'institution.staff.user',
        ],
    ],
    'institution' => [
        'class'     => Institution::class,
        'fillables' => (new Institution())->getFillable(),
        'includes'  => [
            'caps',
            'staff.user',
        ],
    ],
    'program' => [
        'class'     => Program::class,
        'fillables' => (new Program())->getFillable(),
        'includes'  => [
            'institution',
            'cap',
        ],
    ],
];
