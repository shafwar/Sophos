<?php
namespace App\Exports;

use App\Models\Risk;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
// We are not using WithStrictNullCells, WithCustomStartCell, or WithEvents for per-row export
// use Maatwebsite\Excel\Concerns\WithStrictNullCells;
// use Maatwebsite\Excel\Concerns\WithCustomStartCell;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use \PhpOffice\PhpSpreadsheet\Style\Fill;
// use \PhpOffice\PhpSpreadsheet\Style\Alignment;

class RiskExport implements FromCollection, WithHeadings, WithMapping // Implemented interfaces
{
    private $alerts;
    // Removed private $solution;

    public function __construct(array $alerts, string $solution = '') // Kept solution parameter for compatibility, but it won't be used in rows
    {
        $this->alerts = $alerts;
        // Removed $this->solution = $solution;
    }

    public function collection()
    {
        // Return the collection of alerts
        return collect($this->alerts);
    }

    public function headings(): array
    {
        // Define column headings, including Problem and Solution
        return [
            'ID',
            'Category',
            'Severity',
            'Date',
            'Source',
            'Location',
            'Endpoint Type',
            'Endpoint ID',
            'Problem',   // Added
            'Solution' // Added
        ];
    }

    public function map($alert): array
    {
        // Map alert data to table rows, including Problem and Solution
        $problem = $alert['description'] ?? '-'; // Using description as the problem

        // Placeholder for individual solution - replace with actual logic
        $individualSolution = 'Review alert details and take action.';
        if (($alert['severity'] ?? '') === 'high') {
            $individualSolution = 'Immediately investigate and mitigate this high-severity issue.';
        } elseif (($alert['severity'] ?? '') === 'medium') {
            $individualSolution = 'Investigate this medium-severity issue and plan mitigation.';
        } elseif (($alert['severity'] ?? '') === 'low') {
            $individualSolution = 'Monitor this low-severity issue for trends.';
        }
        // Add more complex logic here based on alert details if needed

        return [
            $alert['id'] ?? '-',
            $alert['category'] ?? '-',
            $alert['severity'] ?? '-',
            isset($alert['raisedAt']) ? \Carbon\Carbon::parse($alert['raisedAt'])->format('Y-m-d H:i:s') : (isset($alert['date']) ? \Carbon\Carbon::parse($alert['date'])->format('Y-m-d H:i:s') : '-'),
            $alert['source'] ?? '-',
            $alert['location'] ?? '-',
            $alert['endpoint_type'] ?? '-',
            $alert['endpoint_id'] ?? '-',
            $problem,
            $individualSolution,
        ];
    }
} 