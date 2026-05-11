<?php

namespace App\Controllers\Teacher;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permission;
use App\Models\Ticket\Ticket;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requirePermission(Permission::VIEW_REQUESTER_DASHBOARD);
    }

    public function index(): void
    {
        $ticketsModel = new Ticket();
        $userId = Auth::user()->id;

        $tickets = (new Ticket())->ticketsOrderedByStatusPriorityAndOpeningDateByUser(Auth::user()->id);

        $quantityTicketsByStatus = $ticketsModel->countTicketsByStatus($userId);
        $quantityTicketsByMonth = $ticketsModel->countTicketsByMonth($userId);
        $quantityTicketsByCategory = $ticketsModel->countTicketsByCategory($userId);

        echo $this->view->render("teacher/dashboard", [
            "tickets" => $tickets,
            "quantityTicketsByStatus" => $quantityTicketsByStatus,
            "quantityTicketsByMonth" => $quantityTicketsByMonth,
            "quantityTicketsByCategory" => $quantityTicketsByCategory,

        ]);
    }
}