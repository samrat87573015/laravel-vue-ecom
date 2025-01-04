<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;

class AttributeController extends Controller
{
    public function Create(){
        $attrList = Attribute::with('values')->get();
        //return $attrList;
        return Inertia::render('Product/AttributeCreate', [
            'attrList' => $attrList,
        ]);
    }

    public function store(Request $request)
    {

        // Create the attribute
        $attribute = Attribute::create([
            'name' => $request->name,
        ]);

        
        foreach ($request->values as $value) {
            AttributeValue::create([
                'attribute_id' => $attribute->id, 
                'value' => $value,
            ]);
        }

        return redirect()->route('admin.attributes.create')->with('success', 'Attribute and its values created successfully.');
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);

        $attributeValues = AttributeValue::where('attribute_id', $id)->get();

        foreach ($attributeValues as $value) {
            $value->delete();
        }
    
        $attribute->delete();
    
        return redirect()->route('admin.attributes.create')->with('success', 'Attribute and its values deleted successfully.');
    }

}
