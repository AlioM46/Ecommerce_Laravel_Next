<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);
function ValidLoginPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'ali123123@test.com',
        'password' => 'password123',
    ], $overrides);
}
/*
CASES: 
[1]
SUCCESS LOGIN:
CODE => 200
JSON => {
isSuccess => true,
data => {
accessToken,
user
    }
}

[2]:
Wrong Password or Email
CODE => 401
JSON => {
isSuccess => false,
information
}

[3][4]:
Validation Inputs Error:
Missing Email or Password
CODE => 422
*/

test("invalid password" , function () {
User::factory()->create([
    "email" => "ali123123@test.com",
    "password" => Hash::make('password123'),
    "name" => "Ali"
]);


$response = $this->postJson("/api/auth/login", ValidLoginPayload(["password" => null]));

$response->assertStatus(422);
$response->assertJsonValidationErrors(["password"]);

});

test("invalid email" , function () {
User::factory()->create([
    "email" => "ali123123@test.com",
    "password" => Hash::make('password123'),
    "name" => "Ali"
]);


$response = $this->postJson("/api/auth/login", ValidLoginPayload(["email" => null]));

$response->assertStatus(422);
$response->assertJsonValidationErrors(["email"]);

});

test("wrong email or password", function () {
// Arrange
User::factory()->create([
    "email" => "ali123123@test.com",
    "password" => Hash::make('password123'),
    "name" => "Ali"
]);

//Act
$response = $this->postJson("/api/auth/login", ValidLoginPayload(["email" => "wrongEmail@test.com", "password" => "wrongPassword123123"]));

$response->assertStatus(401);
$response->assertJsonStructure([
    "isSuccess",
    "information"
]);

$response->assertJsonPath("isSuccess" , false);


});


test("success login with ok(200)", function () {

// Arrange
User::factory()->create([
    "email" => "ali123123@test.com",
    "password" => Hash::make('password123'),
    "name" => "Ali"
]);
//Act
$response = $this->postJson("/api/auth/login", ValidLoginPayload());

// Assert => check if response as expected

$response->assertStatus(200);

$response->assertJsonStructure([
        'isSuccess',
        'data' => [
            'accessToken',
            'user',
        ],
    ]);

    
expect($response->json('data.accessToken'))->not->toBeEmpty();
$response->assertJsonPath("isSuccess" , true);


});