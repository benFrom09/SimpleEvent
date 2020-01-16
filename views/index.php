<?php if(isset($_GET['sucess'])):?>
    <div class="alert alert-success">
        les données ont bien été enregistré
    </div>
<?php endif; ?>
<div class="container-fluid">
<?php    
$calendar->show();    
?>
<a class="calendar-btn calendar-btn-add" href="index.php?p=add">+</a>
</div>