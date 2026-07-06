<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
{
    $books = Book::with(['author', 'publisher', 'category'])->get();

    return response()->json($books);
}

public function show(Book $book)
{
    $book->load(['author', 'publisher', 'category']);

    return response()->json($book);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'published_year' => 'required|integer',
        'pages' => 'required|integer|min:1',
        'publisher_id' => 'required|exists:publishers,id',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
    ]);

    $book = Book::create($validated);

    return response()->json($book, 201);
}

public function update(Request $request, Book $book)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'published_year' => 'required|integer',
        'pages' => 'required|integer|min:1',
        'publisher_id' => 'required|exists:publishers,id',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
    ]);

    $book->update($validated);

    return response()->json($book);
}

public function destroy(Book $book)
{
    $book->delete();

    return response()->json([
        'message' => 'Livro excluído com sucesso.'
    ]);
}

}
