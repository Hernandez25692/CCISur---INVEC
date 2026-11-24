<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Tests\TestCase;

class NormalizacionMarcasTiposTest extends TestCase
{
    public function test_normalizar_marca_reconoce_marcas_existentes_insensible_a_caso(): void
    {
        $this->assertSame('HP', Dispositivo::normalizarMarca('hp'));
        $this->assertSame('HP', Dispositivo::normalizarMarca('Hp'));
        $this->assertSame('Cisco', Dispositivo::normalizarMarca('cisco'));
        $this->assertSame('Samsung', Dispositivo::normalizarMarca('samsung'));
    }

    public function test_normalizar_marca_formatea_marcas_nuevas(): void
    {
        $this->assertSame('Anker', Dispositivo::normalizarMarca('anker'));
        $this->assertSame('Kensington', Dispositivo::normalizarMarca('kensington'));
        $this->assertSame('IBM', Dispositivo::normalizarMarca('ibm'));
    }

    public function test_normalizar_tipo_dispositivo_preserva_acronimos_y_capitaliza(): void
    {
        $this->assertSame('No-break / UPS', Dispositivo::normalizarTipo('no-break / ups'));
        $this->assertSame('Router', Dispositivo::normalizarTipo('router'));
        $this->assertSame('Lector de Barras', Dispositivo::normalizarTipo('lector de barras'));
        $this->assertSame('Firma Digital', Dispositivo::normalizarTipo('firma digital'));
    }

    public function test_normalizar_tipo_mobiliario(): void
    {
        $this->assertSame('Escritorio', Mobiliario::normalizarTipo('escritorio'));
        $this->assertSame('Silla', Mobiliario::normalizarTipo('silla'));
        $this->assertSame('Podio', Mobiliario::normalizarTipo('podio'));
        $this->assertSame('Mueble de Cafetera', Mobiliario::normalizarTipo('mueble de cafetera'));
    }

    public function test_cargador_no_forma_parte_de_tipos_independientes(): void
    {
        $this->assertNotContains('Cargador de laptop', Dispositivo::TIPOS);
        $this->assertNotContains('Cargador de laptop', Dispositivo::TIPOS_SERIE_OPCIONAL);
    }
}
