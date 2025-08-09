<?php

namespace App\Http\Controllers;

use App\Imports\SauceImport;
use App\Models\Sauce;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SauceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query  = Sauce::query();
        if ($request->has('search') && $request->filled('search')) {
            $query->where('name', 'LIKE', "{%$request->search%}");
        }
        $sauces = $query->paginate(10);
        if ($request->ajax()) {
            return response()->json([
                'error' => false,
                'html' => view('admin.pages.sauces.table', compact('sauces'))
            ]);
        }
        return view('admin.pages.sauces.index', compact('sauces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.sauces.create');
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
        Excel::import(new SauceImport, $file);
        // $validatedData = $request->validate([
        //     'name' => 'required',
        //     'unit' => 'required',
        //     'calories' => 'required',
        //     'protein' => 'required',
        //     'carbs' => 'required',
        //     'fats_per_100g' => 'required'
        // ]);
        // Sauce::create($validatedData);
        return redirect()->back()->with('success', 'Sauce created successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Sauce $ingredient)
    {
        return view('admin.pages.sauces.show', compact('ingredient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sauce $ingredient)
    {
        return view('admin.pages.sauces.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sauce $ingredient)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'calories' => 'required',
            'protein' => 'required',
            'carbs' => 'required',
            'fats_per_100g' => 'required'
        ]);
        Sauce::find($ingredient)->update($validatedData);
        return redirect()->back()->with('success', 'Sauce updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sauce $ingredient)
    {
        $ingredient->delete();
        return redirect()->back()->with('success', 'Sauce updated successfully');
    }
}
