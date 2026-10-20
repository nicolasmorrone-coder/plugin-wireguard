<?php
require __DIR__ . '/fixtures/coreStub.php';
$root = sys_get_temp_dir() . '/wireguard-config-' . bin2hex(random_bytes(8));
mkdir($root . '/plugins/wireguard/core/class', 0700, true);
mkdir($root . '/core/php', 0700, true);
file_put_contents($root . '/core/php/core.inc.php', '<?php');
copy(dirname(__DIR__) . '/core/class/wireguard.class.php', $root . '/plugins/wireguard/core/class/wireguard.class.php');
set_error_handler(function ($level, $message, $file, $line) { throw new ErrorException($message, 0, $level, $file, $line); });
try {
    require $root . '/plugins/wireguard/core/class/wireguard.class.php';
    $eq = new wireguard();
    $eq->setConfiguration('Address', '198.18.0.1/32');
    $method = new ReflectionMethod('wireguard', 'writeConfig');
    $method->setAccessible(true);
    $method->invoke($eq);
    $path = $root . '/plugins/wireguard/data/wg_7.conf';
    if (strpos(file_get_contents($path), 'Address = 198.18.0.1/32') === false) throw new RuntimeException('First configuration missing');
    $eq->setConfiguration('Address', '198.18.0.2/32');
    $method->invoke($eq);
    $contents = file_get_contents($path);
    if (strpos($contents, 'Address = 198.18.0.2/32') === false || strpos($contents, 'Address = 198.18.0.1/32') !== false) throw new RuntimeException('Configuration was not replaced');
    echo "PASS: first creation and replacement without warnings\n";
} finally {
    restore_error_handler();
    foreach (['/plugins/wireguard/data/wg_7.conf', '/plugins/wireguard/core/class/wireguard.class.php', '/core/php/core.inc.php'] as $path) if (file_exists($root . $path)) unlink($root . $path);
    foreach (['/plugins/wireguard/data', '/plugins/wireguard/core/class', '/plugins/wireguard/core', '/plugins/wireguard', '/plugins', '/core/php', '/core', ''] as $path) if (is_dir($root . $path)) rmdir($root . $path);
}
