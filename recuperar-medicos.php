<?php

use Luizlins\Projeto01\Modulos\Medico;

require_once "vendor/autoload.php";

$caminhoBanco = __DIR__ . "/banco.sqlite";
$pdo = new PDO("sqlite:$caminhoBanco");

$statement = $pdo->query("SELECT * FROM medicos;");

$listaMedicos = [];
 
var_dump($statement->fetchAll());
exit();

// foreach($statement->fetchAll() as $medico)
// {
//     $listaMedicos[] = new Medico(
//         $medico["id"],
//         $medico["crm"],
//         $medico["nome"],
//         $medico["especialidade"]
//     );
// }

var_dump($listaMedicos);