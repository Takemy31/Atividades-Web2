<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_registered_users_receive_cliente_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'email' => 'cliente@teste.com',
            'role' => 'cliente',
        ]);
    }

    public function test_admin_can_change_user_role_and_bibliotecario_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'bibliotecario',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'bibliotecario']);

        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);

        $this->actingAs($bibliotecario)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin',
            ])
            ->assertForbidden();
    }

    public function test_bibliotecario_can_access_catalog_forms_and_cliente_cannot(): void
    {
        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);
        $cliente = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($bibliotecario)
            ->get(route('books.create.select'))
            ->assertOk();

        $this->actingAs($cliente)
            ->get(route('books.create.select'))
            ->assertForbidden();
    }
}
