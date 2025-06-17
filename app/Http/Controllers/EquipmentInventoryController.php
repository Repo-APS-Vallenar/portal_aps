<?php

namespace App\Http\Controllers;

use App\Models\EquipmentInventory;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Exports\EquipmentInventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class EquipmentInventoryController extends Controller
{
    // Comentario para forzar actualización de archivo
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = EquipmentInventory::query();

        // Aplicar filtros si existen
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%")
                  ->orWhere('usuario', 'like', "%{$search}%")
                  ->orWhere('box_oficina', 'like', "%{$search}%");
            });
        }

        if ($request->has('ubicacion') && $request->ubicacion != '') {
            $query->where('location_id', $request->ubicacion);
        }

        // Obtener valores únicos para los filtros
        $marcas = EquipmentInventory::distinct()->pluck('marca');
        $locations = Location::orderBy('name')->pluck('name', 'id');

        $equipment = $query->paginate(10);

        return view('equipment-inventory.index', compact('equipment', 'marcas', 'locations'));
    }

    public function show(EquipmentInventory $equipmentInventory)
    {
        $tickets = $equipmentInventory->tickets()->latest()->get();
        return view('equipment-inventory.show', compact('equipmentInventory', 'tickets'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('equipment-inventory.form', compact('locations'));
    }

    public function store(Request $request)
    {
        Log::info('Inicio de store EquipmentInventory');
        try {
            $validated = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'numero_serie' => 'required|string|max:255|unique:equipment_inventories',
                'tipo' => 'required|string|max:255',
                'estado' => 'required|string|max:255',
                'usuario' => 'nullable|string|max:255',
                'box_oficina' => 'nullable|string|max:255',
                'location_id' => 'required|exists:locations,id',
                'fecha_adquisicion' => 'nullable|date',
                'ultima_mantenimiento' => 'nullable|date',
                'proximo_mantenimiento' => 'nullable|date',
                'observaciones' => 'nullable|string',
                'ip_red_wifi' => 'nullable|string|max:255',
                'cpu' => 'nullable|string|max:255',
                'ram' => 'nullable|string|max:255',
                'capacidad_almacenamiento' => 'nullable|string|max:255',
                'tarjeta_video' => 'nullable|string|max:255',
                'id_anydesk' => 'nullable|string|max:255',
                'pass_anydesk' => 'nullable|string|max:255',
                'version_windows' => 'nullable|string|max:255',
                'licencia_windows' => 'nullable|string|max:255',
                'version_office' => 'nullable|string|max:255',
                'licencia_office' => 'nullable|string|max:255',
                'password_cuenta' => 'nullable|string|max:255',
                'fecha_instalacion' => 'nullable|date',
                'comentarios' => 'nullable|string',
            ]);

            Log::info('Datos validados para EquipmentInventory:', $validated);

            $equipment = EquipmentInventory::create($validated);
            Log::info('Equipo creado exitosamente:', ['id' => $equipment->id]);

            return redirect()->route('equipment-inventory.index')
                ->with('success', 'Equipo agregado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar equipo:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al intentar guardar el equipo. Por favor, inténtalo de nuevo.');
        }
    }

    public function showPartial(EquipmentInventory $equipment)
    {
        return view('equipment-inventory.show-partial', compact('equipment'));
    }

    public function editPartial(EquipmentInventory $equipment)
    {
        $locations = Location::orderBy('name')->get();
        return view('equipment-inventory.edit-partial', compact('equipment', 'locations'));
    }

    public function edit(EquipmentInventory $equipmentInventory)
    {
        $locations = Location::orderBy('name')->get();
        return view('equipment-inventory.form', compact('equipmentInventory', 'locations'));
    }

    public function update(Request $request, EquipmentInventory $equipmentInventory)
    {
        $validated = $request->validate([
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255|unique:equipment_inventories,numero_serie,' . $equipmentInventory->id,
            'tipo' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'usuario' => 'nullable|string|max:255',
            'box_oficina' => 'nullable|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'fecha_adquisicion' => 'nullable|date',
            'ultima_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'ip_red_wifi' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'capacidad_almacenamiento' => 'nullable|string|max:255',
            'tarjeta_video' => 'nullable|string|max:255',
            'id_anydesk' => 'nullable|string|max:255',
            'pass_anydesk' => 'nullable|string|max:255',
            'version_windows' => 'nullable|string|max:255',
            'licencia_windows' => 'nullable|string|max:255',
            'version_office' => 'nullable|string|max:255',
            'licencia_office' => 'nullable|string|max:255',
            'password_cuenta' => 'nullable|string|max:255',
            'fecha_instalacion' => 'nullable|date',
            'comentarios' => 'nullable|string',
        ]);

        $equipmentInventory->update($validated);

        return redirect()->route('equipment-inventory.index')
            ->with('success', 'Equipo actualizado exitosamente.');
    }

    public function destroy(EquipmentInventory $equipmentInventory)
    {
        $equipmentInventory->delete();

        return redirect()->route('equipment-inventory.index')
            ->with('success', 'Equipo eliminado exitosamente.');
    }

    public function export()
    {
        return Excel::download(new EquipmentInventoryExport, 'inventario-equipos.xlsx');
    }

    public function checkSerialNumber(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|string|max:255',
        ]);

        $serialNumber = $request->input('numero_serie');
        $exists = EquipmentInventory::where('numero_serie', $serialNumber)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $equipment = EquipmentInventory::where('numero_serie', 'like', "%{$query}%")
            ->orWhere('marca', 'like', "%{$query}%")
            ->orWhere('modelo', 'like', "%{$query}%")
            ->orWhere('usuario', 'like', "%{$query}%")
            ->with('location:id,name')
            ->take(10)
            ->get(['id', 'marca', 'modelo', 'numero_serie', 'usuario', 'box_oficina', 'location_id']);
            
        return response()->json($equipment);
    }
} 