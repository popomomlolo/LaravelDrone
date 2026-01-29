<?php $__env->startSection('title', 'Bienvenue'); ?>

<?php $__env->startSection('content'); ?>
    <table>
        <?php $__currentLoopData = $bdd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valeur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <a href="/todoSupp/<?php echo e($valeur->id); ?>">supp <?php echo e($valeur->id); ?></a>
                </td>

                <td>
                    <a href="/todoMaj/<?php echo e($valeur->id); ?>">maj <?php echo e($valeur->id); ?></a>
                </td>
                <td><?php echo e($valeur->texte); ?></td>
                <td><?php echo e($valeur->termine ? 'Terminé' : 'En cours'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    <form action="/traitement2" method="POST">
        <?php echo csrf_field(); ?>
        <input type="text" name="texte" placeholder="le texte de la bdd">
        <button name="ajouter" type="submit">Ajouter</button>
    </form>

    <?php if(session('error')): ?>
        <div style="color: red;"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(session('success')): ?>
        <div style="color: green;"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/toto/monPremierProjet/resources/views/todo.blade.php ENDPATH**/ ?>