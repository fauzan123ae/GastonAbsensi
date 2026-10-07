<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index(Request $request)
    {
        $reportData = $this->reportService->getReportData($request->all());

        return view('admin.reports.index', $reportData);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->reportService->exportCsv($request->all());
    }
}
