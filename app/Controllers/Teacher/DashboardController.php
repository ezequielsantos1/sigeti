<?php

namespace App\Controllers\Teacher;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TEACHER);
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