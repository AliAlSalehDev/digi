<?php

namespace App\Http\Controllers;

use App\Imports\IngredientImport;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query  = Ingredient::query();
        if ($request->has('search') && $request->filled('search')) {
            $query->where('name', 'LIKE', "{%$request->search%}");
        }
        $ingredients = $query->paginate(10);
        if ($request->ajax()) {
            return response()->json([
                'error' => false,
                'html' => view('admin.pages.ingredients.table', compact('ingredients'))
            ]);
        }
        return view('admin.pages.ingredients.index', compact('ingredients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.ingredients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);
        $file = $request->file('file');
        Excel::import(new IngredientImport, $file);
        // $validatedData = $request->validate([
        //     'name' => 'required',
        //     'unit' => 'required',
        //     'calories' => 'required',
        //     'protein' => 'required',
        //     'carbs' => 'required',
        //     'fats_per_100g' => 'required'
        // ]);
        // Ingredient::create($validatedData);
        return redirect()->back()->with('success', 'Ingredient created successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        return view('admin.pages.ingredients.show', compact('ingredient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        return view('admin.pages.ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'calories' => 'required',
            'protein' => 'required',
            'carbs' => 'required',
            'fats_per_100g' => 'required'
        ]);
        Ingredient::find($ingredient)->update($validatedData);
        return redirect()->back()->with('success', 'Ingredient updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->back()->with('success', 'Ingredient updated successfully');
    }
}
