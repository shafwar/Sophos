<?php

namespace App\Exports;

use App\Models\Risk;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RiskExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    private $alerts;

    public function __construct(array $alerts, string $solution = '')
    {
        $this->alerts = $alerts;
    }

    public function collection()
    {
        return collect($this->alerts);
    }

    public function headings(): array
    {
        return [
            'Alert ID',
            'Category',
            'Severity Level',
            'Date & Time',
            'Source System',
            'Location',
            'Endpoint Type',
            'Endpoint ID',
            'Problem Description',
            'Recommended Solution',
            'Priority Level',
            'Status'
        ];
    }

    public function map($alert): array
    {
        // Clean up problem description
        $problem = $this->cleanProblemDescription($alert['description'] ?? 'No description available');

        // Generate intelligent solution based on alert details
        $solution = $this->generateIntelligentSolution($alert);

        // Determine priority based on severity and category
        $priority = $this->determinePriority($alert);

        // Format date properly
        $formattedDate = $this->formatDate($alert);

        return [
            $alert['id'] ?? 'N/A',
            $this->formatCategory($alert['category'] ?? 'Unknown'),
            $this->formatSeverity($alert['severity'] ?? 'unknown'),
            $formattedDate,
            $alert['source'] ?? 'Unknown Source',
            $alert['location'] ?? 'Not Specified',
            $alert['endpoint_type'] ?? 'Unknown',
            $alert['endpoint_id'] ?? 'N/A',
            $problem,
            $solution,
            $priority,
            'Open'
        ];
    }

    private function cleanProblemDescription($description)
    {
        // Remove technical jargon and make it more readable
        $cleaned = str_replace([
            'clearThreat',
            'pua',
            'Manual PUA cleanup required:',
            '"',
            "'"
        ], [
            'Security Threat',
            'Potentially Unwanted Application',
            'Action needed:',
            '',
            ''
        ], $description);

        // Limit length and add proper formatting
        $cleaned = trim($cleaned);
        if (strlen($cleaned) > 200) {
            $cleaned = substr($cleaned, 0, 197) . '...';
        }

        return $cleaned ?: 'Issue requires investigation';
    }

    private function generateIntelligentSolution($alert)
    {
        $category = strtolower($alert['category'] ?? '');
        $severity = strtolower($alert['severity'] ?? '');
        $description = strtolower($alert['description'] ?? '');

        // Malware/Security related solutions
        if (strpos($description, 'pua') !== false || strpos($description, 'threat') !== false || strpos($category, 'malware') !== false) {
            switch ($severity) {
                case 'high':
                    return "URGENT: Immediately isolate affected system, run full antivirus scan, and review security logs. Consider reimaging if infection persists.";
                case 'medium':
                    return "Run comprehensive malware scan, update antivirus definitions, and monitor system behavior for 24-48 hours.";
                case 'low':
                    return "Schedule routine malware scan, ensure antivirus is updated, and educate user on safe browsing practices.";
            }
        }

        // Network/Connection issues
        if (strpos($description, 'network') !== false || strpos($description, 'connection') !== false || strpos($category, 'network') !== false) {
            return "Check network connectivity, verify firewall settings, and test connection stability. Contact network admin if issues persist.";
        }

        // Performance issues
        if (strpos($description, 'performance') !== false || strpos($description, 'slow') !== false) {
            return "Monitor system resources, check for background processes, consider hardware upgrade if consistently slow.";
        }

        // Backup related issues
        if (strpos($description, 'backup') !== false || strpos($category, 'backup') !== false) {
            switch ($severity) {
                case 'high':
                    return "CRITICAL: Verify backup integrity immediately, restore from last known good backup if needed, and investigate backup failure cause.";
                default:
                    return "Check backup logs, verify backup schedule, and test restore procedure to ensure data integrity.";
            }
        }

        // Generic solutions based on severity
        switch ($severity) {
            case 'high':
                return "HIGH PRIORITY: Investigate immediately, document findings, and implement corrective action within 2 hours.";
            case 'medium':
                return "MEDIUM PRIORITY: Review within 24 hours, analyze impact, and schedule appropriate remediation.";
            case 'low':
                return "LOW PRIORITY: Monitor for patterns, document for trend analysis, and address during routine maintenance.";
            default:
                return "Review alert details, assess impact on operations, and take appropriate action based on organizational policies.";
        }
    }

    private function formatCategory($category)
    {
        return ucwords(str_replace(['_', '-'], ' ', $category));
    }

    private function formatSeverity($severity)
    {
        $severityMap = [
            'high' => 'HIGH',
            'medium' => 'MEDIUM',
            'low' => 'LOW',
            'critical' => 'CRITICAL'
        ];

        return $severityMap[strtolower($severity)] ?? strtoupper($severity);
    }

    private function determinePriority($alert)
    {
        $severity = strtolower($alert['severity'] ?? '');
        $category = strtolower($alert['category'] ?? '');

        if ($severity === 'high' || $severity === 'critical') {
            return 'P1 - Critical';
        } elseif ($severity === 'medium') {
            return 'P2 - High';
        } elseif ($severity === 'low') {
            return 'P3 - Medium';
        }

        return 'P4 - Low';
    }

    private function formatDate($alert)
    {
        $date = $alert['raisedAt'] ?? $alert['date'] ?? null;

        if ($date) {
            try {
                return \Carbon\Carbon::parse($date)->format('M d, Y H:i');
            } catch (\Exception $e) {
                return $date;
            }
        }

        return 'Not Available';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Set column widths for better readability
                $columnWidths = [
                    'A' => 12, // Alert ID
                    'B' => 20, // Category
                    'C' => 15, // Severity
                    'D' => 18, // Date
                    'E' => 20, // Source
                    'F' => 25, // Location
                    'G' => 15, // Endpoint Type
                    'H' => 15, // Endpoint ID
                    'I' => 60, // Problem Description
                    'J' => 80, // Solution
                    'K' => 15, // Priority
                    'L' => 12  // Status
                ];

                foreach ($columnWidths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                // Enable text wrapping for description and solution columns
                $sheet->getStyle('I:J')->getAlignment()->setWrapText(true);
                $sheet->getStyle('I:J')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Add borders to all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Color code severity levels
                for ($row = 2; $row <= $highestRow; $row++) {
                    $severity = $sheet->getCell("C{$row}")->getValue();

                    switch (strtoupper($severity)) {
                        case 'CRITICAL':
                        case 'HIGH':
                            $sheet->getStyle("C{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('E74C3C');
                            $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                            break;
                        case 'MEDIUM':
                            $sheet->getStyle("C{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F39C12');
                            $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                            break;
                        case 'LOW':
                            $sheet->getStyle("C{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('27AE60');
                            $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                            break;
                    }
                }

                // Color code priority levels
                for ($row = 2; $row <= $highestRow; $row++) {
                    $priority = $sheet->getCell("K{$row}")->getValue();

                    if (strpos($priority, 'P1') !== false) {
                        $sheet->getStyle("K{$row}")->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('C0392B');
                        $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                    } elseif (strpos($priority, 'P2') !== false) {
                        $sheet->getStyle("K{$row}")->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('E67E22');
                        $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                    }
                }

                // Freeze the header row
                $sheet->freezePane('A2');

                // Set row height for better readability
                for ($row = 1; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Auto-adjust row height for wrapped text
                for ($row = 2; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            },
        ];
    }
}
