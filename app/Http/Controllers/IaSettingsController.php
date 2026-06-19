<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class IaSettingsController extends Controller
{
    // Affiche la page avec les niveaux actuels
    public function index()
    {
        return Inertia::render('IaSettings/Index', [
            'humour_level'   => session('humour_level', 5),
            'sarcasm_level'  => session('sarcasm_level', 5),
            'pedagogy_level' => session('pedagogy_level', 5),
            'patience_level' => session('patience_level', 5),
            'anger_level'    => session('anger_level', 5),
        ]);
    }

    // Sauvegarde les niveaux quand on clique "Sauvegarder"
    public function store(Request $request)
    {
        $validated = $request->validate([
            'humour_level'   => 'required|integer|min:1|max:10',
            'sarcasm_level'  => 'required|integer|min:1|max:10',
            'pedagogy_level' => 'required|integer|min:1|max:10',
            'patience_level' => 'required|integer|min:1|max:10',
            'anger_level'    => 'required|integer|min:1|max:10',
        ]);

        session($validated);

        return back();
    }
}
