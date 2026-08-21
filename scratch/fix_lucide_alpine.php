<?php
$content = file_get_contents("resources/views/opd/verifikasi/show.blade.php");

$target = '<i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>';
$replacement = '<span class="transition-transform duration-300 inline-flex" :class="open ? \'rotate-180\' : \'\'"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>';

$content = str_replace($target, $replacement, $content);

file_put_contents("resources/views/opd/verifikasi/show.blade.php", $content);
echo "Fixed";
?>