<form action="/login" method="POST">
    <?php echo csrf_field(); ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<?php if(session('error')): ?>
    <div style="color: red;"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('success')): ?>
    <div style="color: green;"><?php echo e(session('success')); ?></div>
<?php endif; ?><?php /**PATH /home/toto/monPremierProjet/resources/views/auth.blade.php ENDPATH**/ ?>