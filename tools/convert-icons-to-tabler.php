<?php
/**
 * One-off: convert simple Font Awesome <i> tags to smsIcon() in PHP files.
 * Usage: php tools/convert-icons-to-tabler.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$skipDirs = ['vendor', 'node_modules', '.git'];
$skipPathFragments = [
    DIRECTORY_SEPARATOR . 'sms2_system' . DIRECTORY_SEPARATOR . 'sms2_system' . DIRECTORY_SEPARATOR,
];
$extensions = ['php'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$patternSimple = '/<i class="(?:fas|far|fab) fa-([a-z0-9-]+)([^"]*)"([^>]*)><\/i>/i';
$patternSolid = '/<i class="fa-solid fa-([a-z0-9-]+)([^"]*)"([^>]*)><\/i>/i';
$patternDynamic = '/<i class="fas \<\?= ([^?]+)\?\>([^"]*)"([^>]*)><\/i>/i';

$changedFiles = 0;
$changedIcons = 0;

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    foreach ($skipDirs as $skip) {
        if (str_contains($path, DIRECTORY_SEPARATOR . $skip . DIRECTORY_SEPARATOR)) {
            continue 2;
        }
    }
    foreach ($skipPathFragments as $fragment) {
        if (str_contains($path, $fragment)) {
            continue 2;
        }
    }
    if (!in_array(strtolower($file->getExtension()), $extensions, true)) {
        continue;
    }
    if (str_contains($path, DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false || !str_contains($content, 'fa-')) {
        continue;
    }

    $original = $content;
    $localCount = 0;

    $replacer = static function (array $matches) use (&$localCount): string {
        $icon = $matches[1];
        $extraClass = trim($matches[2] ?? '');
        $attrs = trim($matches[3] ?? '');
        $options = [];
        if ($extraClass !== '') {
            $options['class'] = $extraClass;
        }
        if ($attrs !== '') {
            if (preg_match('/aria-hidden="([^"]*)"/', $attrs, $m)) {
                $options['aria-hidden'] = $m[1] !== 'false' ? 'true' : 'false';
            }
            if (preg_match('/class="([^"]*)"/', $attrs, $m)) {
                $options['class'] = trim(($options['class'] ?? '') . ' ' . $m[1]);
            }
            if (preg_match('/style="([^"]*)"/', $attrs, $m)) {
                $options['style'] = $m[1];
            }
        }
        if (isset($options['class'])) {
            $options['class'] = trim(preg_replace('/\s+/', ' ', $options['class']) ?? '');
            if ($options['class'] === '') {
                unset($options['class']);
            }
        }
        $localCount++;
        $optsParts = [];
        foreach ($options as $k => $v) {
            $optsParts[] = var_export($k, true) . ' => ' . var_export($v, true);
        }
        $optsStr = $optsParts ? ', [' . implode(', ', $optsParts) . ']' : '';
        return '<?= smsIcon(' . var_export($icon, true) . $optsStr . ') ?>';
    };

    $content = preg_replace_callback($patternSimple, $replacer, $content) ?? $content;
    $content = preg_replace_callback($patternSolid, $replacer, $content) ?? $content;

    $dynamicReplacer = static function (array $matches) use (&$localCount): string {
        $expr = trim($matches[1]);
        $extraClass = trim($matches[2] ?? '');
        $attrs = trim($matches[3] ?? '');
        $options = [];
        if ($extraClass !== '') {
            $options['class'] = $extraClass;
        }
        if ($attrs !== '') {
            if (preg_match('/aria-hidden="([^"]*)"/', $attrs, $m)) {
                $options['aria-hidden'] = $m[1] !== 'false' ? 'true' : 'false';
            }
            if (preg_match('/class="([^"]*)"/', $attrs, $m)) {
                $options['class'] = trim(($options['class'] ?? '') . ' ' . $m[1]);
            }
            if (preg_match('/style="([^"]*)"/', $attrs, $m)) {
                $options['style'] = $m[1];
            }
        }
        if (isset($options['class'])) {
            $options['class'] = trim(preg_replace('/\s+/', ' ', $options['class']) ?? '');
            if ($options['class'] === '') {
                unset($options['class']);
            }
        }
        $localCount++;
        $optsParts = [];
        foreach ($options as $k => $v) {
            $optsParts[] = var_export($k, true) . ' => ' . var_export($v, true);
        }
        $optsStr = $optsParts ? ', [' . implode(', ', $optsParts) . ']' : '';
        return '<?= smsIcon(' . $expr . $optsStr . ') ?>';
    };
    $content = preg_replace_callback($patternDynamic, $dynamicReplacer, $content) ?? $content;

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changedFiles++;
        $changedIcons += $localCount;
        echo basename(dirname($path)) . '/' . basename($path) . " ($localCount icons)\n";
    }
}

echo "\nDone: $changedFiles files, ~$changedIcons icons converted.\n";
