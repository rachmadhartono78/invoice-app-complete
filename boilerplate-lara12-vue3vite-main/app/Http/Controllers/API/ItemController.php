<?php
namespace App\Http\Controllers\API;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemController {
    /**
     * GET /api/items
     * List all active items with optional filtering
     */
    public function index(Request $request) {
        $query = Item::active()->with('category');

        // Search by name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('name')
                       ->paginate($request->per_page ?? 50);

        return response()->json($items);
    }

    /**
     * GET /api/items/:id
     * Get single item detail
     */
    public function show(Item $item) {
        return response()->json([
            'data' => $item->load('category')
        ]);
    }

    /**
     * GET /api/item-categories
     * List all categories
     */
    public function categories(Request $request) {
        $categories = ItemCategory::withCount('items')
                                  ->orderBy('name')
                                  ->paginate($request->per_page ?? 50);
        return response()->json($categories);
    }

    /**
     * POST /api/items
     * Create new item (admin only)
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:item_categories,id',
            'name' => 'required|string|unique:items',
            'code' => 'nullable|string|unique:items',
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'area' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $item = Item::create($validated);

        return response()->json([
            'message' => '✅ Item created successfully',
            'data' => $item->load('category')
        ], 201);
    }

    /**
     * PUT /api/items/:id
     * Update item (admin only)
     */
    public function update(Request $request, Item $item) {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:item_categories,id',
            'name' => 'required|string|unique:items,name,' . $item->id,
            'code' => 'nullable|string|unique:items,code,' . $item->id,
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'area' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => '✅ Item updated successfully',
            'data' => $item->load('category')
        ]);
    }

    /**
     * DELETE /api/items/:id
     * Soft delete item (admin only)
     */
    public function destroy(Item $item) {
        // Instead of hard delete, just mark as inactive
        $item->update(['is_active' => false]);

        return response()->json([
            'message' => '✅ Item deactivated successfully'
        ]);
    }
}
