<?php
namespace App\Http\Controllers;

use App\Models\Risk;
use App\Exports\RiskExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Services\SophosApiService;
use Illuminate\Support\Collection;

class RiskController extends Controller
{
    public function dashboard()
    {
        $risks = [
            'low' => Risk::with('details')->where('category', 'low')->first(),
            'medium' => Risk::with('details')->where('category', 'medium')->first(),
        ];
        return view('dashboard_risk', compact('risks'));
    }

    public function export(Request $request, $category, $format = 'xlsx')
    {
        // Fetch the risk data for the given category
        // Using SophosApiService seems more appropriate given your dashboard uses it
        $sophosApi = app(SophosApiService::class);
        $alerts = $sophosApi->getAlertsByCategory($category);

        // Ensure alerts is an array
        $alerts = is_array($alerts) ? $alerts : [];

        // Generate solution recommendations based on the $alerts data
        $solution = $this->generateSolution($alerts);

        $filename = ucfirst($category).'_Risk_Report.' . ($format === 'pdf' ? 'pdf' : 'xlsx');

        // Pass the alerts data AND the solution to your Export class
        // You will need to adjust your RiskExport class to handle both data and solution
        return Excel::download(new \App\Exports\RiskExport($alerts, $solution), $filename, ($format === 'pdf' ? \Maatwebsite\Excel\Excel::DOMPDF : \Maatwebsite\Excel\Excel::XLSX));
    }

    // Add a method to generate solution recommendations (example)
    private function generateSolution($alerts)
    {
        if (empty($alerts)) {
            return "No specific recommendations as there are no alerts in this category.";
        }

        $recommendations = [];
        $highRiskCount = collect($alerts)->where('severity', 'high')->count();
        $mediumRiskCount = collect($alerts)->where('severity', 'medium')->count();
        $lowRiskCount = collect($alerts)->where('severity', 'low')->count();

        $recommendations[] = "Summary of alerts:";
        if ($highRiskCount > 0) {
            $recommendations[] = "- {$highRiskCount} High Risk alerts detected.";
        }
        if ($mediumRiskCount > 0) {
            $recommendations[] = "- {$mediumRiskCount} Medium Risk alerts detected.";
        }
        if ($lowRiskCount > 0) {
            $recommendations[] = "- {$lowRiskCount} Low Risk alerts detected.";
        }

        $recommendations[] = "\nGeneral Recommendations:";
        $recommendations[] = "- Review the details of High and Medium risk alerts immediately.";
        $recommendations[] = "- Ensure all endpoints reporting alerts are up-to-date with the latest security patches and software.";
        $recommendations[] = "- Investigate the source and location of frequent alerts to identify potential patterns or compromised systems.";
        $recommendations[] = "- Regularly review low risk alerts for any escalating trends.";
        $recommendations[] = "- Consider implementing stricter security policies if recurring issues are observed.";

        // You can expand this logic significantly based on alert categories, descriptions, etc.
        // For a more advanced solution, you might use AI/ML models or predefined rules based on specific alert types.

        return implode("\n", $recommendations);
    }
} 