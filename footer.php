
<div class="container">

	<hr>

	<footer>
		<div class="row">
			<div class="col-lg-12">
			
				<p>Copyright &copy; Antiloop Software 2016</p>
				
				<p>
					<a rel="nofollow" title="Administration" href="http://<?php echo ROOT;?>/admin">Administration</a> | <a title="Flux Rss" href="http://<?php echo ROOT;?>/rss.php">Rss</a> |
					<a title="Sitemap XML" href="http://<?php echo ROOT;?>/sitemap.php">Sitemap</a>

					<?php
					
					if(isset($afficher_lien_formulaire_contacte) AND $afficher_lien_formulaire_contacte== 1)
					{
						echo ' | <a rel="nofollow" title="Formulaire de contact" href="http://'.ROOT.'/contact.php">Contact</a>';
					}
					
					?>
					
				</p>
				
			</div>
		</div>
	</footer>

</div>
<!-- /.container -->