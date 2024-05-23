<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



 class BaseController extends Controller
{
    protected $model;

    public function index()
    {
        return response()->json($this->model::all(), 200);
    }

    public function show($id)
    {
        $item = $this->model::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json($item, 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->model::$rules);
        $item = $this->model::create($request->all());

        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $this->validate($request, $this->model::$rules);
        $item->update($request->all());

        return response()->json($item, 200);
    }

    public function destroy($id)
    {
        $item = $this->model::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted'], 200);
    }
}
