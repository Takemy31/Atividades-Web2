@extends('layouts.app')

@section('content')
<div class="container">
<div class="container">
    <a href="{{ route('home') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Voltar
</a>

    <h1 class="my-4">Controle de Multas</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($users->isEmpty())

        <div class="alert alert-success">
            Nenhum usuário possui multas pendentes.
        </div>

    @else

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Débito</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody>

            @foreach($users as $user)

                <tr>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        R$ {{ number_format($user->debit,2,',','.') }}
                    </td>

                    <td>

                        <form action="{{ route('users.clearDebit',$user) }}" method="POST">

                            @csrf
                            @method('PATCH')

                            <button class="btn btn-success btn-sm">

                                Receber Pagamento

                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    @endif

</div>
@endsection