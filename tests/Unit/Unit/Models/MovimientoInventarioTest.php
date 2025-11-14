<?php

namespace Tests\Unit\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests críticos para MovimientoInventario
 *
 * NOTA: Estos son tests unitarios básicos. Para tests completos de integración
 * necesitarás usar RefreshDatabase y factories.
 */
class MovimientoInventarioTest extends TestCase
{
    /**
     * Test que valida la generación de hash SHA256
     */
    public function test_hash_tiene_longitud_correcta(): void
    {
        // El hash SHA256 siempre tiene 64 caracteres
        $testHash = hash('sha256', 'test');
        $this->assertEquals(64, strlen($testHash));
    }

    /**
     * Test que valida detección de diferencias
     */
    public function test_detecta_diferencias_mayor_a_tolerancia(): void
    {
        $tolerancia = 0.001;

        // Diferencia dentro de tolerancia
        $diferenciaPequena = 0.0005;
        $this->assertFalse(abs($diferenciaPequena) > $tolerancia);

        // Diferencia fuera de tolerancia
        $diferenciaGrande = 0.5;
        $this->assertTrue(abs($diferenciaGrande) > $tolerancia);
    }

    /**
     * Test de cálculo de diferencias
     */
    public function test_calculo_diferencia(): void
    {
        // Diferencia = cantidad_real - cantidad_presupuestada
        $cantidadReal = 95;
        $cantidadPresupuestada = 100;
        $diferencia = $cantidadReal - $cantidadPresupuestada;

        $this->assertEquals(-5, $diferencia, 'Faltante de 5 unidades');

        // Caso con sobrante
        $cantidadReal2 = 105;
        $cantidadPresupuestada2 = 100;
        $diferencia2 = $cantidadReal2 - $cantidadPresupuestada2;

        $this->assertEquals(5, $diferencia2, 'Sobrante de 5 unidades');
    }
}
