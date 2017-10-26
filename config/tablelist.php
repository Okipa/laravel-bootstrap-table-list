<?php

return [
    // default values
    'default'  => [
        'rows_number' => 20,
    ],
    'template' => [
        'button' => [
            'edit'    => [
                'class' => 'btn btn-primary btn-rounded spin-on-click',
                'icon'  => '<i class="fa fa-pencil" aria-hidden="true"></i>',
            ],
            'destroy' => [
                'class' => 'btn btn-danger btn-rounded',
                'icon'  => '<i class="fa fa-times" aria-hidden="true"></i>',
            ],
            'confirm' => [
                'class' => 'btn btn-success spin-on-click',
                'icon'  => '<i class="fa fa-check" aria-hidden="true"></i>',
            ],
            'cancel'  => [
                'class' => 'btn btn-danger',
                'icon'  => '<i class="fa fa-ban" aria-hidden="true"></i>',
            ],
        ]
    ],
];
