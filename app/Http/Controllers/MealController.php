<?php

namespace App\Http\Controllers;

use App\Imports\MealImport;
use App\Models\Meal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query  = Meal::query();
        if ($request->has('search') && $request->filled('search')) {
            $query->where('name', 'LIKE', "{%$request->search%}");
        }
        $meals = $query->paginate(10);
        if ($request->ajax()) {
            return response()->json([
                'error' => false,
                'html' => view('admin.pages.meals.table', compact('meals'))
            ]);
        }
        return view('admin.pages.meals.index', compact('meals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.meals.create');
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
        try {
            Excel::import(new MealImport, $file);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        // $validatedData = $request->validate([
        //     'name' => 'required',
        //     'unit' => 'required',
        //     'calories' => 'required',
        //     'protein' => 'required',
        //     'carbs' => 'required',
        //     'fats_per_100g' => 'required'
        // ]);
        // Meal::create($validatedData);
        return redirect()->back()->with('success', 'Meal created successfully');
    }
}
