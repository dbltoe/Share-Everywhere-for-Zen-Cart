<?php
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    exit('Illegal access');
}

require_once dirname(__DIR__, 2) . '/extra_datafiles/share_everywhere.php';

if (!defined('BOX_CATALOG_SHARE_EVERYWHERE')) {
    define('BOX_CATALOG_SHARE_EVERYWHERE', 'Share Everywhere');
}

if (!function_exists('share_everywhere_register_page')) {
    function share_everywhere_register_page(): void
    {
        if (!zen_page_key_exists('catalogShareEverywhere')) {
            zen_register_admin_page(
                'catalogShareEverywhere', 'BOX_CATALOG_SHARE_EVERYWHERE', FILENAME_SHARE_EVERYWHERE,
                '', 'catalog', 'Y'
            );
        }
    }
}

if (defined('SHARE_EVERYWHERE_SETTINGS') && function_exists('zen_register_admin_page')) {
    share_everywhere_register_page();
}
