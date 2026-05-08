<?php

namespace App\Controllers\Admin;


use App\Core\Controller;
use App\Core\Auth;
use App\Core\Permission;
use App\Models\Role;
use App\Models\Department;

class  DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requirePermission(Permission::VIEW_USERS);
    }

    public function index(): void
    {

    }
}