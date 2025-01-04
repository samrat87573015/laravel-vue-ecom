<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'massage' => 'required',
        ]);

        // Create the contact
        $contact = Contact::create($validatedData);

        // Redirect to homepage with the success message and updated contacts list
        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Contact created successfully!',
        ]);
    }

    function Home(){
        $products = Product::paginate(10);
        //return $products;
        return Inertia::render('Home', ['products' => $products]);
    }

}
