<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Constructora López S.A.',
                'ruc' => '80012345-6',
                'telefono' => '021-555-0001',
                'email' => 'contacto@lopez.com.py',
                'direccion' => 'Av. Mariscal López 1234, Asunción',
                'ciudad' => 'Asunción',
                'activo' => true,
                'notas' => 'Cliente frecuente. Solicita factura electrónica.',
            ],
            [
                'nombre' => 'Electricidad Moderna',
                'ruc' => '80023456-7',
                'telefono' => '021-555-0002',
                'email' => 'ventas@electricidadmoderna.com',
                'direccion' => 'Calle Palma 567, Asunción',
                'ciudad' => 'Asunción',
                'activo' => true,
                'notas' => 'Pago contra entrega. Descuento del 5% en pedidos mayores a 5.000.000 Gs.',
            ],
            [
                'nombre' => 'Inmobiliaria Del Este',
                'ruc' => '80034567-8',
                'telefono' => '061-555-0003',
                'email' => 'compras@deleste.com.py',
                'direccion' => 'Ruta 2 Km 25, Ciudad del Este',
                'ciudad' => 'Ciudad del Este',
                'activo' => true,
                'notas' => null,
            ],
            [
                'nombre' => 'Taller Electromecánico Rodríguez',
                'ruc' => '80045678-9',
                'telefono' => '0981-123-456',
                'email' => null,
                'direccion' => 'Barrio San Miguel, c/ 1ro de Mayo',
                'ciudad' => 'San Lorenzo',
                'activo' => true,
                'notas' => 'No tiene email. Contactar solo por WhatsApp.',
            ],
            [
                'nombre' => 'Obras y Proyectos S.R.L.',
                'ruc' => '80056789-0',
                'telefono' => '021-555-0005',
                'email' => 'proyectos@obrasyproyectos.com',
                'direccion' => 'Av. España 890, Asunción',
                'ciudad' => 'Asunción',
                'activo' => true,
                'notas' => 'Cliente VIP. Crédito hasta 60 días.',
            ],
            [
                'nombre' => 'Ferretería Central Encarnación',
                'ruc' => '80067890-1',
                'telefono' => '071-555-0006',
                'email' => 'compras@ferreteriacentral.com',
                'direccion' => 'Av. Cambyretá 456, Encarnación',
                'ciudad' => 'Encarnación',
                'activo' => true,
                'notas' => 'Compra al por mayor para reventa.',
            ],
            [
                'nombre' => 'Instalaciones Eléctricas Profesionales',
                'ruc' => '80078901-2',
                'telefono' => '0982-345-678',
                'email' => 'iep@gmail.com',
                'direccion' => 'Barrio Trinidad, Asunción',
                'ciudad' => 'Asunción',
                'activo' => false,
                'notas' => 'Cliente inactivo. Tiene deuda pendiente.',
            ],
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }
    }
}
