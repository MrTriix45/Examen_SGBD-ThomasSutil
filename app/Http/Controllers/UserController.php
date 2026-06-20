<?php

namespace App\Http\Controllers;

use App\Services\SimpleAskService;
use App\Models\User;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private SimpleAskService $askService) {}
    
    public function index(Request $request)
    {
        $my_user = User::getUserDetails($request->user());
        return Inertia::render('user/Index', [
            'models' => $this->askService->getModels(),
            'my_user' => $my_user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Block access if the user tries to update another user's info
        if ($request->user()->id !== $user->id) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'user_info' => 'nullable|string',
            'preferred_model' => ['nullable', 'string', Rule::in(array_column($this->askService->getModels(), 'id'))],
        ]);

        $user->update($request->only('name', 'user_info', 'preferred_model'));

        return redirect('/user')->with('success', 'Woah, t\'as eu des nouvelles tartines, trop de la chance !');
    }
}
