<?php

namespace App;

class FormProcessor
{
    private FormValidator $validator;

    public function __construct(?FormValidator $validator = null)
    {
        $this->validator = $validator ?? new FormValidator();
    }

    /**
     * Format a single field with a label prefix.
     */
    public function formatField(string $label, string $value): string
    {
        return $label . ': ' . trim($value);
    }

    /**
     * Format all form data into display strings.
     */
    public function formatFormData(array $data): array
    {
        return [
            'nombre' => $this->formatField('Este mensaje fue enviado por', $data['name'] ?? ''),
            'apellidos' => $this->formatField('Sus Apellidos', $data['Apellidos'] ?? ''),
            'email' => $this->formatField('Su correo es', $data['email'] ?? ''),
            'telefono' => $this->formatField('Numero de telefono', $data['telefono'] ?? ''),
            'dni' => $this->formatField('Dni', $data['Dni'] ?? ''),
        ];
    }

    /**
     * Process form submission. Returns formatted data or validation errors.
     */
    public function process(array $data): array
    {
        $errors = $this->validator->validateAll($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'data' => $this->formatFormData($data)];
    }

    /**
     * Get the redirect URL after successful form processing.
     */
    public function getRedirectUrl(): string
    {
        return 'respuesta.html';
    }
}
