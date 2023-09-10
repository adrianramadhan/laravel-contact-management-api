<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post("/api/users", [
            "username" => "Adrian",
            "password" => "rahasia",
            "name" => "Adrian Ramadhan"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "Adrian",
                    "name" => "Adrian Ramadhan"
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => " "
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => ["The username field is required."],
                    "password" => ["The password field is required."],
                    "name" => ["The name field is required."],
                ]
            ]);
    }

    public function testRegisterFailedUsernameExists()
    {
        $this->testRegisterSuccess();
        $this->post("/api/users", [
            "username" => "Adrian",
            "password" => "rahasia",
            "name" => "Adrian Ramadhan"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "Adrian",
            "password" => "rahasia",
            "name" => "Adrian Ramadhan"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "Adrian",
                    "name" => "Adrian Ramadhan"
                ]
            ]);

        $user = User::where("username", "Adrian")->first();
        assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post("/api/users/login", [
            "username" => "Adrian",
            "password" => "rahasia",
            "name" => "Adrian Ramadhan"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "Adrian",
            "password" => "rahasia123",
            "name" => "Adrian Ramadhan"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        
        $this->get("/api/users/current", [
            "Authorization" => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "Adrian",
                    "name" => "Adrian Ramadhan"
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        
        $this->get("/api/users/current", [
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => ['unauthorized']
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", [
            "Authorization" => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => ['unauthorized']
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current',
            [
                'name' => 'Eko'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'Eko'
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current',
            [
                'name' => 'EkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEko'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }
}
