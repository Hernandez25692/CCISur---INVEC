<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'unique' => 'El valor ingresado para :attribute ya está en uso.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe exceder los :max caracteres.',
    ],
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo especial.',
        'mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'uncompromised' => 'La :attribute ingresada ha sido filtrada en una brecha de datos externa. Por favor elija una diferente.',
    ],
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'current_password' => 'contraseña actual',
        'superadmin_password' => 'contraseña de superadministrador',
        'name' => 'nombre completo',
        'rol' => 'rol',
    ],
];
