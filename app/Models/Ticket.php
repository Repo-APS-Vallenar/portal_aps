<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TicketCategory;
use App\Models\EquipmentInventory;
use App\Models\EquipmentMaintenanceLog;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    // Constantes para las prioridades
    const PRIORITY_BAJA = 'baja';
    const PRIORITY_MEDIA = 'media';
    const PRIORITY_ALTA = 'alta';
    const PRIORITY_URGENTE = 'urgente';

    // Lista de prioridades válidas
    public static $priorities = [
        self::PRIORITY_BAJA,
        self::PRIORITY_MEDIA,
        self::PRIORITY_ALTA,
        self::PRIORITY_URGENTE
    ];

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'status_id',
        'created_by',
        'assigned_to',
        'priority',
        'marca',
        'modelo',
        'numero_serie',
        'location_id',
        'usuario',
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
        'comentarios',
        'contact_phone',
        'contact_email',
        'solucion_aplicada',
        'equipment_inventory_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'fecha_instalacion' => 'date',
        'priority' => 'string'
    ];

    // Boot method para establecer valores por defecto
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->priority)) {
                $ticket->priority = self::PRIORITY_MEDIA;
            }
        });

        static::deleting(function ($ticket) {
            // Eliminar archivos físicos y registros de documentos
            foreach ($ticket->documents as $document) {
                // Eliminar archivo físico
                if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                    \Storage::disk('public')->delete($document->file_path);
                }
                $document->delete();
            }
            // (Opcional) Eliminar la carpeta si está vacía
            $folder = 'ticket-documents/' . $ticket->id;
            if (\Storage::disk('public')->exists($folder) && empty(\Storage::disk('public')->files($folder))) {
                \Storage::disk('public')->deleteDirectory($folder);
            }
        });
    }

    // Relaciones
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function documents()
    {
        return $this->hasMany(TicketDocument::class);
    }

    public function equipmentInventory()
    {
        return $this->belongsTo(EquipmentInventory::class, 'equipment_inventory_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(EquipmentMaintenanceLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    public function scopeByAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereHas('status', function ($q) {
                $q->whereNotIn('name', ['Resuelto', 'Cerrado', 'Cancelado']);
            });
    }

    // Método para obtener el color de la prioridad
    public function getPriorityColor()
    {
        return match ($this->priority) {
            self::PRIORITY_BAJA => 'success',
            self::PRIORITY_MEDIA => 'info',
            self::PRIORITY_ALTA => 'warning',
            self::PRIORITY_URGENTE => 'danger',
            default => 'secondary'
        };
    }

    // Método para obtener el texto de la prioridad
    public function getPriorityText()
    {
        return match ($this->priority) {
            self::PRIORITY_BAJA => 'Baja',
            self::PRIORITY_MEDIA => 'Media',
            self::PRIORITY_ALTA => 'Alta',
            self::PRIORITY_URGENTE => 'Urgente',
            default => 'Desconocida'
        };
    }
}
