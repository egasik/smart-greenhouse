<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function index()
    {
        $crops = Crop::orderBy('name')->get();

        return view('crops.index', compact('crops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'variety' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        Crop::create($data);

        return redirect()
            ->route('crops.index')
            ->with('success', 'Культура добавлена.');
    }
}
