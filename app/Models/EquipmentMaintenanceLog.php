<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenanceLog extends Model
{
    protected $fillable = [
        'equipment_inventory_id',
        'ticket_id',
        'user_id',
        'maintenance_date',
        'description_of_work',
        'type_of_maintenance',
    ];

    /**
     * Get the equipment inventory that owns the log.
     */
    public function equipmentInventory(): BelongsTo
    {
        return $this->belongsTo(EquipmentInventory::class);
    }

    /**
     * Get the ticket that owns the log.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
