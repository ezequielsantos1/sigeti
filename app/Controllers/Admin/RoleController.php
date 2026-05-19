<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Message;
use App\Core\Permission;
use App\Models\Role\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requirePermission(Permission::VIEW_ROLES);
    }

    public function index(): void
    {
        $roles = Role::all();

        echo $this->view->render('admin/role/index', [
            "roles" => $roles
        ]);

        clear_old();
    }

    public function create(): void
    {
        Auth::requirePermission(Permission::CREATE_ROLE);

        echo $this->view->render('admin/role/create');

        clear_old();
    }

    public function store(?array $data): void
    {
        Auth::requirePermission(Permission::CREATE_ROLE);
        $this->validateCsrfToken($data, "/admin/perfis/cadastrar");

        $newRole = new Role();

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
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/cadastrar");
            return;
        }

        Message::success("Perfil atualizado com sucesso!");
        redirect("/admin/perfis/editar/{id}") .  $newRole->getId();
    }

    public function edit(?array $data): void
    {
        Auth::requirePermission(Permission::EDIT_ROLE);

        $role = Role::find($data['id']);

        if (!$role) {
            Message::warning("Esse perfil não existe!");
            redirect("/admin/perfis");
            return;
        }

        echo $this->view->render('admin/role/edit', [
            "role" => $role,
        ]);

        clear_old();
    }

    public function update(?array $data): void
    {
        Auth::requirePermission(Permission::EDIT_ROLE);

        $this->validateCsrfToken($data, "/admin/perfis/editar/{id}");

        $role = Role::find($data['id']);

        if (!$role) {
            Message::warning("Esse perfil não existe!");
            redirect("/admin/perfis");
            return;
        }

        if ($role->isProtected()){
            Message::warning("Perfils protegidos não podem ser editados!");
            redirect("/admin/perfis");
            return;
        }

        try {
            $role->fill([
                "name" => $data["name"],
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
                return;
            }

            $role->save();

            Message::success("Perfil atualizado com sucesso!");
            redirect("/admin/perfis/editar/") .  $role->getId();

        }catch (\InvalidArgumentException $invalidArgumentException){
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/editar/") .  $role->getId();
        }
    }
}