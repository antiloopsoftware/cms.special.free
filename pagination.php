<?php

function pagination($current_page, $nb_pages, $param = FALSE, $link='?article=%d', $around=6, $firstlast=1) {
    $pagination = '';
    $link = preg_replace('`%([^d])`', '%%$1', $link);
    if (!preg_match('`(?<!%)%d`', $link))
    $link .= '%d';
	
    if ($nb_pages > 1) {
        // Lien précédent
        if ($current_page > 1)
		
        if(!isset($param))
			$pagination .= '<a class="prevnext" href="'.sprintf($link, $current_page-1).'" title="Page précédente">&laquo; Précédent</a>';
        
		else
			$pagination .= '<a class="prevnext" href="'.sprintf($link, $current_page-1). $param .'" title="Page précédente">&laquo; Précédent</a>';
       
		else
        $pagination .= '<span class="prevnext disabled">&laquo; Précédent</span>';
 
        // Lien(s) début
        for ( $i=1 ; $i<=$firstlast ; $i++ ) {
           $pagination .= ' ';
           
		   if(!isset($param))
				$pagination .= ($current_page==$i) ? '<span class="current">'.$i.'</span>' : '<a title="Page '.$i.'" href="'.sprintf($link, $i).'">'.$i.'</a>';
           
		   else 
				$pagination .= ($current_page==$i) ? '<span class="current">'.$i.'</span>' : '<a title="Page '.$i.'" href="'.sprintf($link, $i). $param .'">'.$i.'</a>';
        }
 
        // ... après pages début ?
        if (($current_page-$around) > $firstlast+1)
        $pagination .= ' &hellip;';
 
        // On boucle autour de la page courante
        $start = ($current_page-$around)>$firstlast ? $current_page-$around : $firstlast+1;
        $end = ($current_page+$around)<=($nb_pages-$firstlast) ? $current_page+$around : $nb_pages-$firstlast;
        
		for ( $i=$start ; $i<=$end ; $i++ ) {
            $pagination .= ' ';
            
			if ( $i==$current_page )
				$pagination .= '<span class="current">'.$i.'</span>';
            
			else
				if(!isset($param))
					$pagination .= '<a title="Page '.$i.'" href="'.sprintf($link, $i).'">'.$i.'</a>';
				else
					$pagination .= '<a title="Page '.$i.'" href="'.sprintf($link, $i). $param .'">'.$i.'</a>';
        }
 
        // ... avant page nb_pages ?
        if ( ($current_page+$around) < $nb_pages-$firstlast )
        $pagination .= ' &hellip;';
 
        // Lien(s) fin
        $start = $nb_pages - $firstlast+1;
        if($start <= $firstlast) 
        $start = $firstlast+1;
        for ( $i = $start ; $i <= $nb_pages ; $i++ ) {
            $pagination .= ' ';
           
		   if(!isset($param)) 
				$pagination .= ($current_page==$i) ? '<span class="current">'.$i.'</span>' : '<a title="Page '.$i.'" href="'.sprintf($link, $i).'">'.$i.'</a>';
            
			else 
				$pagination .= ($current_page==$i) ? '<span class="current">'.$i.'</span>' : '<a title="Page '.$i.'" href="'.sprintf($link, $i). $param .'">'.$i.'</a>';
        }
 
        // Lien suivant
        if ($current_page < $nb_pages) {
            if(!isset($param)) 
				$pagination .= ' <a class="prevnext" href="'.sprintf($link, ($current_page+1)).'" title="Page suivante">Suivant &raquo;</a>';
           
		   else
				$pagination .= ' <a class="prevnext" href="'.sprintf($link, ($current_page+1)).$param.'" title="Page suivante">Suivant &raquo;</a>';
        } 
		
		else {
            $pagination .= ' <span class="prevnext disabled">Suivant &raquo;</span>';
        }
    }
	
    return $pagination;
}
 
//on affiche la pagination
echo '<p class="pagination">' . pagination($pageActuelle, $nombreDePages) . '</p>';
?>