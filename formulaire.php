
<div class="search">
<form class="form-haut" method="post" action="http://<?php echo $_SERVER['HTTP_HOST'];?>/recherche.php">
<input size="37" name="requete" value="Votre recherche..." onfocus="if (this.value == 'Votre recherche...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Votre recherche...';} "type="text"/>
<input class="submit-haut" value="" name="submit" type="submit"/>
</form>
</div>