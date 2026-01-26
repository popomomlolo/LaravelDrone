<?php $__env->startSection('title', 'Bienvenue'); ?>

<?php $__env->startSection('content'); ?>
   <h1>PONG</h1>
   <h1><?php echo e($coucou); ?></h1>

   <ul>
  <?php $__currentLoopData = $bdd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <li><?php echo e($key); ?> : <?php echo e($value); ?></li>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/toto/monPremierProjet/resources/views/pong.blade.php ENDPATH**/ ?>