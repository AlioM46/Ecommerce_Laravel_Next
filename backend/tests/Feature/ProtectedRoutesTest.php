<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function ValidPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'ali123123@test.com',
        'password' => 'password123',
    ], $overrides);
}

test("unauthorized user can't access protected routes", function () {

    $response = $this->getJson("/api/protected");
    $response->assertStatus(401);
});


test("authorized user access to protected route", function () {


// Arrange
User::factory()->create([
    "email" => "ali123123@test.com",
    "password" => Hash::make('password123'),
    "name" => "Ali"
]);

// Act

$login = $this->postJson("/api/auth/login", ValidPayload());


// assert
$login->assertStatus(200);
$login->assertJsonStructure([
        'isSuccess',
        'data' => [
            'accessToken',
            'user',
        ],
    ]);

// arrange for second case
$token = $login->json('data.accessToken');


$protectedResponse =$this->withHeaders([
    "Authorization" => "Bearer $token"
])->getJson("/api/protected");


$protectedResponse->assertStatus(200);


});