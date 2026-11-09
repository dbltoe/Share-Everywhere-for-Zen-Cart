<?php
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    exit('Illegal access');
}

class zcObserverShareEverywhereAdmin extends base
{
    public function __construct()
    {
        // Zen Cart 2.0 loads plugin observers even when plugin extra functions are skipped.
        require_once dirname(__DIR__, 2) . '/functions/extra_functions/share_everywhere_menu.php';
    }
}
