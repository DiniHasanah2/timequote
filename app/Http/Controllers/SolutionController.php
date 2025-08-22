<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solution;
use App\Models\Quotation;
use App\Models\User;

class SolutionController extends Controller
{
    public function index()
    {
        $existingSolutions = Solution::with('quotation', 'version', 'customer', 'project')->get();
        $existingQuotationIds = $existingSolutions->pluck('quotation_id')->toArray();

        $quotations = Quotation::with(['version', 'project.customer'])
            ->whereNotIn('id', $existingQuotationIds)
            ->get();

        $autoSolutions = $quotations->map(function ($q) {
            return (object)[
                'version_name' => optional($q->version)->version_name ?? '-',
                'customer_name' => optional($q->project->customer)->name ?? '-',
                'project_name' => optional($q->project)->name ?? '-',
                'status' => $q->status ?? '-',
                'quotation_id' => $q->id,
                'version_id' => $q->version_id,
                'is_auto' => true
            ];
        });

        $allSolutions = $existingSolutions->map(function ($s) {
            return (object)[
                'version_name' => $s->version_name ?? optional($s->version)->version_name ?? '-',
                'customer_name' => $s->customer_name ?? optional($s->customer)->name ?? '-',
                'project_name' => $s->project_name ?? optional($s->project)->name ?? '-',
                'status' => $s->status,
                'quotation_id' => $s->quotation_id,
                'version_id' => $s->version_id,
                'is_auto' => false
            ];
        })->concat($autoSolutions);

        $availableQuotations = $quotations;
        $presales = User::where('role', 'presale')->get();

        return view('solutions.index', [
            'solutions' => $allSolutions,
            'availableQuotations' => $availableQuotations,
            'presales' => $presales
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|uuid|exists:quotations,id',
            'status' => 'required|string',
            'presale_id' => 'required|uuid|exists:users,id'
        ]);

        $quotation = Quotation::with(['version', 'project.customer'])->findOrFail($request->quotation_id);

        Solution::create([
            'quotation_id' => $quotation->id,
            'version_id' => $quotation->version_id,
            'project_id' => $quotation->project_id,
            'customer_id' => $quotation->project->customer_id,
            'presale_id' => $request->presale_id,
            'version_name' => $quotation->version->version_name,
            'project_name' => $quotation->project->name,
            'customer_name' => $quotation->project->customer->name,
            'status' => $request->status
        ]);

        return redirect()->route('solutions.index')->with('success', 'Solution created successfully.');
    }
}
