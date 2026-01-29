<h1>Sign Up</h1>

<form action="/signup" method="POST">
    @csrf
    <input type="text" name="name" placeholder="UserName" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="/signin">Login here</a></p>

@if (session('error'))
    <div style="color: red;">{{ session('error') }}</div>
@endif
@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif