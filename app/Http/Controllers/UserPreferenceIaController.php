<?php

namespace App\Http\Controllers;

use App\Models\UserPreferenceIa;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPreferenceIaController extends Controller
{
    // Affiche la page avec les niveaux actuels
    public function index()
    {
        $preferences = UserPreferenceIa::getUserPreferences(Auth::user());

        return Inertia::render('IaSettings/Index', [
            'preferences' => $preferences,
        ]);
    }

    // Sauvegarde les niveaux quand on clique "Sauvegarder"
    public function update(Request $request)
    {

        $validated = $request->validate([
            'humour_level'   => 'required|integer|min:1|max:10',
            'sarcasm_level'  => 'required|integer|min:1|max:10',
            'pedagogy_level' => 'required|integer|min:1|max:10',
            'patience_level' => 'required|integer|min:1|max:10',
            'anger_level'    => 'required|integer|min:1|max:10',
            'web_plugin_enabled' => 'required|boolean',
        ]);

        UserPreferenceIa::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        $phrase = 'Paramètres de Manuel la Truelle sauvegardés avec succès !';
        if ($validated['anger_level'] > 7) {
            $phrase = 'Manuel la Truelle est en colère ! Paramètres sauvegardés, mais attention à ne pas le faire trop souvent !';
        }
        else if ($validated['humour_level'] > 7) {
            $phrase = 'Manuel la Truelle est de bonne humeur ! Paramètres sauvegardés, profitez-en pour lui demander une blague !';
        }
        else if ($validated['sarcasm_level'] > 7) {
            $phrase = 'Manuel la Truelle est sarcastique ! Paramètres sauvegardés, mais ne vous attendez pas à des réponses sérieuses !';
        }
        else if ($validated['pedagogy_level'] > 7) {
            $phrase = 'Manuel la Truelle est pédagogue ! Paramètres sauvegardés, il sera plus patient pour expliquer les choses !';
        }
        else if ($validated['patience_level'] > 7) {
            $phrase = 'Manuel la Truelle est très patient ! Paramètres sauvegardés, il prendra le temps de répondre à toutes vos questions !';
        }

        return redirect()->back()->with('success', $phrase);
    }
}
