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
}
