<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrinterController extends Controller
{
    public function index(): View
    {
        $printers = Printer::orderBy('id')->get();
        $allJobs  = Printer::$allJobs;

        $assignedJobs = $printers->where('is_active', true)
            ->flatMap(fn ($p) => $p->handles ?? [])
            ->unique()
            ->values()
            ->all();

        return view('printers.index', compact('printers', 'allJobs', 'assignedJobs'));
    }

    public function create(): View
    {
        return view('printers.create', ['allJobs' => Printer::$allJobs]);
    }

    public function edit(Printer $printer): View
    {
        return view('printers.edit', [
            'printer' => $printer,
            'allJobs' => Printer::$allJobs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'printer_name' => 'required|string|max:255',
            'is_active'    => 'boolean',
            'handles'      => 'nullable|array',
            'handles.*'    => 'string|in:' . implode(',', array_keys(Printer::$allJobs)),
            'notes'        => 'nullable|string|max:255',
        ]);

        $data['handles']   = $data['handles'] ?? [];
        $data['is_active'] = $request->boolean('is_active', true);

        $this->releaseJobs($data['handles']);

        Printer::create($data);

        return redirect()->route('printers.index')->with('success', __('ui.printers.created'));
    }

    public function update(Request $request, Printer $printer): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'printer_name' => 'required|string|max:255',
            'is_active'    => 'boolean',
            'handles'      => 'nullable|array',
            'handles.*'    => 'string|in:' . implode(',', array_keys(Printer::$allJobs)),
            'notes'        => 'nullable|string|max:255',
        ]);

        $data['handles']   = $data['handles'] ?? [];
        $data['is_active'] = $request->boolean('is_active');

        $this->releaseJobs($data['handles'], excludeId: $printer->id);

        $printer->update($data);

        return redirect()->route('printers.index')->with('success', __('ui.printers.updated'));
    }

    public function destroy(Printer $printer): RedirectResponse
    {
        $printer->delete();

        return redirect()->route('printers.index')->with('success', __('ui.printers.deleted'));
    }

    /** Remove the given job types from all other printers (enforce single-owner rule). */
    private function releaseJobs(array $jobs, ?int $excludeId = null): void
    {
        if (empty($jobs)) {
            return;
        }

        $query = Printer::query();
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        foreach ($query->get() as $p) {
            $current = $p->handles ?? [];
            $updated = array_values(array_diff($current, $jobs));
            if ($updated !== $current) {
                $p->update(['handles' => $updated]);
            }
        }
    }
}
