<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
use App\Models\Category;
use App\Models\User;

class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
        $categories = Category::all();

        echo $this->view->render("technician/category/index", [
            "categories" => $categories
        ]);
    }

    public function create(): void
    {
        echo $this->view->render("technician/category/create");
    }

    public function store(?array $data): void
    {
        //1. Validação do CSRFTOKEN
        $this->validateCsrfToken($data, "tecnico/categorias/cadastrar");

        $newCategory = new Category();

        //2. Validação DOS CAMPOS
        $errors = $newCategory->validate($data);

        if ($errors) {
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/categorias/cadastrar");
        }

        //3. VERIFICAR SE NOME DESSA CATEGORIA JA EXISTE
        if ($newCategory->getCategoryByName($data["name"])) {
            Message::warning("Já existe uma categoria com esse nome.");
            redirect("/tecnico/categorias/cadastrar");
            return;
        }

        //4. SALVAR CATEGORIA
        try {
            $newCategory->fill([
                "name" => $data["name"],
                "description" => $data["description"] ?? null,
            ]);

            $newCategory->save();
        }catch (\InvalidArgumentException $invalidArgumentException){
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/categorias/cadastrar");
            return;
        }

        Message::success("Categoria cadastrada com sucesso!");
        redirect("/tecnico/categorias/editar/" . $newCategory->getId());
    }

    public function edit(?array $data): void
    {
        $category = Category::find($data["id"]);

        if (!$category) {
            Message::error("Essa categoria não existe");
            redirect("/tecnico/categorias");
            return;
        }

        echo $this->view->render("technician/category/edit", [
            "category" => $category
        ]);
    }

    public function update(?array $data): void
    {
        $this->validateCsrfToken($data, "tecnico/categorias/editar/" . $data["id"]);

        $category = Category::find($data["id"]);

        if (!$category) {
            Message::error("Essa categoria não existe");
            redirect("/tecnico/categorias");
            return;
        }

        $errors = $category->validate($data);

        if ($errors) {
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/categorias/editar/" . $category->getId());
        }

        try {
            $category->fill([
                "name" => $data["name"],
                "description" => $data["description"] ?? null,
            ]);

            $category->save();
        }catch (\InvalidArgumentException $invalidArgumentException){
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/categorias/editar/" . $category->getId());
            return;
        }

        Message::success("Categoria atualizada com sucesso!");
        redirect("/tecnico/categorias/editar/" . $category->getId());
    }

    public function destroy(?array $data): void
    {

    }
}