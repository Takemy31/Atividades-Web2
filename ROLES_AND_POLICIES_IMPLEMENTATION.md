# Atividade 7 - Sistema de Papéis (Roles) e Políticas (Policies) de Autorização

## Resumo da Implementação

Foi implementado com sucesso um sistema completo de controle de acesso baseado em papéis (RBAC - Role-Based Access Control) na aplicação Laravel de biblioteca, com três níveis de autorização:

### Papéis Implementados:

1. **admin**: Acesso total ao sistema
   - Criar, editar e visualizar todos os recursos (livros, autores, categorias, editoras)
   - Editar papéis de usuários
   - Gerenciar empréstimos

2. **bibliotecario**: Acesso de bibliotecário
   - Criar, editar e visualizar: livros, autores, categorias, editoras
   - Registrar e gerenciar empréstimos
   - **NÃO** pode editar papéis de usuários

3. **cliente**: Acesso de cliente
   - Apenas visualizar informações
   - **NÃO** pode criar, editar ou deletar recursos
   - **NÃO** pode gerenciar empréstimos

---

## Alterações Realizadas

### 1. Banco de Dados

**Arquivo:** `database/migrations/2026_06_26_000000_add_role_to_users_table.php`
- Criada migração que adiciona a coluna `role` à tabela `users`
- Tipo: ENUM com valores: 'admin', 'bibliotecario', 'cliente'
- Valor padrão: 'cliente'
- Posicionada após a coluna 'email'

### 2. Modelo User

**Arquivo:** `app/Models/User.php`
- Adicionado `role` ao array `#[Fillable]`
- Criados métodos auxiliares:
  - `isAdmin()`: Verifica se o usuário é administrador
  - `isBibliotecario()`: Verifica se é bibliotecário
  - `isCliente()`: Verifica se é cliente

### 3. Políticas (Policies) Laravel

Foram criadas as seguintes políticas de autorização:

**`app/Policies/UserPolicy.php`**
- `viewAny()`: Todos podem visualizar lista de usuários
- `view()`: Todos podem visualizar perfil
- `update()`: Apenas admins
- `updateRole()`: Apenas admins podem alterar papéis

**`app/Policies/BookPolicy.php`**
- `viewAny()`, `view()`: Todos podem visualizar
- `create()`, `update()`, `delete()`: Apenas admin e bibliotecario

**`app/Policies/AuthorPolicy.php`**
- Mesmas regras que BookPolicy

**`app/Policies/CategoryPolicy.php`**
- Mesmas regras que BookPolicy

**`app/Policies/PublisherPolicy.php`**
- Mesmas regras que BookPolicy

### 4. Provedor de Autenticação

**Arquivo:** `app/Providers/AuthServiceProvider.php`
- Criado novo provedor para registrar todas as políticas
- Mapeamento de modelos para suas respectivas políticas

**Arquivo:** `bootstrap/providers.php`
- Adicionado `AuthServiceProvider` à lista de provedores de inicialização

### 5. Controladores Atualizados

Todos os controladores agora usam `$this->authorize()` para verificar permissões:

**`app/Http/Controllers/BookController.php`**
- Adicionados checks de autorização em: `createWithId()`, `storeWithId()`, `createWithSelect()`, `storeWithSelect()`, `edit()`, `update()`, `show()`, `index()`, `destroy()`

**`app/Http/Controllers/AuthorController.php`**
- Adicionados checks em: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`

**`app/Http/Controllers/CategoryController.php`**
- Adicionados checks em: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`

**`app/Http/Controllers/PublisherController.php`**
- Adicionados checks em: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`

**`app/Http/Controllers/UserController.php`**
- Adicionados checks em: `index()`, `show()`, `edit()`, `update()`
- Lógica especial: Apenas admins podem atualizar o campo `role`

**`app/Http/Controllers/BorrowingController.php`**
- Adicionados checks: `store()` e `returnBook()` apenas para admin/bibliotecario

### 6. Autenticação e Registro

**Arquivo:** `app/Http/Controllers/Auth/RegisterController.php`
- Modificado método `create()` para atribuir papel 'cliente' automaticamente ao novo usuário

### 7. Factory de Testes

**Arquivo:** `database/factories/UserFactory.php`
- Adicionado campo `role` com valor padrão 'cliente'

### 8. Seeders

**Arquivo:** `database/seeders/AdminUserSeeder.php`
- Criado novo seeder que cria um usuário administrador padrão
- Email: `admin@biblioteca.com`
- Senha: `admin123`
- Role: `admin`
- Usa `firstOrCreate()` para evitar duplicatas

**Arquivo:** `database/seeders/DatabaseSeeder.php`
- Adicionado `AdminUserSeeder::class` à lista de seeders

### 9. Visualizações (Views)

**`resources/views/users/index.blade.php`**
- Adicionada coluna de papel/role
- Badges coloridas indicando o tipo de papel:
  - Vermelho (bg-danger) para admin
  - Azul (bg-info) para bibliotecario
  - Cinza (bg-secondary) para cliente

**`resources/views/users/edit.blade.php`**
- Adicionado seletor de papel (role) visível apenas para admins
- Para não-admins, exibe o papel como campo desabilitado com mensagem informativa

**`resources/views/users/show.blade.php`**
- Adicionada exibição do papel do usuário com badge colorida

### 10. Testes Automatizados

**Arquivo:** `tests/Feature/RoleAuthorizationTest.php`
- Teste 1: Verifica se novos usuários registrados recebem papel 'cliente'
- Teste 2: Valida que admins podem alterar papéis e bibliotecários não podem
- Teste 3: Confirma que bibliotecários têm acesso a formulários de criação, mas clientes não

**Configuração de Testes:** `phpunit.xml`
- Alterado para usar banco de dados MySQL para testes (atv_01_test)
- Todos os 3 testes passando com sucesso ✓

---

## Como Usar

### Fazendo Login

1. **Usuário Admin**:
   - Email: `admin@biblioteca.com`
   - Senha: `admin123`
   - Acesso: Total ao sistema

2. **Novos Usuários**:
   - Registram-se com papel padrão 'cliente'
   - Admins podem editar usuários para atribuir papel 'bibliotecario'

### Fluxo de Autorização

1. Quando um usuário tenta acessar uma ação (criar, editar, deletar livro, etc.):
   - O controlador chama `$this->authorize('action', Model::class)`
   - A política correspondente é consultada
   - Se não autorizado, lança exceção `AuthorizationException` (erro 403)

2. Mensagens de erro automáticas:
   - Laravel exibe página de acesso negado (403) quando o usuário não tem permissão

---

## Estrutura das Políticas

Cada política segue o padrão Laravel:

```php
// Exemplo: BookPolicy
public function create(User $user): bool
{
    return $user->isAdmin() || $user->isBibliotecario();
}

public function update(User $user, Book $book): bool
{
    return $user->isAdmin() || $user->isBibliotecario();
}

public function delete(User $user, Book $book): bool
{
    return $user->isAdmin() || $user->isBibliotecario();
}
```

---

## Testes de Sucesso

```
PASS  Tests\Feature\RoleAuthorizationTest
✓ newly registered users receive cliente role                          3.92s  
✓ admin can change user role and bibliotecario cannot                  0.05s  
✓ bibliotecario can access catalog forms and cliente cannot            0.03s  

Tests:    3 passed (9 assertions)
Duration: 4.09s
```

---

## Recursos Adicionais

- **Documentação Laravel Authorization**: https://laravel.com/docs/13.x/authorization
- **Tutorial Policies**: https://medium.com/@zulfikarditya/mastering-laravel-policies-a-complete-guide-to-authorization-in-laravel-991bbdcc6756

---

## Próximos Passos Opcionais

1. Implementar middleware para verificar papéis em rotas específicas
2. Adicionar soft deletes para auditoria de usuários deletados
3. Implementar logs de ações para rastreabilidade
4. Adicionar mais papéis customizáveis (ex: 'supervisor', 'curator')
5. Implementar sistema de permissões granulares (permissions + roles)
