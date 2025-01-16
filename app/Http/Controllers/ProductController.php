<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        // Fetch products with related data
        $products = Product::with([
            'variations.attributes.value.attribute', // Load attributes and their values
        ])->paginate(10);

        return Inertia::render('Product/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        // Fetch attributes with their values and group them by attribute name
        $attributeValues = AttributeValue::with('attribute')
            ->get()
            ->groupBy('attribute.name')
            ->map(function ($values, $attributeName) {
                return [
                    'attribute_name' => $attributeName,
                    'values' => $values->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                        ];
                    }),
                ];
            })
            ->values();

        //return $attributeValues;

        
        return Inertia::render('Product', [
            'attributeValues' => $attributeValues,
        ]);
    }

    public function store(Request $request)
    {


        try {

            // Validate the incoming data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'default_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'stock' => 'nullable|integer|min:0',
                'variations' => 'required|array|min:1', // Ensure at least one variation is provided
                'variations.*.price' => 'required|numeric|min:0',
                'variations.*.stock' => 'nullable|integer|min:0',
                'variations.*.image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'variations.*.attributes' => 'required|array|min:1', // Ensure attributes are provided
                'variations.*.attributes.*.attribute_value_id' => 'required|integer|exists:attribute_values,id',
            ]);

            DB::beginTransaction();

            // Upload the default image if provided
            $defaultImagePath = null;
            if ($request->hasFile('default_image')) {
                $file = $request->file('default_image');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                $file->move('storage/products/', $filename);
                $defaultImagePath = 'storage/products/' . $filename;
            }

            // Create the product
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => 'variable',
                'price' => $validated['price'],
                'default_image' => $defaultImagePath,
                'stock' => $validated['stock'] ?? 0,
            ]);

            // Process variations
            foreach ($validated['variations'] as $variationData) {
                $variationImagePath = null;
                if (isset($variationData['image_path']) && $variationData['image_path']) {
                    $file = $variationData['image_path'];
                    $ext = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $ext;
                    $file->move('storage/variations/', $filename);
                    $variationImagePath = 'storage/variations/' . $filename;
                }

                // Create the product variation
                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'price' => $variationData['price'],
                    'stock' => $variationData['stock'] ?? null,
                    'image_path' => $variationImagePath,
                ]);

                // Add variation attributes
                foreach ($variationData['attributes'] as $attributeData) {
                    VariationAttribute::create([
                        'product_variation_id' => $variation->id,
                        'attribute_value_id' => $attributeData['attribute_value_id'],
                    ]);
                }
            }

            DB::commit();

            // Return an Inertia response with a success message
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id){
        $product = Product::with(['variations.attributes.value.attribute'])->findOrFail($id);
        //return $product;
        return Inertia::render('Product/Show', [
            'product' => $product
        ]);
    }


    public function destroy($id)
    {
        try {
            // Find the product by ID
            $product = Product::findOrFail($id);

            // Delete the product variations and their associated images
            foreach ($product->variations as $variation) {
                // Delete variation image if it exists
                if ($variation->image_path && file_exists(public_path($variation->image_path))) {
                    unlink(public_path($variation->image_path));
                }

                // Delete the variation attributes
                VariationAttribute::where('product_variation_id', $variation->id)->delete();

                // Delete the variation itself
                $variation->delete();
            }

            // Delete product image if it exists
            if ($product->default_image && file_exists(public_path($product->default_image))) {
                unlink(public_path($product->default_image));
            }

            // Delete the product
            $product->delete();

            // Return a success message with a redirect
            return redirect()->route('admin.products.index')->with('success', 'Product and its variations deleted successfully!');
        } catch (\Exception $e) {
            // In case of any error, return with error message
            return redirect()->route('admin.products.index')->with('error', 'An error occurred while deleting the product: ' . $e->getMessage());
        }
    }



}


