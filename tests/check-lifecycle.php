<?php
declare(strict_types=1);

namespace Zencart\PluginSupport {
    class ScriptedInstaller
    {
        public array $settings = [];
        public function install(): bool { return $this->executeInstall(); }
        public function upgrade(): bool { return $this->executeUpgrade('v0.9.0'); }
        public function uninstall(): bool { return $this->executeUninstall(); }
        protected function addConfigurationKey(string $key, array $properties): int
        {
            $this->settings[$key] ??= $properties['configuration_value'];
            return 1;
        }
        protected function getConfigurationKeyDetails(string $key, bool $exists): bool
        {
            return isset($this->settings[$key]);
        }
        protected function deleteConfigurationKeys(array $keys): int
        {
            foreach ($keys as $key) unset($this->settings[$key]);
            return count($keys);
        }
    }
}

namespace {
    define('IS_ADMIN_FLAG', true);
    define('TABLE_ADMIN_PAGES', 'admin_pages');
    $pages = ['unrelated' => ['something']];
    $db = new class {
        public function Execute(string $sql, int $limit): object
        {
            check($sql === "SELECT main_page FROM admin_pages WHERE page_key = 'catalogShareEverywhere'" && $limit === 1,
                'Unexpected menu lookup.');
            $page = $GLOBALS['pages']['catalogShareEverywhere'] ?? null;
            return (object)['EOF' => $page === null, 'fields' => ['main_page' => $page[1] ?? null]];
        }
    };
    function zen_db_perform(string $table, array $fields, string $type, string $where): void
    {
        check($table === TABLE_ADMIN_PAGES && $type === 'UPDATE'
            && $where === "page_key = 'catalogShareEverywhere' AND main_page = 'share_everywhere'",
            'Menu repair must target only the legacy row.');
        $GLOBALS['pages']['catalogShareEverywhere'][1] = $fields['main_page'];
    }
    function zen_register_admin_page(string $key, ...$details): void
    {
        $GLOBALS['pages'][$key] = $details;
    }
    function zen_deregister_admin_pages(array $keys): void
    {
        foreach ($keys as $key) unset($GLOBALS['pages'][$key]);
    }
    function check(bool $passed, string $message): void
    {
        if (!$passed) throw new RuntimeException($message);
    }

    $root = dirname(__DIR__) . '/files/zc_plugins/ShareEverywhere/v1.0.6';
    // A request can load the installed version's helpers before the new installer.
    require dirname($root) . '/v1.0.0/admin/includes/functions/extra_functions/share_everywhere_menu.php';
    require $root . '/Installer/ScriptedInstaller.php';
    $adminPage = file_get_contents($root . '/admin/share_everywhere.php');
    check(strpos($adminPage, "require DIR_WS_INCLUDES . 'admin_html_head.php';") < strpos($adminPage, "'header.php';")
        && str_contains($adminPage, "require DIR_WS_INCLUDES . 'admin_html_head.php';")
        && !str_contains($adminPage, 'includes/stylesheet.css'), 'Use the supported Zen Cart admin head before the header.');
    $installer = new ScriptedInstaller();
    check($installer->install(), 'Install failed.');
    check($pages['catalogShareEverywhere'] === ['BOX_CATALOG_SHARE_EVERYWHERE', 'FILENAME_SHARE_EVERYWHERE', '', 'catalog', 'Y'],
        'Catalog menu registration is incorrect.');
    $settings = json_decode($installer->settings[ShareEverywhere::KEY], true);
    check(is_array($settings) && !$settings['enabled'], 'Install defaults are incorrect.');
    $installer->settings[ShareEverywhere::KEY] = '{"enabled":true}';
    check(defined($pages['catalogShareEverywhere'][1]) && constant($pages['catalogShareEverywhere'][1]) === 'share_everywhere',
        'Zen Cart would skip a menu target that is not a defined filename constant.');
    $pages['catalogShareEverywhere'][1] = 'share_everywhere';
    $pages['catalogShareEverywhere'][4] = 'N';
    check($installer->upgrade() && $installer->settings[ShareEverywhere::KEY] === '{"enabled":true}', 'Upgrade replaced settings.');
    check($pages['catalogShareEverywhere'][1] === 'FILENAME_SHARE_EVERYWHERE', 'Upgrade did not repair the 1.0.0 menu.');
    check($pages['catalogShareEverywhere'][4] === 'N', 'Upgrade replaced the existing menu registration.');

    unset($pages['catalogShareEverywhere']);
    // Admin bootstrap has already included the helper before loading settings.
    require_once $root . '/admin/includes/functions/extra_functions/share_everywhere_menu.php';
    check(!isset($pages['catalogShareEverywhere']), 'Bootstrap registered a menu before settings loaded.');
    define(ShareEverywhere::KEY, $installer->settings[ShareEverywhere::KEY]);
    class base {}
    require $root . '/admin/includes/classes/observers/auto.share_everywhere_admin.php';
    new zcObserverShareEverywhereAdmin();
    check(isset($pages['catalogShareEverywhere']), 'Runtime menu repair did not run.');
    check($installer->uninstall() && $pages === ['unrelated' => ['something']], 'Uninstall affected an unrelated registration.');
    echo "Installer defaults, upgrade preservation, menu repair, and uninstall checks passed.\n";
}
