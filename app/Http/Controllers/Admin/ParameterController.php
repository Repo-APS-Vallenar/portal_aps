<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\TicketCategory;
use App\Models\TicketStatus;

class ParameterController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->get();
        $categories = TicketCategory::orderBy('name')->get();
        $statuses = TicketStatus::orderBy('name')->get();
        return view('admin.parameters', compact('locations', 'categories', 'statuses'));
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
        ]);
        \App\Models\Location::create(['name' => $request->name]);
        return redirect()->route('admin.parameters', ['tab' => 'locations'])->with('success', 'Ubicación agregada correctamente.');
    }

    public function updateLocation(Request $request, \App\Models\Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
        ]);
        $location->update(['name' => $request->name]);
        return redirect()->route('admin.parameters', ['tab' => 'locations'])->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroyLocation(\App\Models\Location $location)
    {
        $location->delete();
        return redirect()->route('admin.parameters', ['tab' => 'locations'])->with('success', 'Ubicación eliminada correctamente.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name',
            'color' => 'required|string|max:20',
            'is_active' => 'required|boolean',
        ]);
        \App\Models\TicketCategory::create($request->only('name', 'color', 'is_active'));
        return redirect()->route('admin.parameters', ['tab' => 'categories'])->with('success', 'Categoría agregada correctamente.');
    }

    public function updateCategory(Request $request, \App\Models\TicketCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name,' . $category->id,
            'color' => 'required|string|max:20',
            'is_active' => 'required|boolean',
        ]);
        $category->update($request->only('name', 'color', 'is_active'));
        return redirect()->route('admin.parameters', ['tab' => 'categories'])->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroyCategory(\App\Models\TicketCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.parameters', ['tab' => 'categories'])->with('success', 'Categoría eliminada correctamente.');
    }

    public function storeStatus(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_statuses,name',
            'color' => 'required|string|max:20',
            'is_active' => 'required|boolean',
        ]);
        \App\Models\TicketStatus::create($request->only('name', 'color', 'is_active'));
        return redirect()->route('admin.parameters', ['tab' => 'statuses'])->with('success', 'Estado agregado correctamente.');
    }

    public function updateStatus(Request $request, \App\Models\TicketStatus $status)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_statuses,name,' . $status->id,
            'color' => 'required|string|max:20',
            'is_active' => 'required|boolean',
        ]);
        $status->update($request->only('name', 'color', 'is_active'));
        return redirect()->route('admin.parameters', ['tab' => 'statuses'])->with('success', 'Estado actualizado correctamente.');
    }

    public function destroyStatus(\App\Models\TicketStatus $status)
    {
        $status->delete();
        return redirect()->route('admin.parameters', ['tab' => 'statuses'])->with('success', 'Estado eliminado correctamente.');
    }
} 