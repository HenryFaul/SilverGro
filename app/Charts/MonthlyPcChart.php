<?php

namespace App\Charts;

use App\Models\ContractSummary;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;

class MonthlyPcChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): array
    {
        $today_date = Carbon::now();
        $month = ($today_date)->monthName;
        $filters['date'] = $today_date->toDateString();
        $contract_summary = ContractSummary::where('contract_type_id',2)->month($filters)->get();

        return $this->chart->barChart()
            ->setTitle('PC Contracts for the month')
                ->setSubtitle('Actual vs Planned units outgoing')
                ->addData('Planned out', $contract_summary->pluck('planned_tons_out')->toArray())
                ->addData('Actual out', $contract_summary->pluck('actual_tons_out')->toArray())
                ->addData('Variance', $contract_summary->pluck('variance_out')->toArray())
                ->setXAxis($contract_summary->pluck('contract_number')->toArray())
            ->toVue();
    }
}
