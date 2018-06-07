<?php

return [
    // default values
    'value'    => [
        'rows_number' => 20,
    ],
    // template customization
    'template' => [
        'table'  => [
            'container' => [
                'class' => 'table-responsive-lg',
            ],
            'item'      => [
                'class' => 'table-striped table-hover',
            ],
            'tr'    => [
                'class' => '',
            ],
            'th'    => [
                'class' => 'align-middle',
            ],
            'td'    => [
                'class' => 'align-middle',
            ],
            'thead'     => [
                'item' => [
                    'class' => '',
                ],
                'options-bar' => [
                    'tr'                   => [
                        'class' => '',
                    ],
                    'td'                   => [
                        'class' => 'border-0',
                    ],
                    'rows-number-selector' => [
                        'item'     => [
                            'class' => 'rows-number-selector col-sm-12 col-lg-4 pb-2',
                        ],
                        'lines'    => [
                            'container' => [
                                'class' => '',
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-list"></i>',
                                'class' => '',
                            ],
                        ],
                        'validate' => [
                            'container' => [
                                'class' => 'p-0',
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-check"></i>',
                                'class' => 'btn btn-link text-success',
                            ],
                        ],
                    ],
                    'spacer'               => [
                        'item' => [
                            'class' => 'spacer col-sm-2',
                        ]
                    ],
                    'search-bar'           => [
                        'item'     => [
                            'class' => 'col-sm-12 col-lg-6',
                        ],
                        'search'   => [
                            'container' => [
                                'class' => '',
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-search"></i>',
                                'class' => '',
                            ],
                        ],
                        'validate' => [
                            'container' => [
                                'class' => 'p-0',
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-check"></i>',
                                'class' => 'btn btn-link text-success',
                            ],
                        ],
                        'cancel'   => [
                            'container' => [
                                'class' => 'p-0',
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-times"></i>',
                                'class' => 'btn btn-link text-danger',
                            ],
                        ],
                    ],
                ],
                'titles-bar'  => [
                    'tr'   => [
                        'class' => '',
                    ],
                    'th'   => [
                        'class' => 'border-0',
                    ],
                    'sort' => [
                        'item'     => [
                            'class' => 'sort',
                        ],
                        'asc'      => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort-up"></i>',
                                'class' => '',
                            ],
                        ],
                        'desc'     => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort-down"></i>',
                                'class' => '',
                            ],
                        ],
                        'unsorted' => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort"></i>',
                                'class' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'tbody'  => [
            'tr' => [
                'class' => '',
            ],
            'td' => [
                'class' => '',
            ],
        ],
        'tfoot'  => [
            'tr'          => [
                'class' => '',
            ],
            'td'          => [
                'class' => '',
            ],
            'options-bar' => [
                'create'               => [
                    'container' => [
                        'class' => 'col-sm-4',
                    ],
                ],
                'navigation-status'    => [
                    'with-create-route'    => [
                        'container' => [
                            'class' => 'col-sm-4 text-center',
                        ],
                    ],
                    'without-create-route' => [
                        'container' => [
                            'class' => 'col-sm-6 text-left',
                        ],
                    ],
                ],
                'pagination-container' => [
                    'with-create-route'    => [
                        'container' => [
                            'class' => 'col-sm-4',
                        ],
                    ],
                    'without-create-route' => [
                        'container' => [
                            'class' => 'col-sm-6',
                        ],
                    ],
                ],
            ],
        ],
        'button' => [
            'create'  => [
                'class' => 'btn btn-success',
                'icon'  => '<i class="fas fa-fw fa-plus-circle"></i>',
            ],
            'edit'    => [
                'class' => 'btn btn-link text-primary p-1',
                'icon'  => '<i class="fas fa-fw fa-edit"></i>',
            ],
            'destroy' => [
                'class'                        => 'btn btn-link text-danger p-1',
                'icon'                         => '<i class="fas fa-fw fa-times-circle"></i>',
                'trigger-bootrap-native-modal' => true,
            ],
            'confirm' => [
                'class' => 'btn btn-success',
                'icon'  => '<i class="fas fa-fw fa-check"></i>',
            ],
            'cancel'  => [
                'class' => 'btn btn-danger',
                'icon'  => '<i class="fas fa-fw fa-ban"></i>',
            ],
        ],
    ],
];
