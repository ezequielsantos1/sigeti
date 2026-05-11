<?php

namespace App\Controllers\Admin;


use App\Core\Controller;
use App\Core\Auth;
use App\Core\Permission;
use App\Models\Department\Department;
use App\Models\Role;
use App\Models\Ticket\Ticket;
use App\Models\User;

class  DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requirePermission(Permission::VIEW_MANAGER_DASHBOARD);
    }

    public function index(): void
    {
        $totalUsers = (new User())->totalUsers();
        $totalRoles = (new Role\Role())->totalRoles();
        $totalDepartments = (new Department())->totalDepartments();
        $totalOpenTickets = (new Ticket())->totalOpenTickets();
        $recentUsers = (new User())->recentUsers();
        $roles = (new Role\Role())->recentRoles();


        echo $this->view->render('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalRoles' => $totalRoles,
            'totalDepartments' => $totalDepartments,
            'totalOpenTickets' => $totalOpenTickets,
            'recentUsers' => $recentUsers,
            'roles' => $roles
        ]);
    }
}