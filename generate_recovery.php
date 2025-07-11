<?php
// Jalankan: php generate_recovery.php
// Output: 10 recovery code unik, siap copy ke config/recovery.php

$used = [];
for ($i = 1; $i <= 10; $i++) {
    do {
        $code = strtoupper(bin2hex(random_bytes(2))) . '-' .
                strtoupper(bin2hex(random_bytes(2))) . '-' .
                strtoupper(bin2hex(random_bytes(2)));
    } while (in_array($code, $used));
    $used[] = $code;
    echo "'$code',\n";
}
