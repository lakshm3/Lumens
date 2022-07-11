<?php

//ce script est à inclure partout.

if (strpos(__DIR__, "/var/www/studentzone")) //condition pour déterminer si on est en prod
    define("ENV", "prod");
else
    define("ENV", "local");


if (ENV == "local") {

    define("DB_USER", "sacha");
    define("DB_PASSWORD", "sacha");
    define("SHOW_ERROR", true);
}else{

    define("DB_USER", "sacha");
    define("DB_PASSWORD", "qdlkcoiazmjcsjccs"); // ac changer avec le vrai mdp
    define("SHOW_ERROR", false);
}

//a partir de là, les variables DB_USER, DB_PASSWORD, ... sont configuré selon l'env