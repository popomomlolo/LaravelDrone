<h1>Sign In</h1>

<form action="/signin" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign In</button>
</form>

<p>Don't have an account? <a href="/signup">Sign up here</a></p>

@if (session('error'))
    <div style="color: red;">{{ session('error') }}</div>
@endif
@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif