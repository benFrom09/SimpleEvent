<div class="form-group">
    <label for="name">Titre:</label>
    <input class="form-control" type="text" name="name" id="name" value="<?=isset($data['name']) ? $data['name'] : 'Mon super événement';?> "
        required>
</div>
<div class="form-group">
    <label for="date">Date:</label>
    <input type="date" name="date" id="date" class="form-control"
        value="<?=isset($data['date']) ? $data['date'] : date('Y-m-d');?>" required>
</div>
<div class="form-group">
    <label for="start_time">heure debut:</label>
    <input class="form-control" type="time" name="start_time" id="start_time" placeholder="HH:MM"
        value="<?=isset($data['start_time']) ? $data['start_time'] : '10:00';?>" required>
</div>
<div class="form-group">
    <label for="end_time">heure debut:</label>
    <input class="form-control" type="time" name="end_time" id="end_time" placeholder="HH:MM"
        value="<?=isset($data['end_time']) ? $data['end_time'] : '19:00';?>" required>
</div>
<div class="form-group">
    <label for="end_time">Description:</label>
    <textarea class="form-control" id="description" name="description" id="description" cols="30" rows="10">
                <?=isset($data['description']) ? $data['description'] :"Ceci est un texte par default, veuillez éditer les champs pour enregistrer votre évènement" ;?>
    </textarea>
</div>