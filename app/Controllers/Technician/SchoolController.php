<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
use App\Models\School;
use App\Models\User;

class SchoolController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
        $schools = School::all();

        echo $this->view->render("technician/school/index", [
            "schools" => $schools,
        ]);
    }

    public function create(): void
    {
        echo $this->view->render("technician/school/create");
    }

    public function store(?array $data): void
    {
        $this->validateCsrfToken($data, "tecnico/escolas/cadastrar");

        $newSchool = new School();

        $errors = $newSchool->validate($data);

        if ($errors) {
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/escolas/cadastrar");
        }

        if ($newSchool->getSchoolByName($data["name"])) {
            Message::warning("Já existe uma school cadastrada com este nome.");
            redirect("/tecnico/escolas/cadastrar");
            return;
        }
        if ($newSchool->getSchoolByName($data["code"])) {
            Message::warning("Ja existe uma escola com esse código.");
            redirect("/tecnico/escolas/cadastrar");
            return;
        }

        try {
            $newSchool->fill([
                "name" => $data["name"],
                "code" => $data["code"],
                "address" => $data["address"],
            ]);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/escolas/cadastrar");
            return;
        }

        Message::success("Escola Cadastrada com sucesso!");
        redirect("/tecnico/escolas/cadastrar") . $newSchool->getId();;
    }

    public function edit(?array $data): void
    {
        $school = School::find($data["id"]);

        if (!$school) {
            Message::error("Essa Escola não existe");
            redirect("/tecnico/escolas");
            return;
        }

        echo $this->view->render("technician/school/edit", [
            "school" => $school
        ]);
    }

    public function update(?array $data): void
    {
        $this->validateCsrfToken($data, "tecnico/escolas/editar/" . $data["id"]);

        $school = School::find($data["id"]);

        if (!$school) {
            Message::error("Essa Escola não existe");
            redirect("/tecnico/escolas");
            return;
        }

        $errors = $school->validate($data);

        if ($errors) {
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/escolas/editar/" . $school->getId());
        }

        if($school->getSchoolByName($data["name"]) && $school->getid() !== (int)$data["id"]){

                Message::warning("Já existe uma school cadastrada com este nome.");
                redirect("/tecnico/escolas/editar/" . $school->getId());
                return;
        }

        if($school->findByCode($data[""]) && $school->getid() !== (int)$data["id"]){

            Message::warning("Já existe uma school cadastrada com este nome.");
            redirect("/tecnico/escolas/editar/" . $school->getId());
            return;
        }

        try {
            $school->fill([
                "name" => $data["name"],
                "code" => $data["code"] ?? null,
                "address" => $data["address"] ?? null,
            ]);

            $school->save();
        }catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/escolas/editar/" . $school->getId());
            return;
        }

        Message::success("Escola atualizada com sucesso!");
        redirect("/tecnico/escolas") . $school->getId();

    }

    public function destroy(?array $data): void
    {

    }
}