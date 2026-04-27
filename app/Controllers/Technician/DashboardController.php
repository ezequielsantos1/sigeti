<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
        $ticketsModel = new Ticket();
        $tickets = (new Ticket())->ticketsOrderedByStatusPriorityAndOpeningDate();

        $quantityTicketsByMonth = $ticketsModel->countTicketsByMonth(2024);
        $quantityTicketsByCategory = $ticketsModel->countTicketsByCategory(2024);
        $quantityTicketsByStatus = $ticketsModel->countTicketsByStatus(2024);

        $avgResolutionDays = $ticketsModel->avgResolutionDaysByMonthCurrentYear(2024);
        $ticketsByPriorityAndStatus = $ticketsModel->countByPriorityAndStatusCurrentYear(2024);

        echo $this->view->render("technician/dashboard", [
            "tickets" => $tickets,
            "quantityTicketsByMonth" => $quantityTicketsByMonth,
            "quantityTicketsByCategory" => $quantityTicketsByCategory,
            "quantityTicketsByStatus" => $quantityTicketsByStatus,

            "avgResolutionDays" => $avgResolutionDays,
            "ticketsByPriorityAndStatus" => $ticketsByPriorityAndStatus
        ]);
    }
}