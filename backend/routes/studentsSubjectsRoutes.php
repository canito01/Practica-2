<?php
require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsSubjectsController.php");

routeRequest($conn);  //server.php llama a este archivo o alguno de los otros de la carpetas routes, luego se invoca a la funcion routeRequest del routesFactory.php
?>