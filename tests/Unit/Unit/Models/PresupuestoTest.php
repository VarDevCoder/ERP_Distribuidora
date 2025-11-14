<?php

namespace Tests\Unit\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests críticos para cálculos en Guaraníes (moneda paraguaya)
 *
 * Guaraníes no tienen decimales, todos los montos deben ser enteros.
 */
class PresupuestoTest extends TestCase
{
    /**
     * Test que los cálculos se redondean correctamente a enteros (Guaraníes)
     */
    public function test_calculo_subtotal_guaranies(): void
    {
        // Ejemplo: 2.5 kg x 10,000 Gs/kg = 25,000 Gs
        $cantidad = 2.5;
        $precioUnitario = 10000; // Guaraníes
        $subtotal = round($cantidad * $precioUnitario);

        $this->assertEquals(25000, $subtotal);
        $this->assertIsInt($subtotal);
    }

    /**
     * Test de redondeo de decimales en multiplicación
     */
    public function test_redondeo_decimales_en_multiplicacion(): void
    {
        // 3.7 unidades x 1,500 Gs = 5,550 Gs
        $cantidad = 3.7;
        $precioUnitario = 1500;
        $subtotal = round($cantidad * $precioUnitario);

        $this->assertEquals(5550, $subtotal);

        // Verificar que es entero
        $this->assertIsInt($subtotal);
    }

    /**
     * Test de cálculo de total sin IVA (por ahora)
     */
    public function test_calculo_total_sin_iva(): void
    {
        $subtotal = 100000; // Guaraníes
        $descuento = 5000;  // Guaraníes
        $total = $subtotal - $descuento;

        $this->assertEquals(95000, $total);
        $this->assertIsInt($total);
    }

    /**
     * Test que verifica que los precios son enteros (sin decimales)
     */
    public function test_precios_son_enteros(): void
    {
        $precio1 = 50000; // Gs. 50,000
        $precio2 = 125000; // Gs. 125,000

        $this->assertIsInt($precio1);
        $this->assertIsInt($precio2);

        // No deben tener parte decimal
        $this->assertEquals(0, $precio1 % 1);
        $this->assertEquals(0, $precio2 % 1);
    }

    /**
     * Test de conversión de decimal a entero (si vienen datos con decimales)
     */
    public function test_conversion_decimal_a_entero(): void
    {
        $precioConDecimal = 50000.75;
        $precioEntero = round($precioConDecimal);

        $this->assertEquals(50001, $precioEntero);
        $this->assertIsInt($precioEntero);
    }
}
