<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
use App\Core\Permission;
use App\Models\Role\Permission as PermissionModel;
use App\Models\Role\Role;
use App\Models\Role\RolePermission;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");
<<<<<<< HEAD

=======
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        Auth::requirePermission(Permission::MANAGE_ROLE_PERMISSIONS);
    }

    public function edit(?array $data): void
    {
<<<<<<< HEAD
        Auth::requirePermission(Permission::EDIT_ROLE);

        $roles = Role::find($data['id']);

        if ($roles) {
            Message::warning("Esse perfil não existe!");
            redirect("/admin/perfils");
            return;
        }

        $permission = (new PermissionModel())->groupedByGroup();

        $currentPermissions = RolePermission::permissionIdsByRole($roles->getId());

        echo $this->view->render("admin/role/permissions", [
            "roles" => $roles,
            "permissions" => $permission,
            "currentPermissions" => $currentPermissions

=======
        $role = Role::find((int)$data["id"]);

        if (!$role) {
            Message::warning("Perfil não encontrado ou não existe.");
            redirect("/admin/perfis");
            return;
        }

        $permissions = (new PermissionModel())->groupedByGroup();
        $currentPermissions = RolePermission::permissionIdsByRole($role->getId());

        echo $this->view->render("admin/role/permissions", [
            "role" => $role,
            "permissions" => $permissions,
            "currentPermissions" => $currentPermissions,
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
        ]);

        clear_old();
    }

    public function update(?array $data): void
    {
<<<<<<< HEAD
        Auth::requirePermission(Permission::EDIT_ROLE);

        $this->validateCsrfToken($data, "/admin/perfis/permissions");

        $role = Role::find($data['id']);
        if (!$role) {
            Message::warning("Perfil não encontrado!");
=======
        $this->validateCsrfToken($data, "/admin/perfis/" . $data["id"] . "/permissoes");

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
            Message::warning("As permissões deste perfil são protegidas e não podem ser alteradas.");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
            redirect("/admin/perfis");
            return;
        }

<<<<<<< HEAD
        $permissionIds = array_map(
            'intval',
            $data['permissions'] ?? []
        );

        try {
            RolePermission::syncPermissions($role->getId(), $permissionIds);

            Message::success("Perfil atualizado com sucesso!");
            redirect("/admin/perfis/permissions");

        }catch (\InvalidArgumentException $invalidArgumentException){
            Message::warning($invalidArgumentException->getMessage());
            redirect("/admin/perfis/permissions");
            return;
        }
=======
        $permissionIds = array_map('intval', $data["permissions"] ?? []);

        try {
            RolePermission::syncPermissions($role->getId(), $permissionIds);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/admin/perfis/" . $role->getId() . "/permissoes");
            return;
        }

        Message::success("Permissões atualizadas com sucesso.");
        redirect("/admin/perfis/" . $role->getId() . "/permissoes");
>>>>>>> f267c2f53651f6cb844f9c2aac6de8b81675f0af
    }
}