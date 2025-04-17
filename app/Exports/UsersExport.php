<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromArray, WithHeadings
{
    protected $usuarios;

    public function __construct($usuarios)
    {
        $this->usuarios = $usuarios;
    }

    public function array(): array
    {
        return $this->usuarios->map(function ($user) {
            return [
                'Nombre' => $user->name,
                'Correo' => $user->email,
                'Rol' => $this->getTranslatedRole($user->role),
                'Estado' => $user->is_active ? 'Activo' : 'Bloqueado',
                'Creado' => $user->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Nombre', 'Correo', 'Rol', 'Estado', 'Creado'];
    }

    private function getTranslatedRole($role)
    {
        return match ($role) {
            'superadmin' => 'Superadministrador',
            'admin' => 'Administrador',
            default => 'Usuario',
        };
    }
}
