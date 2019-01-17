<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDratabase;


class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use WithFaker;

    public function testRegiter()
    {
      $attributes = [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->Email(),
            'password' => 'Service',
            'confirm_password' => 'Service',
            'cpf' => rand(00000000000, 99999999999)
        ];
      $response = $this->post('/api/register', $attributes);
      $response->assertStatus(201);
    }

    public function testRegiterWrongEmail ()
    {
      $attributes = [
            'name' => 'Service',
            'email' => 'gmmmgmail.com',
            'password' => 'Service',
            'confirm_password' => 'Service',
            'cpf' => 12345678301
        ];
      $response = $this->post('/api/register', $attributes);
      $response->assertStatus(401);
    }

    public function testRegiterWrongCPF ()
    {
      $attributes = [
            'name' => 'Service',
            'email' => 'gmmm@gmail.com',
            'password' => 'Service',
            'confirm_password' => 'Service',
            'cpf' => 1234567301
        ];
      $response = $this->post('/api/register', $attributes);
      $response->assertStatus(401);
    }

    public function testRegiterWrongPassword ()
    {
      $attributes = [
            'name' => 'Service',
            'email' => 'gmmm@gmail.com',
            'password' => 'Service',
            'confirm_password' => 'Serice',
            'cpf' => 12345378301
        ];
      $response = $this->post('/api/register', $attributes);
      $response->assertStatus(401);
    }

    public function testRegiterWrongName ()
    {
      $attributes = [
            'name' => 'Servic2e',
            'email' => 'gmmm@gmail.com',
            'password' => 'Service',
            'confirm_password' => 'Serice',
            'cpf' => 12345378301
        ];
      $response = $this->post('/api/register', $attributes);
      $response->assertStatus(401);
    }

    public function testLogin()
    {
      $user = User::create([
            'name' => $this->faker->firstName(),
            'email' => $this->faker->Email(),
            'cpf' => rand(00000000000, 99999999999),
            'password' =>  bcrypt('testr1234'),
        ]);
      $attributes = [
            'email' => $user->email,
            'password' => 'testr1234',
        ];
      $response = $this->post('/api/login', $attributes);
      $response->assertStatus(200);
    }

    public function testLoginWrongCredentials()
    {
      $attributes = [
            'email' => 'gmmm@gmail.com',
            'password' => 'Servicsse',
        ];
      $response = $this->post('/api/login', $attributes);
      $response->assertStatus(401);
    }

    public function getToken()
    {
      $user = User::create([
          'name' => $this->faker->firstName(),
          'email' => $this->faker->Email(),
          'cpf' => rand(00000000000, 99999999999),
          'password' => $this->faker->Password(),
        ]);
      return $user->createToken('Token Acess')->accessToken;
    }

    public function testDetails()
    {
      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->getToken(),
          'Accept' => 'application/json'
        ])->json('GET', '/api/details');
        $response->assertStatus(200);
    }

    public function testUpdate()
    {

      $attributes = [
          'email' => $this->faker->Email(),
          'name' => 'TEste',
          'cpf' => rand(00000000000, 99999999999),
        ];
      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->getToken(),
          'Accept' => 'application/json'
      ])->json('POST', '/api/update', $attributes);
      $content = json_decode($response->getContent(), true);
      $response->assertStatus(200);
    }

    public function testUpdateExistCpfEmail()
    {
      $user = User::all()->first();
      $attributes = [
          'email' => $user->email,
          'name' => 'TEste',
          'cpf' => $user->cpf,
        ];
      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->getToken(),
          'Accept' => 'application/json'
      ])->json('POST', '/api/update', $attributes);
      $response->assertStatus(401);
    }

    public function testChangePassword()
    {
      $attributes = [
          'password' => 'Service',
          'confirm_password' => 'Service',
          ];
      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->getToken(),
          'Accept' => 'application/json'
      ])->json('POST', '/api/changepassword', $attributes);
      $response->assertStatus(200);
    }

    public function testDelete()
    {
      $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json'
        ])->json('GET', '/api/delete');
        $response->assertStatus(200);
    }

    public function testLogout()
    {
      $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json'
        ])->json('GET', '/api/logout');
        $response->assertStatus(200);
    }
}
