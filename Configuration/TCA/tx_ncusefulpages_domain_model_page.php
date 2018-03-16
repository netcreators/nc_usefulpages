<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ncusefulpages_domain_model_page');

return [
    'ctrl' => [
        'title' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page',
        'label' => 'page_i_d',
        'label_alt' => 'page_title',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:nc_usefulpages/Resources/Public/Icons/tx_ncusefulpages_domain_model_page.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'page_i_d,page_title,page_u_r_l,page_parameters,useful,notuseful,undecided',
    ],
    'types' => [
        '1' => ['showitem' => 'page_i_d,page_title,page_u_r_l,page_parameters,useful,notuseful,undecided,comments'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.php:LGL.default_value', 0]
                ],
            ]
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_ncusefulpages_domain_model_page',
                'foreign_table_where' => 'AND tx_ncusefulpages_domain_model_page.uid=###REC_FIELD_l18n_parent### AND tx_ncusefulpages_domain_model_page.sys_language_uid IN (-1,0)',
            ]
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27,
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
            ]
        ],
        'page_i_d' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.page_i_d',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ],
        ],
        'page_title' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.page_title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'page_u_r_l' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.page_u_r_l',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'page_parameters' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.page_parameters',
            'config' => [
                'type' => 'text',
                'rows' => 5,
                'cols' => 18
            ]
        ],
        'useful' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.useful',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ],
        ],
        'notuseful' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.notUseful',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ],
        ],
        'undecided' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.undecided',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ],
        ],
        'comments' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:nc_usefulpages/Resources/Private/Language/locallang_db.xml:tx_ncusefulpages_domain_model_page.comments',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_ncusefulpages_domain_model_comment',
                'foreign_field' => 'page',
                'size' => 10,
                'maxitems' => 9999,
                'autoSizeMax' => 30,
                'multiple' => 0,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ]
            ]
        ],
    ],
];
