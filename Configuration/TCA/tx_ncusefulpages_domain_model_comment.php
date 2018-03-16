<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ncusefulpages_domain_model_comment');

return [
    'ctrl' => [
        'title' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_comment',
        'label' => 'content',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            // no 'hidden' field necessary for these comments, as they are a BE-only feature.
        ],
        'iconfile' => 'EXT:nc_usefulpages/Resources/Public/Icons/tx_ncusefulpages_domain_model_comment.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'rating, content, author_name, author_email'
    ],
    'types' => [
        '1' => ['showitem' => 'rating, content, author_name, author_email']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ],
    'columns' => [
        'content' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_comment.content',
            'config' => [
                'type' => 'text',
                'rows' => 15,
                'cols' => 80
            ]
        ],
        'page' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'rating' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_comment.rating',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '-',
                        // Empty option despite 'eval'=>'required' to allow smooth transition without necessity of updating old records.
                        0
                    ],

                    [
                        'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.useful',
                        \Netcreators\NcUsefulpages\Controller\CommentController::RATING_USEFUL
                    ],

                    [
                        'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.notUseful',
                        \Netcreators\NcUsefulpages\Controller\CommentController::RATING_NOT_USEFUL
                    ],

                    [
                        'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.undecided',
                        \Netcreators\NcUsefulpages\Controller\CommentController::RATING_UNDECIDED
                    ]
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ],
        ],
        'author_name' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_comment.author_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => '255'
            ],
        ],
        'author_email' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_comment.author_email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => '255'
            ],
        ],
    ]
];

