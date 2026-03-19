@extends('layouts.base')
@section('title', 'Gestion des apprentis')

@section('content')
<form>
    <div class="row">    
        <div class="col-4">
            <div class="input-group">
                <div class="input-group-text">apprenti</div>
                <select name="apprenti" class="form-select" id="apprenti">
                    <option value="-1" selected>Sélectionnez un apprenti</option>
                </select>
            </div>
        </div>
    </div>
</form>
<script>
function genererListeApprentis() {
    // Vider les listes
    $("#apprenti").find('option').not(':first').remove();
    
    $.getJSON('/api/apprentis')
        .done(function(donnees) {
            $.each(donnees, function(index, ligne) {
                $("#apprenti").append($('<option>', {
                    value: ligne.apprenti_id
                }).text(ligne.apprenti_nom));
            });
        })
        .fail(function(xhr, text, error) {
            console.log("Erreur : " + error);
        });
}
$(document).ready(function() {
    genererListeApprentis();
});
</script>
@endsection