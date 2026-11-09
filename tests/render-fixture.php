<?php
declare(strict_types=1);

define('IS_ADMIN_FLAG', false);
$root = dirname(__DIR__) . '/files/zc_plugins/ShareEverywhere/v1.0.3';
require $root . '/lib/ShareEverywhere.php';

$settings = ShareEverywhere::defaults();
$settings['trigger'] = 'hover';
$settings['buttons']['qr']['enabled'] = true;
$settings['buttons']['follow-instagram']['enabled'] = true;
$settings['buttons']['follow-instagram']['url'] = 'https://www.instagram.com/example/';
$product = ['url' => 'https://shop.example/duck', 'title' => 'Example duck', 'image' => 'https://shop.example/duck.png'];
$buttons = ShareEverywhere::buttons($settings, $product);
$assets = ShareEverywhere::assets('/');
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Product fixture</title></head>
<body>
<main id="productGeneral"><h1>Example duck</h1><div id="cartAdd"><button>Add to Cart</button></div></main>
<footer>Store footer</footer>
<?php require $root . '/lib/storefront.php'; ?>
</body>
</html>
