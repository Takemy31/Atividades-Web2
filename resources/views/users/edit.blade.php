@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Editar Usuário</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="name"
                   value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email"
                   value="{{ old('email', $user->email) }}" required>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="mb-3">
            <label class="form-label">Papel/Role</label>
            <select class="form-control" name="role">
                <option value="cliente" {{ old('role', $user->role) === 'cliente' ? 'selected' : '' }}>
                    Cliente
                </option>
                <option value="bibliotecario" {{ old('role', $user->role) === 'bibliotecario' ? 'selected' : '' }}>
                    Bibliotecário
                </option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                    Admin
                </option>
            </select>
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">Papel/Role</label>
            <input type="text" class="form-control" value="{{ $user->role }}" disabled>
            <small class="form-text text-muted">Apenas administradores podem alterar papéis.</small>
        </div>
        @endif

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection