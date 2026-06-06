<?php

namespace App;

class FormValidator
{
    /**
     * Validate that a name is non-empty and contains only letters and spaces.
     */
    public function validateName(string $name): bool
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return false;
        }
        return (bool) preg_match('/^[\p{L}\s]+$/u', $trimmed);
    }

    /**
     * Validate an email address.
     */
    public function validateEmail(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate a Spanish phone number (9 digits, optionally prefixed with +34).
     */
    public function validatePhone(string $phone): bool
    {
        $cleaned = preg_replace('/[\s\-]/', '', trim($phone));
        return (bool) preg_match('/^(\+34)?[6-9]\d{8}$/', $cleaned);
    }

    /**
     * Validate a Spanish DNI (8 digits followed by a letter).
     */
    public function validateDni(string $dni): bool
    {
        $trimmed = strtoupper(trim($dni));
        if (!preg_match('/^\d{8}[A-Z]$/', $trimmed)) {
            return false;
        }

        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $number = (int) substr($trimmed, 0, 8);
        $expectedLetter = $letters[$number % 23];

        return $trimmed[8] === $expectedLetter;
    }

    /**
     * Validate all form fields. Returns an array of error messages (empty if valid).
     */
    public function validateAll(array $data): array
    {
        $errors = [];

        if (!isset($data['name']) || !$this->validateName($data['name'])) {
            $errors[] = 'El nombre es obligatorio y solo puede contener letras y espacios.';
        }

        if (!isset($data['Apellidos']) || !$this->validateName($data['Apellidos'])) {
            $errors[] = 'Los apellidos son obligatorios y solo pueden contener letras y espacios.';
        }

        if (!isset($data['email']) || !$this->validateEmail($data['email'])) {
            $errors[] = 'El correo electrónico no es válido.';
        }

        if (!isset($data['telefono']) || !$this->validatePhone($data['telefono'])) {
            $errors[] = 'El número de teléfono no es válido (debe ser un número español de 9 dígitos).';
        }

        if (!isset($data['Dni']) || !$this->validateDni($data['Dni'])) {
            $errors[] = 'El DNI no es válido (8 dígitos seguidos de una letra).';
        }

        return $errors;
    }
}
