<?php
if (!defined('IS_ADMIN_FLAG')) {
    exit('Illegal access');
}

return [
    'pluginVersion' => 'v1.0.5',
    'pluginName' => 'Share Everywhere for Zen Cart',
    'pluginDescription' => 'Configurable product sharing, messaging, bookmarks, and store profiles.',
    'pluginAuthor' => 'Melanie Prough, PRO-Webs, Inc.',
    'pluginId' => 0, // No Zen Cart Plugins Library ID assigned yet.
    'zcVersions' => ['v200', 'v210', 'v220'],
    'pluginGroups' => [],
    'github_repo' => 'https://github.com/mprough/Share-Everywhere-for-Zen-Cart',
    'changelog' => 'https://github.com/mprough/Share-Everywhere-for-Zen-Cart/blob/main/CHANGELOG.md',
];
