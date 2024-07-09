<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $category = Category::count();
        $stall = Stall::count();
        $stock = Stock::count();
        $scrap  = Scrap::count();
        $book = Book::count();

        $monthlySales = SalesOrder::monthlySales()->get();
        $salesData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthSales = $monthlySales->firstWhere('month', $i);
            $salesData[] = $monthSales ? $monthSales->total_sales : 0;
        }

        return response()->json([$category, $stall, $stock, $scrap, $book, $salesData], 200);
    }
    public function calendar()
    {
        $user = Auth::user();
        $userid = Auth::user()->id;
        $currentYear = Carbon::now()->year;

        // Determine the query based on user role
        if ($user->role == 'admin') {
            // Admin can see all approved leaves
            $leavesQuery = Leave::where('status', 'approved');
        } else {
            $leavesQuery = Leave::where('status', 'approved')
                                ->where('user_id', $userid);
        }

        // Fetch and map leave events
        $leaveEvents = $leavesQuery->get(['startdate', 'enddate', 'employee_id', 'totalhours', 'status'])
            ->flatMap(function ($leave) {
                $events = [];
                if ($leave->employee) {
                    // Iterate over each leave
                    $leaveStartDate = date('Y-m-d', strtotime($leave->startdate));
                    $leaveEndDate = date('Y-m-d', strtotime($leave->enddate));

                    // Calculate the number of days the leave spans
                    $startDate = new \DateTime($leaveStartDate);
                    $endDate = new \DateTime($leaveEndDate);
                    $interval = $startDate->diff($endDate);
                    $leaveDays = $interval->days + 1; // Add 1 to include both start and end dates
                    // Add each day of the leave as a separate event
                    for ($i = 0; $i < $leaveDays; $i++) {
                        $events[] = [
                            'title' => $leave->employee->firstname . "'s Leave",
                            'start' => $startDate->format('Y-m-d'), // Use ISO 8601 date format
                            'allDay' => true, // Consider it as an all-day event
                            'color' => in_array($leave->totalhours, ['3', '4']) ? 'grey' : 'green', // Updated condition
                            'textColor' => 'white',
                        ];
                        // Move to the next day
                        $startDate->modify('+1 day');
                    }
                }
                return $events;
            })
            ->all();

        // Fetch all employee birthdays for the current year
        $birthdayEvents = Employee::all(['id', 'firstname', 'dob'])
            ->map(function ($employee) use ($currentYear) {
                if (isset($employee->dob)) {
                    $dob = Carbon::parse($employee->dob);
                    $birthdayDate = Carbon::createFromDate($currentYear, $dob->month, $dob->day);
                    return [
                        'title' => $employee->firstname . "'s Birthday",
                        'start' => $birthdayDate->toDateString(),
                        'allDay' => true,
                        'color' => '#FFD700',
                        'textColor' => 'black',
                    ];
                }
            })
            ->filter()
            ->all();

            // Fetch holidays
            $holidayEvents = Holiday::all(['name', 'date'])
            ->map(function ($holiday) {
                return [
                    'title' => $holiday->name,
                    'start' => $holiday->date,
                    'allDay' => true,
                    'color' => '#FF4500',
                    'textColor' => 'white',
                ];
            })
            ->all();
        // Merge and return all events
        $leaves = array_merge($leaveEvents, $birthdayEvents,$holidayEvents);
        return response()->json($leaves);
    }
}
