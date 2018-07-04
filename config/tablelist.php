<?php

return [
    // default values
    'value'    => [
        'rows_number'      => 20,
        'disabled_line'    => [
            'class' => ['disabled', 'bg-secondary', 'text-white'],
        ],
        'highlighted_line' => [
            'class' => ['highlighted', 'bg-info', 'text-white'],
        ],
    ],
    // template customization
    'template' => [
        'table' => [
            // table > global
            'container' => [
                'class' => ['table-responsive'],
            ],
            'item'      => [
                'class' => ['table-striped', 'table-hover'],
            ],
            'tr'        => [
                'class' => [],
            ],
            'th'        => [
                'class' => ['align-middle'],
            ],
            'td'        => [
                'class' => ['align-middle'],
            ],
            // table > header
            'thead'     => [
                'item'        => [
                    'class' => [],
                ],
                // table > header > options bar
                'options-bar' => [
                    'tr'                   => [
                        'class' => [],
                    ],
                    'td'                   => [
                        'class' => ['border-0'],
                    ],
                    // table > header > options bar > row number selector
                    'rows-number-selector' => [
                        'item'     => [
                            'class' => ['col-sm-12', 'col-lg-4', 'pb-2'],
                        ],
                        'lines'    => [
                            'container' => [
                                'class' => [],
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-list"></i>',
                                'class' => [],
                            ],
                        ],
                        'validate' => [
                            'container' => [
                                'class' => ['p-0'],
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-check"></i>',
                                'class' => ['btn', 'btn-link', 'text-success'],
                            ],
                        ],
                    ],
                    // table > header > options bar > spacer
                    'spacer'               => [
                        'item' => [
                            'class' => ['spacer', 'col-sm-2'],
                        ],
                    ],
                    // table > header > options bar > search bar
                    'search-bar'           => [
                        'item'     => [
                            'class' => ['col-sm-12', 'col-lg-6'],
                        ],
                        'search'   => [
                            'container' => [
                                'class' => [],
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-search"></i>',
                                'class' => [],
                            ],
                        ],
                        'validate' => [
                            'container' => [
                                'class' => ['p-0'],
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-check"></i>',
                                'class' => ['btn', 'btn-link', 'text-success'],
                            ],
                        ],
                        'cancel'   => [
                            'container' => [
                                'class' => ['p-0'],
                            ],
                            'item'      => [
                                'icon'  => '<i class="fas fa-fw fa-times"></i>',
                                'class' => ['btn', 'btn-link', 'text-danger'],
                            ],
                        ],
                    ],
                ],
                // table > header > titles bar
                'titles-bar'  => [
                    'tr'   => [
                        'class' => [],
                    ],
                    'th'   => [
                        'class' => ['border-0'],
                    ],
                    // table > header > titles bar > sort
                    'sort' => [
                        'item'     => [
                            'class' => ['sort'],
                        ],
                        'asc'      => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort-up"></i>',
                                'class' => [],
                            ],
                        ],
                        'desc'     => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort-down"></i>',
                                'class' => [],
                            ],
                        ],
                        'unsorted' => [
                            'item' => [
                                'icon'  => '<i class="fas fa-fw fa-sort"></i>',
                                'class' => [],
                            ],
                        ],
                    ],
                ],
            ],
            // table > body
            'tbody'     => [
                'tr'      => [
                    'class' => [],
                ],
                'td'      => [
                    'class' => [],
                ],
                // table > body > edit
                'edit'    => [
                    'container' => [
                        'class' => ['d-table-cell'],
                    ],
                    'item'      => [
                        'class' => ['btn', 'btn-link', 'text-primary', 'p-1'],
                        'icon'  => '<i class="fas fa-fw fa-edit"></i>',
                    ],
                ],
                // table > body > destroy
                'destroy' => [
                    'container'               => [
                        'class' => ['d-table-cell'],
                    ],
                    'item'                    => [
                        'class' => ['btn', 'btn-link', 'text-danger', 'p-1'],
                        'icon'  => '<i class="fas fa-fw fa-times-circle"></i>',
                    ],
                    'trigger-bootstrap-modal' => true,
                ],
            ],
            // table > footer
            'tfoot'     => [
                'item'        => [
                    'class' => [],
                ],
                'tr'          => [
                    'class' => [],
                ],
                'td'          => [
                    'class' => [],
                ],
                // table > footer > options bar
                'options-bar' => [
                    'item'       => [
                        'class' => ['row'],
                    ],
                    // table > footer > options bar > create
                    'create'     => [
                        'container' => [
                            'class' => ['col-sm-4'],
                        ],
                        'item'      => [
                            'class' => ['btn', 'btn-success'],
                            'icon'  => '<i class="fas fa-fw fa-plus-circle"></i>',
                        ],
                    ],
                    // table > footer > options bar > navigation
                    'navigation' => [
                        'with-create-route'    => [
                            'container' => [
                                'class' => ['col-sm-4', 'text-center'],
                            ],
                        ],
                        'without-create-route' => [
                            'container' => [
                                'class' => ['col-sm-6', 'text-left'],
                            ],
                        ],
                    ],
                    // table > footer > options bar > pagination
                    'pagination' => [
                        'with-create-route'    => [
                            'container' => [
                                'class' => ['col-sm-4'],
                            ],
                        ],
                        'without-create-route' => [
                            'container' => [
                                'class' => ['col-sm-6'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        // modal
        'modal' => [
            'container' => [
                'class' => [],
            ],
            'item'      => [
                'class' => ['modal-lg'],
            ],
            'title'     => [
                'container' => [
                    'class' => ['text-danger'],
                ],
                'item'      => [
                    'class' => [],
                    'icon'  => '<i class="fas fa-exclamation-triangle"></i>',
                ],
            ],
            'body'      => [
                'item' => [
                    'class' => [],
                ],
            ],
            'footer'    => [
                'item'    => [
                    'class' => [],
                ],
                'confirm' => [
                    'item' => [
                        'class' => ['btn', 'btn-success'],
                        'icon'  => '<i class="fas fa-fw fa-check"></i>',
                    ],
                ],
                'cancel'  => [
                    'item' => [
                        'class' => ['btn', 'btn-danger'],
                        'icon'  => '<i class="fas fa-fw fa-ban"></i>',
                    ],
                ],
            ],
        ],
    ],
];
