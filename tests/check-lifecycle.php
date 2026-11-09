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
    $pages = ['unrelated' => ['something']];
    function zen_page_key_exists(string $key): bool { return isset($GLOBALS['pages'][$key]); }
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

    $root = dirname(__DIR__) . '/files/zc_plugins/ShareEverywhere/v1.0.0';
    require $root . '/Installer/ScriptedInstaller.php';
    $installer = new ScriptedInstaller();
    check($installer->install(), 'Install failed.');
    check($pages['catalogShareEverywhere'] === ['BOX_CATALOG_SHARE_EVERYWHERE', 'share_everywhere', '', 'catalog', 'Y'],
        'Catalog menu registration is incorrect.');
    $settings = json_decode($installer->settings[ShareEverywhere::KEY], true);
    check(is_array($settings) && !$settings['enabled'], 'Install defaults are incorrect.');
    $installer->settings[ShareEverywhere::KEY] = '{"enabled":true}';
    $pages['catalogShareEverywhere'][4] = 'N';
    check($installer->upgrade() && $installer->settings[ShareEverywhere::KEY] === '{"enabled":true}', 'Upgrade replaced settings.');
    check($pages['catalogShareEverywhere'][4] === 'N', 'Upgrade replaced the existing menu registration.');

    unset($pages['catalogShareEverywhere']);
    define(ShareEverywhere::KEY, $installer->settings[ShareEverywhere::KEY]);
    class base {}
    require $root . '/admin/includes/classes/observers/auto.share_everywhere_admin.php';
    // Simulate a later request in which the bootstrap has already been included by core.
    include $root . '/admin/includes/functions/extra_functions/share_everywhere_menu.php';
    new zcObserverShareEverywhereAdmin();
    check(isset($pages['catalogShareEverywhere']), 'Runtime menu repair did not run.');
    check($installer->uninstall() && $pages === ['unrelated' => ['something']], 'Uninstall affected an unrelated registration.');
    echo "Installer defaults, upgrade preservation, menu repair, and uninstall checks passed.\n";
}
