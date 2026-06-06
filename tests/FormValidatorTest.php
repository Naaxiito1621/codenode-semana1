<?php

namespace Tests;

use App\FormValidator;
use PHPUnit\Framework\TestCase;

class FormValidatorTest extends TestCase
{
    private FormValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new FormValidator();
    }

    // --- validateName ---

    public function testValidateNameAcceptsValidNames(): void
    {
        $this->assertTrue($this->validator->validateName('Juan'));
        $this->assertTrue($this->validator->validateName('María José'));
        $this->assertTrue($this->validator->validateName('José Ángel'));
    }

    public function testValidateNameRejectsEmptyString(): void
    {
        $this->assertFalse($this->validator->validateName(''));
        $this->assertFalse($this->validator->validateName('   '));
    }

    public function testValidateNameRejectsNumbers(): void
    {
        $this->assertFalse($this->validator->validateName('Juan123'));
        $this->assertFalse($this->validator->validateName('12345'));
    }

    public function testValidateNameRejectsSpecialCharacters(): void
    {
        $this->assertFalse($this->validator->validateName('Juan@García'));
        $this->assertFalse($this->validator->validateName('Juan#'));
    }

    // --- validateEmail ---

    public function testValidateEmailAcceptsValidEmails(): void
    {
        $this->assertTrue($this->validator->validateEmail('user@example.com'));
        $this->assertTrue($this->validator->validateEmail('name.surname@domain.co'));
        $this->assertTrue($this->validator->validateEmail('test+tag@gmail.com'));
    }

    public function testValidateEmailRejectsInvalidEmails(): void
    {
        $this->assertFalse($this->validator->validateEmail(''));
        $this->assertFalse($this->validator->validateEmail('notanemail'));
        $this->assertFalse($this->validator->validateEmail('missing@'));
        $this->assertFalse($this->validator->validateEmail('@domain.com'));
    }

    // --- validatePhone ---

    public function testValidatePhoneAcceptsValidSpanishNumbers(): void
    {
        $this->assertTrue($this->validator->validatePhone('664256891'));
        $this->assertTrue($this->validator->validatePhone('912345678'));
        $this->assertTrue($this->validator->validatePhone('+34664256891'));
    }

    public function testValidatePhoneAcceptsNumbersWithSpacesOrDashes(): void
    {
        $this->assertTrue($this->validator->validatePhone('664 256 891'));
        $this->assertTrue($this->validator->validatePhone('664-256-891'));
    }

    public function testValidatePhoneRejectsInvalidNumbers(): void
    {
        $this->assertFalse($this->validator->validatePhone(''));
        $this->assertFalse($this->validator->validatePhone('12345'));
        $this->assertFalse($this->validator->validatePhone('123456789'));  // starts with 1
        $this->assertFalse($this->validator->validatePhone('5551234567890'));
    }

    // --- validateDni ---

    public function testValidateDniAcceptsValidDni(): void
    {
        // 12345678Z is a valid DNI (12345678 % 23 = 14 → letter Z)
        $this->assertTrue($this->validator->validateDni('12345678Z'));
        $this->assertTrue($this->validator->validateDni('00000000T'));
    }

    public function testValidateDniAcceptsLowercaseInput(): void
    {
        $this->assertTrue($this->validator->validateDni('12345678z'));
    }

    public function testValidateDniRejectsInvalidLetter(): void
    {
        $this->assertFalse($this->validator->validateDni('12345678A'));
    }

    public function testValidateDniRejectsInvalidFormat(): void
    {
        $this->assertFalse($this->validator->validateDni(''));
        $this->assertFalse($this->validator->validateDni('1234567Z'));   // 7 digits
        $this->assertFalse($this->validator->validateDni('123456789Z')); // 9 digits
        $this->assertFalse($this->validator->validateDni('ABCDEFGHZ'));  // letters instead of digits
    }

    // --- validateAll ---

    public function testValidateAllReturnsEmptyForValidData(): void
    {
        $data = [
            'name' => 'Juan',
            'Apellidos' => 'García López',
            'email' => 'juan@example.com',
            'telefono' => '664256891',
            'Dni' => '12345678Z',
        ];

        $errors = $this->validator->validateAll($data);
        $this->assertEmpty($errors);
    }

    public function testValidateAllReturnsErrorsForMissingFields(): void
    {
        $errors = $this->validator->validateAll([]);
        $this->assertCount(5, $errors);
    }

    public function testValidateAllReturnsPartialErrors(): void
    {
        $data = [
            'name' => 'Juan',
            'Apellidos' => 'García',
            'email' => 'invalid-email',
            'telefono' => '664256891',
            'Dni' => '12345678Z',
        ];

        $errors = $this->validator->validateAll($data);
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('correo', $errors[0]);
    }
}
