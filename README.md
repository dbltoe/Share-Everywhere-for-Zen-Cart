# Share Everywhere for Zen Cart

Share Everywhere adds a configurable sharing and profile widget to product pages in Zen Cart ecommerce software. The first release is a Plugin Manager package for Zen Cart 2.0.x, 2.1.x, and 2.2.x. Use a PHP version supported by your Zen Cart release.

## Install and configure

1. Copy the contents of `files/zc_plugins/` into the store's `zc_plugins/` directory.
2. In Zen Cart admin, open **Modules > Plugin Manager** and install **Share Everywhere for Zen Cart 1.0.3**.
3. Open **Catalog > Share Everywhere**, choose a layout, and enable the destinations you want.
4. Select **Enable on product pages**, save, and open an enabled product page.

The widget is disabled at installation. It uses the standard footer notifier in Bootstrap and Responsive Classic, then moves below the add-to-cart area in the browser. For a customized template, keep the `NOTIFY_FOOTER_END` notifier and a standard add-to-cart or product container. If placement targets are missing, the widget stays where the notifier rendered it.

## Buttons and layouts

- Choose a single button, compact icon row, full row, grid, or vertical stack independently for desktop and mobile.
- Choose click or hover-and-click expansion. On a touch device or keyboard, use the same disclosure button.
- Configure order, compact-row pinning, alignment, icon size, and labels.
- Share buttons build links from the product title, canonical URL, and main image where supported. Pinterest is hidden for products without a main image. Profile and invite buttons require the store's public URL.
- Native Share is available on supported browsers. Copy link, print, and a locally generated product QR code are also available.
- Optional destinations grouped under **Additional services via AddToAny** send the shopper to AddToAny after a click. The product page loads all icons and scripts from the store. Third-party destinations may require a login or app, and SMS support varies by device.

## Custom links

Enter a label, icon, and URL. For a custom profile, use a full HTTP or HTTPS URL. For a custom share button, use a fixed HTTPS destination and place `{url}` or `{text}` in its path, query, or fragment. You can also include `{title}` and `{image}`. The plugin URL-encodes the replacement values.

Example: `https://example.com/share?url={url}&title={title}`

Enter a product ID and choose **Prepare test links** to test the custom destinations before saving. Preview does not save the form.

## Upgrade and uninstall

Future versions use **Modules > Plugin Manager** for upgrades. Settings are stored in one plugin-owned configuration row and preserved by the upgrade handler. Uninstall in Plugin Manager removes that row and the Catalog menu registration. The plugin creates no tables and requires no collation changes.

## Support and license

Maintained by Melanie Prough, [PRO-Webs, Inc.](https://pro-webs.net/). Report reproducible issues through the [PRO-Webs helpdesk](https://prowebsinc.zohodesk.com/portal/en/newticket).

See [LICENSE](LICENSE) for GPL v2. This plugin is distributed without warranty. Installation, configuration, and customization are available separately.

Brand artwork is from [Simple Icons](https://github.com/simple-icons/simple-icons), revision `1bb2f6e`. Product QR codes use [QR Code Generator](https://github.com/kazuhikoarase/qrcode-generator), revision `83b7e8f`. Their licenses are included with the assets. Platform names and marks belong to their respective owners.
