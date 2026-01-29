<h1>Sign Up</h1>

<form action="/signup" method="POST">
    <?php echo csrf_field(); ?>
    <input type="text" name="name" placeholder="UserName" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="/signin">Login here</a></p>

<?php if(session('error')): ?>
    <div style="color: red;"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('success')): ?>
    <div style="color: green;"><?php echo e(session('success')); ?></div>
<?php endif; ?><?php /**PATH /home/toto/monPremierProjet/resources/views/signup.blade.php ENDPATH**/ ?>