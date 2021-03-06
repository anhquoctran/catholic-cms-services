<?php
/**
 * Define all constant use for App
 */

if (!defined('DEFAULT_PAGINATION_PER_PAGE')) {
    define('DEFAULT_PAGINATION_PER_PAGE', 50);
}

if (!defined('DATE_TIME_FORMAT')) {
    define('DATE_TIME_FORMAT', 'Y-m-d H:i:s');
}

if (!defined('DATE_FORMAT')) {
    define('DATE_FORMAT', 'Y-m-d');
}

if (!defined('DATE_TIME_END_FORMAT')) {
    define('DATE_TIME_END_FORMAT', 'Y-m-d 23:59:59');
}

if(!defined('CHARGE_NEW')) {
    define('CHARGE_NEW', 0);
}

if(!defined('CHARGE_UPDATE')) {
    define('CHARGE_UPDATE', 1);
}

if(!defined('ASC')) {
    define('ASC', 0);
}

if(!defined('DESC')) {
    define('DESC', 1);
}

if(!defined('SORT_NONE')) {
    define('SORT_NONE', 2);
}

if (!defined('SEARCH_CONTRIBUTE_ALL')) {
    define('SEARCH_CONTRIBUTE_ALL', 0);
}

if (!defined('SEARCH_CONTRIBUTE_NEW')) {
    define('SEARCH_CONTRIBUTE_NEW', 1);
}

if (!defined('SEARCH_CONTRIBUTE_UPDATE')) {
    define('SEARCH_CONTRIBUTE_UPDATE', 2);
}

if (!defined('IS_DELETED')) {
    define('IS_DELETED', 1);
}