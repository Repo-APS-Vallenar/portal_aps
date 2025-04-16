<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
class SettingController extends Controller
{
    public function toggleMaintenance()
    {
        $current = Setting::getValue('maintenance_mode');
        $newValue = $current === 'on' ? 'off' : 'on';
        Setting::setValue('maintenance_mode', $newValue);

        return back()->with('success', 'Modo mantenimiento actualizado.');
    }

    public function index()
    {
        abort_unless(auth()->user()->role === 'superadmin', 403);

        $maintenance = Setting::getValue('maintenance_mode');
        return view('settings.index', compact('maintenance'));
    }
}
