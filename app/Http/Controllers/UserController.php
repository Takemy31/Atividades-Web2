<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $data = $request->only('name', 'email');
        
        // Only admins can update the role
        if ($request->has('role') && auth()->user()->isAdmin()) {
            $this->authorize('updateRole', $user);
            $data['role'] = $request->input('role');
        }
        
        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function debits()
    {
    $users = User::where('debit', '>', 0)->get();

    return view('users.debits', compact('users'));
    }

    public function clearDebit(User $user)
    {
    $user->update([
        'debit' => 0
    ]);

    return redirect()
        ->route('users.debits')
        ->with('success', 'Débito quitado com sucesso.');
    }
    
}