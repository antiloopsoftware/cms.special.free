<?php

include 'config.php';

//connexion à la base de donnée

function db_open(){
   
    pg_connect(CONNECTION_STRING) or die ('Erreur : '.pg_last_error());
}

function db_close()
{
    pg_close();
}
