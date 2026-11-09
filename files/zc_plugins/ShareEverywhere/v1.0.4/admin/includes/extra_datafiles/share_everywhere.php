<?php
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    exit('Illegal access');
}

if (!defined('FILENAME_SHARE_EVERYWHERE')) {
    define('FILENAME_SHARE_EVERYWHERE', 'share_everywhere');
}
