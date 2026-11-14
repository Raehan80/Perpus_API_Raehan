<?php

namespace App\Http\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthResponse',
    required: ['message', 'token', 'token_type', 'user'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil'),
        new OA\Property(property: 'token', type: 'string', example: '1|abc123tokenxyz'),
        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
class AuthResponse
{
}
