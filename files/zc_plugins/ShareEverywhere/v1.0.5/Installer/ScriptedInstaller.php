<?php
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    exit('Illegal access');
}

require_once dirname(__DIR__) . '/lib/ShareEverywhere.php';

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall(): bool
    {
        $this->addConfigurationKey(ShareEverywhere::KEY, [
            'configuration_title' => 'Share Everywhere settings',
            'configuration_value' => json_encode(ShareEverywhere::defaults(), JSON_THROW_ON_ERROR),
            'configuration_description' => 'Managed through Catalog > Share Everywhere.',
            'configuration_group_id' => 0,
            'sort_order' => 0,
        ]);
        if (!$this->getConfigurationKeyDetails(ShareEverywhere::KEY, true)) {
            return false;
        }
        require_once dirname(__DIR__) . '/admin/includes/functions/extra_functions/share_everywhere_menu.php';
        share_everywhere_sync_catalog_menu();
        return true;
    }

    protected function executeUpgrade(...$args): bool
    {
        return $this->executeInstall();
    }

    protected function executeUninstall(): bool
    {
        zen_deregister_admin_pages(['catalogShareEverywhere']);
        $this->deleteConfigurationKeys([ShareEverywhere::KEY]);
        return !$this->getConfigurationKeyDetails(ShareEverywhere::KEY, true);
    }
}
