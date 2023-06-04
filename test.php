<?php

$content = file_get_contents(__DIR__ . D . 'test.txt');

echo '<style>:target{background:#ff0}a[aria-hidden]::before{content:\'\00A7\'}</style>';
echo x\anchor\page__content($content);

exit;