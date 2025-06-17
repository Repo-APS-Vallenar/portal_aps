<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Location;

class EquipmentInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment_inventories';

    protected $fillable = [
        'marca',
        'modelo',
        'numero_serie',
        'tipo',
        'estado',
        'usuario',
        'location_id',
        'box_oficina',
        'fecha_adquisicion',
        'ultima_mantenimiento',
        'proximo_mantenimiento',
        'observaciones',
        'ip_red_wifi',
        'cpu',
        'ram',
        'capacidad_almacenamiento',
        'tarjeta_video',
        'id_anydesk',
        'pass_anydesk',
        'version_windows',
        'licencia_windows',
        'version_office',
        'licencia_office',
        'password_cuenta',
        'fecha_instalacion',
        'comentarios'
    ];

    protected $casts = [
        'fecha_instalacion' => 'datetime',
        'fecha_adquisicion' => 'datetime',
        'ultima_mantenimiento' => 'datetime',
        'proximo_mantenimiento' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'numero_serie', 'numero_serie');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(EquipmentMaintenanceLog::class);
    }
}
