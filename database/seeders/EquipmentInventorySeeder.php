<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentInventory;
use App\Models\Location;

class EquipmentInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locationIds = Location::pluck('id')->toArray();

        $equipmentData = [
            [
                'marca' => 'Dell',
                'modelo' => 'XPS 15',
                'numero_serie' => 'SN001-DEL',
                'tipo' => 'Notebook',
                'estado' => 'Activo',
                'usuario' => 'Juan Pérez',
                'fecha_adquisicion' => '2022-01-15',
                'ultima_mantenimiento' => '2023-01-15',
                'proximo_mantenimiento' => '2024-01-15',
                'observaciones' => 'Uso general, buen estado.',
                'ip_red_wifi' => '192.168.1.100',
                'cpu' => 'Intel Core i7',
                'ram' => '16GB',
                'capacidad_almacenamiento' => '512GB SSD',
                'tarjeta_video' => 'NVIDIA GeForce RTX 3050',
                'id_anydesk' => '123456789',
                'pass_anydesk' => 'anypass1',
                'version_windows' => 'Windows 11 Pro',
                'licencia_windows' => 'LIC-WIN-001',
                'version_office' => 'Office 365',
                'licencia_office' => 'LIC-OFF-001',
                'password_cuenta' => 'userpass1',
                'comentarios' => 'Asignado a jefe de proyectos.',
            ],
            [
                'marca' => 'HP',
                'modelo' => 'EliteDesk 800 G6',
                'numero_serie' => 'SN002-HP',
                'tipo' => 'Escritorio',
                'estado' => 'Activo',
                'usuario' => 'María García',
                'fecha_adquisicion' => '2021-03-20',
                'ultima_mantenimiento' => '2023-03-20',
                'proximo_mantenimiento' => '2024-03-20',
                'observaciones' => 'Rendimiento alto, para diseño.',
                'ip_red_wifi' => '192.168.1.101',
                'cpu' => 'Intel Core i9',
                'ram' => '32GB',
                'capacidad_almacenamiento' => '1TB SSD',
                'tarjeta_video' => 'AMD Radeon RX 6800 XT',
                'id_anydesk' => '987654321',
                'pass_anydesk' => 'anypass2',
                'version_windows' => 'Windows 10 Pro',
                'licencia_windows' => 'LIC-WIN-002',
                'version_office' => 'Office 2019',
                'licencia_office' => 'LIC-OFF-002',
                'password_cuenta' => 'userpass2',
                'comentarios' => 'Uso en departamento de diseño.',
            ],
            [
                'marca' => 'Lenovo',
                'modelo' => 'ThinkPad X1 Carbon',
                'numero_serie' => 'SN003-LEN',
                'tipo' => 'Notebook',
                'estado' => 'En Reparación',
                'usuario' => 'Carlos Ruíz',
                'fecha_adquisicion' => '2023-07-01',
                'ultima_mantenimiento' => '2024-01-01',
                'proximo_mantenimiento' => '2025-01-01',
                'observaciones' => 'Pantalla rota, en espera de repuesto.',
                'ip_red_wifi' => '192.168.1.102',
                'cpu' => 'Intel Core i5',
                'ram' => '8GB',
                'capacidad_almacenamiento' => '256GB SSD',
                'tarjeta_video' => 'Integrada',
                'id_anydesk' => '112233445',
                'pass_anydesk' => 'anypass3',
                'version_windows' => 'Windows 11 Home',
                'licencia_windows' => 'LIC-WIN-003',
                'version_office' => 'Office 365',
                'licencia_office' => 'LIC-OFF-003',
                'password_cuenta' => 'userpass3',
                'comentarios' => 'Para uso administrativo.',
            ],
            [
                'marca' => 'Apple',
                'modelo' => 'iMac 24-inch',
                'numero_serie' => 'SN004-APL',
                'tipo' => 'Escritorio',
                'estado' => 'Activo',
                'usuario' => 'Laura Fernández',
                'fecha_adquisicion' => '2022-05-10',
                'ultima_mantenimiento' => '2023-05-10',
                'proximo_mantenimiento' => '2024-05-10',
                'observaciones' => 'Excelente para edición de video.',
                'ip_red_wifi' => '192.168.1.103',
                'cpu' => 'Apple M1',
                'ram' => '16GB',
                'capacidad_almacenamiento' => '512GB SSD',
                'tarjeta_video' => 'Integrada Apple M1',
                'id_anydesk' => '556677889',
                'pass_anydesk' => 'anypass4',
                'version_windows' => 'macOS Ventura',
                'licencia_windows' => 'LIC-MAC-001',
                'version_office' => 'Office 365 Mac',
                'licencia_office' => 'LIC-OFF-MAC-001',
                'password_cuenta' => 'userpass4',
                'comentarios' => 'Asignado a equipo de marketing.',
            ],
            [
                'marca' => 'Acer',
                'modelo' => 'Aspire 5',
                'numero_serie' => 'SN005-ACR',
                'tipo' => 'Notebook',
                'estado' => 'Activo',
                'usuario' => 'Pedro Soto',
                'fecha_adquisicion' => '2020-08-01',
                'ultima_mantenimiento' => '2022-08-01',
                'proximo_mantenimiento' => '2024-08-01',
                'observaciones' => 'Buen rendimiento para tareas diarias.',
                'ip_red_wifi' => '192.168.1.104',
                'cpu' => 'AMD Ryzen 5',
                'ram' => '8GB',
                'capacidad_almacenamiento' => '256GB SSD',
                'tarjeta_video' => 'Integrada',
                'id_anydesk' => '998877665',
                'pass_anydesk' => 'anypass5',
                'version_windows' => 'Windows 10 Home',
                'licencia_windows' => 'LIC-WIN-004',
                'version_office' => 'Office 2016',
                'licencia_office' => 'LIC-OFF-004',
                'password_cuenta' => 'userpass5',
                'comentarios' => 'Laptop de respaldo.',
            ]
        ];

        foreach ($equipmentData as $data) {
            $data['location_id'] = $locationIds[array_rand($locationIds)];
            EquipmentInventory::create($data);
        }
    }
}
