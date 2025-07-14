<?php
/**
 * Early alias so legacy imports still work:
 *   ArielMejiaDev\LarapexCharts\LarapexChart
 */
if (!class_exists('ArielMejiaDev\\LarapexCharts\\LarapexChart')) {
    class_alias(
        'App\\Support\\LarapexChartCompat',
        'ArielMejiaDev\\LarapexCharts\\LarapexChart'
    );
}
