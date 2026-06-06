<?php

namespace Tests;

use App\FormProcessor;
use App\FormValidator;
use PHPUnit\Framework\TestCase;

class FormProcessorTest extends TestCase
{
    private FormProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new FormProcessor();
    }

    // --- formatField ---

    public function testFormatFieldConcatenatesLabelAndValue(): void
    {
        $result = $this->processor->formatField('Nombre', 'Juan');
        $this->assertEquals('Nombre: Juan', $result);
    }

    public function testFormatFieldTrimsValue(): void
    {
        $result = $this->processor->formatField('Nombre', '  Juan  ');
        $this->assertEquals('Nombre: Juan', $result);
    }

    public function testFormatFieldHandlesEmptyValue(): void
    {
        $result = $this->processor->formatField('Nombre', '');
        $this->assertEquals('Nombre: ', $result);
    }

    // --- formatFormData ---

    public function testFormatFormDataReturnsAllFields(): void
    {
        $data = [
            'name' => 'Juan',
            'Apellidos' => 'García',
            'email' => 'juan@example.com',
            'telefono' => '664256891',
            'Dni' => '12345678Z',
        ];

        $result = $this->processor->formatFormData($data);

        $this->assertEquals('Este mensaje fue enviado por: Juan', $result['nombre']);
        $this->assertEquals('Sus Apellidos: García', $result['apellidos']);
        $this->assertEquals('Su correo es: juan@example.com', $result['email']);
        $this->assertEquals('Numero de telefono: 664256891', $result['telefono']);
        $this->assertEquals('Dni: 12345678Z', $result['dni']);
    }

    public function testFormatFormDataHandlesMissingKeys(): void
    {
        $result = $this->processor->formatFormData([]);

        $this->assertEquals('Este mensaje fue enviado por: ', $result['nombre']);
        $this->assertEquals('Sus Apellidos: ', $result['apellidos']);
        $this->assertEquals('Su correo es: ', $result['email']);
        $this->assertEquals('Numero de telefono: ', $result['telefono']);
        $this->assertEquals('Dni: ', $result['dni']);
    }

    // --- process ---

    public function testProcessReturnsSuccessForValidData(): void
    {
        $data = [
            'name' => 'Juan',
            'Apellidos' => 'García López',
            'email' => 'juan@example.com',
            'telefono' => '664256891',
            'Dni' => '12345678Z',
        ];

        $result = $this->processor->process($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
    }

    public function testProcessReturnsErrorsForInvalidData(): void
    {
        $data = [
            'name' => '',
            'Apellidos' => '',
            'email' => 'invalid',
            'telefono' => '123',
            'Dni' => 'bad',
        ];

        $result = $this->processor->process($data);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertNotEmpty($result['errors']);
    }

    public function testProcessReturnsFormattedDataOnSuccess(): void
    {
        $data = [
            'name' => 'María',
            'Apellidos' => 'López Fernández',
            'email' => 'maria@test.org',
            'telefono' => '912345678',
            'Dni' => '00000000T',
        ];

        $result = $this->processor->process($data);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('María', $result['data']['nombre']);
        $this->assertStringContainsString('López Fernández', $result['data']['apellidos']);
    }

    // --- getRedirectUrl ---

    public function testGetRedirectUrlReturnsExpectedPath(): void
    {
        $this->assertEquals('respuesta.html', $this->processor->getRedirectUrl());
    }

    // --- constructor with custom validator ---

    public function testAcceptsCustomValidator(): void
    {
        $mockValidator = $this->createMock(FormValidator::class);
        $mockValidator->method('validateAll')->willReturn([]);

        $processor = new FormProcessor($mockValidator);
        $result = $processor->process(['name' => 'x']);

        $this->assertTrue($result['success']);
    }
}
