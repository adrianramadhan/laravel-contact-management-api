<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
