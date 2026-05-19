<?php

namespace App\Controllers\Admin;

<<<<<<< HEAD
use App\Core\Controller;
use App\Core\Auth;
=======
use App\Core\Auth;
use App\Core\Controller;
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
use App\Core\Message;
use App\Core\Permission;
use App\Models\Role\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");
<<<<<<< HEAD

=======
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        Auth::requirePermission(Permission::VIEW_ROLES);
    }

    public function index(): void
    {
<<<<<<< HEAD
        $roles = Role::all();

        echo $this->view->render('admin/role/index', [
            "roles" => $roles
=======
        $roles = (new Role())->orderBy("name", "ASC")->get();

        echo $this->view->render("admin/role/index", [
            "roles" => $roles,
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        ]);

        clear_old();
    }

    public function create(): void
    {
        Auth::requirePermission(Permission::CREATE_ROLE);

<<<<<<< HEAD
        echo $this->view->render('admin/role/create');

=======
        echo $this->view->render("admin/role/create");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        clear_old();
    }

    public function store(?array $data): void
    {
        Auth::requirePermission(Permission::CREATE_ROLE);
<<<<<<< HEAD
=======

>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        $this->validateCsrfToken($data, "/admin/perfis/cadastrar");

        $newRole = new Role();

<<<<<<< HEAD
        $errors = $newRole->validate($data);

        if ($errors) {
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/admin/perfis/cadastrar");
        }

        try {
            $newRole->fill([
                "name" => $data["name"],
                "description" => $data["description"],
                "is_permissions" => $data["permissions"] = 0,
            ]);

            $newRole->save();
        }catch (\InvalidArgumentException $invalidArgumentException){
=======
        try {
            $newRole->fill([
                "name" => $data["name"],
                "description" => $data["description"] ?? null,
                "is_protected" => 0,
            ]);

            $errors = array_merge(
                $newRole->validate($data),
                $newRole->validateBusinessRule()
            );

            if ($errors) {
                flash_old($data);
                foreach ($errors as $error) {
                    Message::warning($error);
                }
                redirect("/admin/perfis/cadastrar");
                return;
            }

            $newRole->save();

        } catch (\InvalidArgumentException $invalidArgumentException) {
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/cadastrar");
            return;
        }

<<<<<<< HEAD
        Message::success("Perfil atualizado com sucesso!");
        redirect("/admin/perfis/editar/{id}") .  $newRole->getId();
=======
        Message::success("Perfil cadastrado com sucesso.");
        redirect("/admin/perfis/editar/" . $newRole->getId());
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
    }

    public function edit(?array $data): void
    {
        Auth::requirePermission(Permission::EDIT_ROLE);

<<<<<<< HEAD
        $role = Role::find($data['id']);

        if (!$role) {
            Message::warning("Esse perfil não existe!");
=======
        $role = Role::find((int)$data["id"]);

        if (!$role) {
            Message::warning("Perfil não encontrado ou não existe.");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            redirect("/admin/perfis");
            return;
        }

<<<<<<< HEAD
        echo $this->view->render('admin/role/edit', [
=======
        echo $this->view->render("admin/role/edit", [
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            "role" => $role,
        ]);

        clear_old();
    }

    public function update(?array $data): void
    {
        Auth::requirePermission(Permission::EDIT_ROLE);

<<<<<<< HEAD
        $this->validateCsrfToken($data, "/admin/perfis/editar/{id}");

        $role = Role::find($data['id']);

        if (!$role) {
            Message::warning("Esse perfil não existe!");
=======
        $this->validateCsrfToken($data, "/admin/perfis/editar/" . $data["id"]);

        $role = Role::find((int)$data["id"]);

        if (!$role) {
            Message::warning("Perfil não encontrado ou não existe.");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            redirect("/admin/perfis");
            return;
        }

<<<<<<< HEAD
        if ($role->isProtected()){
            Message::warning("Perfils protegidos não podem ser editados!");
=======
        if ($role->isProtected()) {
            Message::warning("Este perfil é protegido e não pode ser editado.");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            redirect("/admin/perfis");
            return;
        }

        try {
            $role->fill([
                "name" => $data["name"],
<<<<<<< HEAD
                "description" => $data["description"],
            ]);

            $erros = array_merge(
                $role->validate($data),
                $role->validateBusinessRule($role->getId()),
            );

            if ($erros) {
                foreach ($erros as $error) {
                    Message::warning($error);
                }

                redirect("/admin/perfis/editar/") .  $role->getId();
=======
                "description" => $data["description"] ?? null,
            ]);

            $errors = array_merge(
                $role->validate($data),
                $role->validateBusinessRule($role->getId())
            );

            if ($errors) {
                flash_old($data);
                foreach ($errors as $error) {
                    Message::warning($error);
                }
                redirect("/admin/perfis/editar/" . $role->getId());
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
                return;
            }

            $role->save();

<<<<<<< HEAD
            Message::success("Perfil atualizado com sucesso!");
            redirect("/admin/perfis/editar/") .  $role->getId();

        }catch (\InvalidArgumentException $invalidArgumentException){
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/editar/") .  $role->getId();
        }
=======
        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/editar/" . $role->getId());
            return;
        }

        Message::success("Perfil atualizado com sucesso.");
        redirect("/admin/perfis/editar/" . $role->getId());
    }

    public function destroy(?array $data): void
    {
        Auth::requirePermission(Permission::DELETE_ROLE);

        $this->validateCsrfToken($data, "/admin/perfis");

        $role = Role::find((int)$data["id"]);

        if (!$role) {
            Message::error("Perfil não encontrado ou não existe.");
            redirect("/admin/perfis");
            return;
        }

        if ($role->isProtected()) {
            Message::warning("Este perfil é protegido e não pode ser excluído.");
            redirect("/admin/perfis");
            return;
        }

        if ($role->existsUsers()) {
            Message::warning("Este perfil possui usuários vinculados e não pode ser excluído.");
            redirect("/admin/perfis");
            return;
        }

        try {
            $role->delete();
        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis");
            return;
        }

        Message::success("Perfil excluído em segurança com sucesso.");
        redirect("/admin/perfis");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
    }
}