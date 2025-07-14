<?php

namespace App\Support;

use marineusde\LarapexCharts\LarapexChart as Base;
use ReflectionNamedType;
use ReflectionProperty;

class LarapexChartCompat extends Base
{
    private const TYPE_MAP = [
        'barchart'           => 'bar',
        'linechart'          => 'line',
        'areachart'          => 'area',
        'horizontalbarchart' => 'bar',
        'heatmapchart'       => 'heatmap',
        'piechart'           => 'pie',
        'donutchart'         => 'donut',
        'radialbarchart'     => 'radialbar',
    ];

    /* ---------------- chart-type helpers ---------------- */
    private function applyType(string $chartType): static
    {
        return method_exists($this, 'setChartType')
            ? $this->setChartType($chartType)
            : (method_exists($this, 'setType')
                ? $this->setType($chartType)
                : tap($this, fn () => $this->type = $chartType));
    }

    /* ---------------- legacy addData() ------------------ */
    public function addData(string $seriesName, array $data): static
    {
        if (method_exists($this, 'addSeries')) {
            return $this->addSeries($seriesName, $data);
        }
        $this->series[] = ['name' => $seriesName, 'data' => $data];
        return $this;
    }

    /* ---------------- setLabels → proxy ----------------- */
    public function setLabels(array $labels): static
    {
        return $this->setXAxis($labels);
    }

    /* ---------------- legacy setXAxis() ----------------- */
    public function setXAxis(array $labels): static
    {
        $this->labels = $labels;

        $xAxis = ['categories' => $labels];

        $prop = new ReflectionProperty($this, 'xAxis');
        $typed = $prop->getType();
        $isString = $typed instanceof ReflectionNamedType
            && $typed->getName() === 'string';

        $this->xAxis = $isString ? json_encode($xAxis) : $xAxis;

        return $this;
    }

    /* -------- magic: static helpers & passthrough ------- */
    public static function __callStatic(string $name, array $args): static
    {
        $key = strtolower($name);
        if (isset(self::TYPE_MAP[$key])) {
            return (new static())->applyType(self::TYPE_MAP[$key]);
        }
        if (method_exists(Base::class, $name)) {
            /** @phpstan-ignore-next-line */
            return forward_static_call([Base::class, $name], ...$args);
        }
        throw new \BadMethodCallException("Method {$name} does not exist.");
    }

    /* -------- magic: instance helpers & passthrough ----- */
    public function __call(string $name, array $args): mixed
    {
        $key = strtolower($name);
        if (isset(self::TYPE_MAP[$key])) {
            return $this->applyType(self::TYPE_MAP[$key]);
        }
        if (method_exists($this, $name)) {
            /** @phpstan-ignore-next-line */
            return $this->$name(...$args);
        }
        throw new \BadMethodCallException("Method {$name} does not exist.");
    }
}
