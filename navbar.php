

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul class="nav navbar-nav navbar-right">

				<?php

				//On sélectionne les données pour afficher les catégories
				$categories = pg_query("SELECT categorie_id, categorie_name, categorie_slug FROM csf_categories ORDER BY categorie_id ASC");

				while($cat = pg_fetch_array($categories))
				{					
					$categories_enfant = pg_query("SELECT categorie_id, categorie_name, categorie_slug FROM csf_categories WHERE categorie_parent = '".$cat['categorie_id']."'");

					if(pg_num_rows($categories_enfant) > 0)
					{							  
						echo '<li class="dropdown">';

							echo '<a class="dropdown-toggle" data-toggle="dropdown" title="'.$cat['categorie_name'].
								 '" href="http://'.$_SERVER['HTTP_HOST'].'/'.$cat['categorie_slug'].'/">'.
								 $cat['categorie_name'].'<b class="caret"></b></a>';
							
							echo '<ul class="dropdown-menu">';
							
									while($subCat = pg_fetch_array($categories_enfant))
									{	
										echo '<li><a title="'.$subCat['categorie_name'].'" href="http://'.$_SERVER['HTTP_HOST'].
										     '/subcategory.php?q='.$subCat['categorie_slug'].'">'
											 .$subCat['categorie_name'].'</a></li>';
									}
													
							echo '</ul>';
							
						echo '</li>';							  
					}
					
					else
					{							  
						echo ' <li><a title="'.$cat['categorie_name'].'" href="http://'.$_SERVER['HTTP_HOST'].'/category.php?q='.$cat['categorie_slug'].'">'.
							 $cat['categorie_name'].'</a></li>';
					}
				}
			

				?>
				
			    <li><a href="about.html">About</a>
				</li>
				<li><a href="services.html">Services</a>
				</li>
				<li><a href="contact.php">Contact</a>
				</li>
				
			</ul>
		</div>
		<!-- /.navbar-collapse -->
