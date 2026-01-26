<?php $__env->startSection('title', 'Bienvenue'); ?>

<?php $__env->startSection('content'); ?>

<h1>Traitement des formulaires</h1>

<?php if(session('error')): ?>
<div style="color: red;"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('success')): ?>
<div style="color: green;"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/toto/monPremierProjet/resources/views/traitement.blade.php ENDPATH**/ ?>