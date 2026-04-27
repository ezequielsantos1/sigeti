<?php

require __DIR__ . "/vendor/autoload.php";

use App\Core\Email;
use App\Core\Session;
use App\Models\School;
use App\Models\Category;

new Session();

require __DIR__ . "/routes/web.php";

$template = file_get_contents(__DIR__ . "/app/Views/Email/forgot-password.php");

$messageHtml = str_replace([
    '{{NOME_USUARIO}}',
    '{{LINK_RESET}}',
    '{{EXPIRACAO_HORAS}}',
    '{{ANO}}',
], [
    "Pedro Leandro",
    url('/redefinir-senha/' . 1),
    '2',
    date('Y'),
], $template);

//try {
//
//    $email = new \App\Core\Email();
//
//    $email->bootstrap(
//      "Redefinição de Senha",
//        $messageHtml,
//      "franciscokawsio@gmail.com",
//      "Francisco Kawsio",
//    );
//
//    $email->send();
//
//    echo "E-mail enviado com sucesso!";
//
//}catch (\InvalidArgumentException $exception){
//    var_dump($exception->getMessage());
//}




//try {
//
////    $school = \App\Models\School::find(1);
////    var_dump($school->getName());
//    //Capturar todos os registros
////    $schools = \App\Models\School::all();
////
////    foreach ($schools as $school) {
////        var_dump($school->getAttributes());
////    }
//
////    $schoolModel = new \App\Models\School();
////
////    $school = (new \App\Models\School())->where("code",'=', "UIM00001")->get();
////    var_dump($school);
////
////    $schools = $schoolModel->where('name', 'LIKE', '%José')->get();
////    if ($schools) {
////        var_dump($schools);
////    }else{
////        echo "Sua busca não retornou nenhum registro!";
////    }
//
////    $newSchool = new School();
////    $newSchool->fill([
////        "name" => "U.I.M Escola Francisco Kassio",
////        "code" => "12345678",
////        "address" => "Rua das Andorinhas, SN, Bairro Trindade, Caxias - Ma"
////    ]);
////
////    $success = $newSchool->save();
////
////    if ($success) {
////        echo "Escola cadastrada com sucesso!";
////    }
//
////    $school = School::find(50);
////    $school->fill([
////        "name" => "U.I.M Escola Sao Francisco Kassio"
////    ]);
////
////    $success = $school->save();
////
////    if ($success) {
////        var_dump("Escola atualizada com sucesso");
////    }
//
//
//    //Categorias =>
////    $categories = \App\Models\Category::find(1);
////    var_dump($categories);
////    $categories = \App\Models\Category::all();
////
////    foreach ($categories as $category) {
////       var_dump($category->getAttributes());
////    }
////
////    $newCategory = new Category();
////    $newCategory->fill([
////        "name" => "Maquinas",
////        "description" => "Defeitos ou mau funcionamento em computadores, impressoras, projetores e periféricos"
////    ]);
////
////    $success = $newCategory->save();
////
////    if ($success) {
////        echo "Categoria cadastrada com sucesso!";
////   }
//
//    //Usuarios=>
//    $users = \App\Models\User::all();
//
//    foreach ($users as $user) {
//        var_dump($user->getAttributes());
//    }
//
//
//
//
//}catch (\InvalidArgumentException $exception){
//    var_dump("Erro:" .$exception->getMessage());
//} catch (PDOException $pdoException){
//    var_dump("Erro no Banco de Dados" .$pdoException->getMessage());
//}