<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Apprenti;
use App\Models\Classes;
use App\Models\Formateurs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ApprentisControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuration initiale pour chaque test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Créer un formateur pour l'authentification
        $this->formateur = Formateurs::create([
            'login' => 'testuser',
            'mot_de_passe' => bcrypt('password'),
            'nom' => 'Test',
            'prenom' => 'User'
        ]);

        // Créer une classe de test
        $this->classe = Classes::create([
            'libelle_classe' => 'BTS Test'
        ]);

        // Créer un apprenti de test
        $this->apprenti = Apprenti::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'id_classe' => $this->classe->id_classe
        ]);
    }

    /**
     * Test de suppression d'un apprenti valide
     *
     * @return void
     */
    public function test_destroy_apprenti_valide_retourne_success()
    {
        // S'authentifier en tant que formateur
        $this->actingAs($this->formateur, 'web');

        // Envoyer une requête POST pour supprimer l'apprenti
        $response = $this->post('/apprentis/supprimer', [
            'apprenti_id' => $this->apprenti->id_apprenti
        ]);

        // Vérifier la réponse
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        // Vérifier que l'apprenti a été supprimé de la base de données
        $this->assertDatabaseMissing('apprentis', [
            'id_apprenti' => $this->apprenti->id_apprenti
        ]);
    }

    /**
     * Test de suppression d'un apprenti inexistant
     *
     * @return void
     */
    public function test_destroy_apprenti_inexistant_retourne_404()
    {
        // S'authentifier en tant que formateur
        $this->actingAs($this->formateur, 'web');

        // Envoyer une requête POST avec un ID inexistant
        $response = $this->post('/apprentis/supprimer', [
            'apprenti_id' => 99999
        ]);

        // Vérifier la réponse
        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Apprenti introuvable'
                 ]);
    }

    /**
     * Test de suppression sans authentification
     *
     * @return void
     */
    public function test_destroy_sans_authentification_redirige()
    {
        // Envoyer une requête POST sans authentification
        $response = $this->post('/apprentis/supprimer', [
            'apprenti_id' => $this->apprenti->id_apprenti
        ]);

        // Vérifier la redirection vers la page de connexion
        $response->assertRedirect('/signin');
    }

    /**
     * Test de suppression d'un apprenti avec sessions associées
     *
     * @return void
     */
    public function test_destroy_apprenti_avec_sessions_supprime_tout()
    {
        // S'authentifier
        $this->actingAs($this->formateur, 'web');

        // Créer une méteo pour la session
        $meteoId = \DB::table('conditions_meteo')->insertGetId([
            'jour' => 1,
            'ciel' => 0,
            'vent_x' => 0,
            'vent_y' => 0,
            'vent_z' => 0,
            'vent_norme' => 0
        ]);

        // Créer une session associée à l'apprenti
        \DB::table('sessions_drone')->insert([
            'date_heure' => now(),
            'type_environnement' => 1,
            'type_drone' => 1,
            'duree_max' => 30,
            'id_meteo' => $meteoId,
            'id_formateur' => $this->formateur->id_formateur,
            'id_apprenti' => $this->apprenti->id_apprenti
        ]);

        // Vérifier que la session existe
        $this->assertDatabaseHas('sessions_drone', [
            'id_apprenti' => $this->apprenti->id_apprenti
        ]);

        // Supprimer l'apprenti
        $response = $this->post('/apprentis/supprimer', [
            'apprenti_id' => $this->apprenti->id_apprenti
        ]);

        // Vérifier la réponse
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        // Vérifier que l'apprenti et ses sessions ont été supprimés
        $this->assertDatabaseMissing('apprentis', [
            'id_apprenti' => $this->apprenti->id_apprenti
        ]);

        $this->assertDatabaseMissing('sessions_drone', [
            'id_apprenti' => $this->apprenti->id_apprenti
        ]);
    }
}
