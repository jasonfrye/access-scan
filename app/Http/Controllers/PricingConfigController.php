<?php

namespace App\Http\Controllers;

use App\Models\PricingConfig;
use App\Services\PricingConfigService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PricingConfigController extends Controller
{
    public function __construct(
        protected PricingConfigService $service
    ) {}

    /**
     * Display all pricing configurations.
     */
    public function index()
    {
        $configs = $this->service->getAll();

        return view('pricing-configs.index', compact('configs'));
    }

    /**
     * Show the form for creating a new pricing configuration.
     */
    public function create()
    {
        return view('pricing-configs.create');
    }

    /**
     * Store a new pricing configuration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $config = $this->service->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => false,
        ]);

        return redirect()->route('pricing-configs.index')
            ->with('success', 'Pricing configuration created successfully!');
    }

    /**
     * Show the form for editing a pricing configuration.
     */
    public function edit(PricingConfig $config)
    {
        $plans = $config->getPlans();
        $plansJson = json_encode($plans, JSON_PRETTY_PRINT);

        return view('pricing-configs.edit', compact('config', 'plansJson'));
    }

    /**
     * Update a pricing configuration.
     */
    public function update(Request $request, PricingConfig $config)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->service->update($config, [
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('pricing-configs.index')
            ->with('success', 'Pricing configuration updated successfully!');
    }

    /**
     * Delete a pricing configuration.
     */
    public function destroy(PricingConfig $config)
    {
        if (!$config->delete()) {
            return redirect()->route('pricing-configs.index')
                ->with('error', 'Cannot delete the only pricing configuration.');
        }

        return redirect()->route('pricing-configs.index')
            ->with('success', 'Pricing configuration deleted successfully!');
    }

    /**
     * Activate a pricing configuration.
     */
    public function activate(PricingConfig $config)
    {
        $this->service->activate($config);

        return redirect()->route('pricing-configs.index')
            ->with('success', 'Pricing configuration activated successfully!');
    }

    /**
     * Preview a pricing configuration.
     */
    public function preview(PricingConfig $config)
    {
        $this->service->assignToVisitor($config);

        return redirect()->route('billing.pricing');
    }
}
