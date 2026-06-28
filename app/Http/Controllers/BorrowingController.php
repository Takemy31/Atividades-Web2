<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        // Only librarians and admins can register borrowings
        $this->authorize('create', Book::class);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $user = User::find($request->user_id);
        
        if ($user->debit > 0) {
        return redirect()
        ->route('books.show', $book)
        ->with('error', 'Este usuário possui multas pendentes e não pode realizar novos empréstimos.');
}
        // Livro já emprestado?        
            $livrosEmprestados = Borrowing::where('user_id', $request->user_id)
            ->whereNull('returned_at')
            ->count();

            if ($livrosEmprestados >= 5) {
            return redirect()
            ->route('books.show', $book)
            ->with('error', 'Este usuário já possui 5 livros emprestados.');
}
        $emprestimoAberto = Borrowing::where('book_id', $book->id)
        ->whereNull('returned_at')
        ->exists();
        
        if ($emprestimoAberto) {
        return redirect()
        ->route('books.show', $book)
        ->with('error', 'Este livro já está emprestado.');
}
        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function returnBook(Borrowing $borrowing)
{
    // Apenas bibliotecários e administradores podem devolver livros
    $this->authorize('update', $borrowing->book);

    $returnedAt = now();

    // Data limite (15 dias após o empréstimo)
    $dueDate = Carbon::parse($borrowing->borrowed_at)->addDays(15);

    // Registrar a devolução
    $borrowing->update([
        'returned_at' => $returnedAt,
    ]);

    // Calcula os dias de atraso (ignorando horas)
    $daysLate = $dueDate->copy()->startOfDay()->diffInDays(
        $returnedAt->copy()->startOfDay(),
        false
    );

    if ($daysLate > 0) {

        $fine = $daysLate * 0.50;

        $user = $borrowing->user;
        $user->debit += $fine;
        $user->save();

        return redirect()
            ->route('books.show', $borrowing->book_id)
            ->with(
                'success',
                'Livro devolvido. Multa aplicada: R$ ' .
                number_format($fine, 2, ',', '.')
            );
    }

    return redirect()
        ->route('books.show', $borrowing->book_id)
        ->with('success', 'Livro devolvido sem atraso.');
}

    public function userBorrowings(User $user)
    {
        $this->authorize('view', $user);
        
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }
}