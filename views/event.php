<h1><?= $event->getName();?></h1>
<p><?= $event->getDescription();?></p>
<p>Début de l'évènement: <?= $event->getStartTime()->format('H:i');?></p>
<p>Fin de l'évènement: <?= $event->getEndTime()->format('H:i');?></p>
