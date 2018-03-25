<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', str_random(32)),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */
    'locale' => env('APP_LOCALE', 'vi'),
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'vi'),

     'entities' => [
        'member' => 'membertbl',
        'parish' => 'parishtbl',
        'contribute_history' => 'contributehistorytbl',
        'login_history' => 'loginhistorytbl'
    ],

	'diocese_code' => [
		'HN' => 'Tổng giáo phận Hà Nội',
		'BN' => 'Giáo phận Bắc Ninh',
		'BC' => 'Giáo phận Bùi Chu',
		'HP' => 'Giáo phận Hải Phòng',
		'HH' => 'Giáo phận Hưng Hóa',
		'LS' => 'Giáo phận Lạng Sơn và Cao Bằng',
		'PD' => 'Giáo phận Phát Diệm',
		'TB' => 'Giáo phận Thái Bình',
		'TH' => 'Giáo phận Thanh Hóa',
		'VI' => 'Giáo phận Vinh',
		'HU' => 'Tổng giáo phận Huế',
		'BM' => 'Giáo phận Buôn Mê Thuộc',
		'DN' => 'Giáo phận Đà Nẵng',
		'KT' => 'Giáo phận Kontum',
		'NT' => 'Giáo phận Nha Trang',
		'QN' => 'Giáo phận Qui Nhơn',
		'SG' => 'Tổng giáo phận TP. HCM',
		'BR' => 'Giáo phận Bà Rịa',
		'CT' => 'Giáo phận Cần Thơ',
		'DL' => 'Giáo phận Đà Lạt',
		'LX' => 'Giáo phận Long Xuyên',
		'MT' => 'Giáo phận Mỹ Tho',
		'PT' => 'Giáo phận Phan Thiết',
		'PC' => 'Giáo phận Phú Cường',
		'VL' => 'Giáo phận Vĩnh Long',
		'XL' => 'Giáo phận Xuân Lộc',
		'NN' => 'Giáo phận nước ngoài',
		'UN' => 'Chưa rõ'
	]
];
