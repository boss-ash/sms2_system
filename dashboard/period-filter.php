<?php
/**
 * SMS 2 – Dashboard reporting period filter helpers
 */

function smsDashboardPeriods(): array
{
    return [
        'this_week'    => ['label' => 'This week',    'sy' => 'SY 2025–2026', 'factor' => 0.22],
        'this_month'   => ['label' => 'This month',   'sy' => 'SY 2025–2026', 'factor' => 1.0],
        'last_30_days' => ['label' => 'Last 30 days', 'sy' => 'SY 2025–2026', 'factor' => 0.88],
        'this_term'    => ['label' => 'This term',    'sy' => 'SY 2025–2026', 'factor' => 3.4],
        'ay_2025_2026' => ['label' => 'AY 2025-2026', 'sy' => 'SY 2025–2026', 'factor' => 8.5],
    ];
}

function smsDashboardCurrentPeriod(): string
{
    $periods = smsDashboardPeriods();
    $key = $_GET['period'] ?? 'this_month';

    return array_key_exists($key, $periods) ? $key : 'this_month';
}

function smsDashboardPeriodUrl(string $periodKey): string
{
    $params = $_GET;
    $params['period'] = $periodKey;

    return '?' . http_build_query($params);
}

function smsDashboardScaleMetricValue(string $value, float $factor): string
{
    $value = trim($value);

    if (preg_match('/^(₱|PHP\s*)([\d,]+(?:\.\d+)?)(K|M)?$/i', $value, $matches)) {
        $num = (float) str_replace(',', '', $matches[2]);
        $suffix = strtoupper($matches[3] ?? '');

        if ($suffix === 'M') {
            $num *= 1000000;
        } elseif ($suffix === 'K') {
            $num *= 1000;
        }

        $scaled = $num * $factor;
        $prefix = str_starts_with($value, '₱') ? '₱' : 'PHP ';

        if ($scaled >= 1000000) {
            return $prefix . number_format($scaled / 1000000, 1) . 'M';
        }
        if ($scaled >= 1000) {
            return $prefix . number_format($scaled / 1000, $scaled >= 10000 ? 0 : 1) . 'K';
        }

        return $prefix . number_format($scaled, 0);
    }

    if (preg_match('/^([+\-]?)([\d,]+(?:\.\d+)?)%$/', $value, $matches)) {
        $sign = $matches[1] === '-' ? '-' : '+';
        $num = (float) str_replace(',', '', $matches[2]);
        $scaled = min(99.9, max(0.1, $num * (0.82 + min($factor, 3.4) * 0.06)));

        $decimals = $scaled < 10 ? 1 : 0;

        return $sign . number_format($scaled, $decimals) . '%';
    }

    if (preg_match('/^[\d,]+(?:\.\d+)?$/', $value)) {
        $num = (float) str_replace(',', '', $value);
        $scaled = max(0, round($num * $factor));

        return number_format($scaled);
    }

    if (preg_match('/^\d+\.\d+$/', $value)) {
        $num = (float) $value;

        return number_format(min(5.0, max(1.0, $num * (0.92 + min($factor, 3.4) * 0.02))), 1);
    }

    return $value;
}

function smsDashboardScaleDelta(string $delta, float $factor): string
{
    if (!preg_match('/^([+\-]?)([\d,]+(?:\.\d+)?)(%?)$/', $delta, $matches)) {
        return $delta;
    }

    $sign = $matches[1];
    $num = (float) str_replace(',', '', $matches[2]);
    $suffix = $matches[3];
    $scaled = max(0.1, $num * (0.75 + min($factor, 3.4) * 0.12));

    if ($suffix === '%') {
        return ($sign !== '-' ? '+' : '-') . number_format($scaled, $scaled < 10 ? 1 : 0) . '%';
    }

    $rounded = max(0, (int) round($scaled));

    return ($sign === '-' ? '-' : '+') . (string) $rounded;
}

function smsDashboardScaleStatCards(array $cards, float $factor): array
{
    foreach ($cards as &$card) {
        $card['value'] = smsDashboardScaleMetricValue((string) ($card['value'] ?? ''), $factor);

        if (isset($card['delta'])) {
            $card['delta'] = smsDashboardScaleDelta((string) $card['delta'], $factor);
        }
    }
    unset($card);

    return $cards;
}
