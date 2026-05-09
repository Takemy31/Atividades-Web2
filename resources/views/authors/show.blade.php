@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Autor</h1>

    <div class="card">
        <div class="card-header">
            Autor: {{ $author->name }}
        </div>
        <div class="card-body">
            <p><strong>Nome:</strong> {{ $author->name }}</p>
            <p><strong>Data de Nascimento:</strong> {{ $author->birth_date }}</p>
            <p><strong>email:</strong> {{ $author->email }}</p>
        </div>
    </div>

    <a href="{{ route('authors.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection

