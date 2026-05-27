<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    /**
     * Display a listing of rewards.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $rewards = Reward::with('product')->latest()->paginate(20);
        return view('admin.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new reward.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.rewards.create', compact('products'));
    }

    /**
     * Store a newly created reward.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Reward store request data:', $request->all());
        
        // Custom validation based on type
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:product,travel_package',
            'required_points' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];

        // Add conditional validation based on type
        if ($request->type === 'product') {
            $rules['product_id'] = 'required|exists:products,id';
        } elseif ($request->type === 'travel_package') {
            $rules['price'] = 'required|numeric|min:0';
        }

        try {
            $validatedData = $request->validate($rules);
            Log::info('Validation passed, validated data:', $validatedData);
            
            Reward::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'price' => $request->price,
                'product_id' => $request->product_id,
                'required_points' => $request->required_points,
                'is_active' => (bool) $request->is_active, // Explicitly cast to boolean
            ]);
            
            Log::info('Reward created successfully');
            return redirect()->route('admin.rewards.index')->with('success', 'Reward created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creating reward:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create reward: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified reward.
     *
     * @param  \App\Models\Reward  $reward
     * @return \Illuminate\View\View
     */
    public function edit(Reward $reward)
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.rewards.edit', compact('reward', 'products'));
    }

    /**
     * Update the specified reward.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reward  $reward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Reward $reward)
    {
        // Log the incoming request data for debugging
        Log::info('Reward update request data:', $request->all());
        
        // Custom validation based on type
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:product,travel_package',
            'required_points' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];

        // Add conditional validation based on type
        if ($request->type === 'product') {
            $rules['product_id'] = 'required|exists:products,id';
        } elseif ($request->type === 'travel_package') {
            $rules['price'] = 'required|numeric|min:0';
        }

        try {
            $validatedData = $request->validate($rules);
            Log::info('Validation passed, validated data:', $validatedData);
            
            $reward->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'price' => $request->price,
                'product_id' => $request->product_id,
                'required_points' => $request->required_points,
                'is_active' => (bool) $request->is_active, // Explicitly cast to boolean
            ]);
            
            Log::info('Reward updated successfully');
            return redirect()->route('admin.rewards.index')->with('success', 'Reward updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating reward:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update reward: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified reward.
     *
     * @param  \App\Models\Reward  $reward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Reward $reward)
    {
        try {
            $reward->delete();
            return redirect()->route('admin.rewards.index')->with('success', 'Reward deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting reward:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to delete reward: ' . $e->getMessage());
        }
    }
}