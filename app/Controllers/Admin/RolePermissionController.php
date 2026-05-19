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

        Auth::requirePermission(Permission::MANAGE_ROLE_PERMISSIONS);
    }

    public function edit(?array $data): void
    {
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

        ]);

        clear_old();
    }

    public function update(?array $data): void
    {
        Auth::requirePermission(Permission::EDIT_ROLE);

        $this->validateCsrfToken($data, "/admin/perfis/permissions");

        $role = Role::find($data['id']);
        if (!$role) {
            Message::warning("Perfil não encontrado!");
            redirect("/admin/perfis");
            return;
        }

        if ($role->isProtected()){
            Message::warning("Perfils protegidos não podem ser editados!");
            redirect("/admin/perfis");
            return;
        }

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
    }
}