<?php $__env->startSection('title', 'uiiii'); ?>

<?php $__env->startSection('content'); ?>
   <h1><?php echo e($word); ?></h1>
   <h2>coucou</h2>
   <h1>PING</h1>
   <p>Uiiiiii</p>

<?php if($word == 'PING'): ?>
<p>La page est en mode PING (<?php echo e(time()); ?></p>
<?php else: ?>
<p>La page est en mode PONG (<?php echo e(time()); ?></p>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/toto/monPremierProjet/resources/views/ping.blade.php ENDPATH**/ ?>