<h1>Connexion</h1>

<form action="/validerconnexion" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Se connecter</button>
</form>

<p>Vous n'avez pas de compte ? <a href="/inscription">Inscrivez-vous ici</a></p>

@if (session('error'))
    <div style="color: red;">{{ session('error') }}</div>
@endif
@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif 