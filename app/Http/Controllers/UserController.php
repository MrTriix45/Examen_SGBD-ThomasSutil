<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $my_user = User::getUserDetails($request->user());
        return Inertia::render('user/Index', [
            'my_user' => $my_user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($request->user()->id !== $user->id) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'user_info' => 'nullable|string',
        ]);

        $user->update($request->only('name', 'user_info'));

        return redirect('/user')->with('success', 'Woah, t\'as eu des nouvelles tartines, trop de la chance !');
    }
}
