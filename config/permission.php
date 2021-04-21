<?php

/**
 * 功能權限設置相關
 * 資料格式說明：
 *     group_func_key：分類的 Key 值
 *     menu：[ 所屬該分類的功能清單
 *         [
 *             func_key：該功能的 Key 值,
 *             open: 開關
 *             options: []
 *         ],
 *         ...
 *     ],
 *     options: [
 *     ...
 */

return [
    [
        'group_func_key' => 10,
        'menu' => [
            [
                'func_key' => 1101,
                'open' => true,
            ],
            [
                'func_key' => 1102,
                'open' => true,
            ],
            [
                'func_key' => 1103,
                'open' => true,
            ],
        ],
    ]
];
