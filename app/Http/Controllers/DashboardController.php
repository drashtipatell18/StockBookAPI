<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Category;
use App\Models\Stall;
use App\Models\Stock;
use App\Models\Scrap;
use App\Models\SalesOrder;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $category = Category::count();
        $stall = Stall::count();
        $stock = Stock::count();
        $scrap  = Scrap::count();
        $book = Book::count();

        $monthlySales = SalesOrder::get();
        $salesData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthSales = $monthlySales->firstWhere('month', $i);
            $salesData[] = $monthSales ? $monthSales->total_sales : 0;
        }
        return response()->json([
            'success' => true,
            'message' => 'Data successfully',
            'result' => ['category' => $category, 'stall' => $stall, 'stock' => $stock, 'scrap' => $scrap, 'book' => $book, 'salesData' => $salesData],
        ], 200);
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
        $leaves = array_merge($leaveEvents, $birthdayEvents, $holidayEvents);
        return response()->json([
            'success' => true,
            'message' => 'Employee Birthday, Holiday data successfully',
            'result' => $leaves
        ], 200);
        return response()->json($leaves);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->remember_token = Str::random(40);
            $user->save();

            Mail::to($user->email)->send(new ForgotPasswordMail($user));

            return response()->json(['success' => true, 'message' => 'Password reset link sent successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }
    }

    // Method to display the reset form
    public function reset($token)
    {
        $user = User::where('remember_token', $token)->first();
    
        if ($user) {
            $data = [
                'user' => $user,
                'token' => $user->remember_token
            ];
            return view('reset', $data);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid token.'], 400);
        }
    }
    
    // Method to handle the form submission
    public function postReset($token, Request $request)
    {
        // Debugging line to check request data
        // dd($request->all());

        $validateUser = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validateUser->errors()
            ], 401);
        }

        $user = User::where('remember_token', $token)->first();

        if ($user) {
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->remember_token = Str::random(40);
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['success' => true,'message' => 'Password successfully reset.'], 200);
        } else {
            return response()->json(['success' => false,'message' => 'Invalid token.'], 400);
        }
    }


    public function changePassword(Request $request)
    { 
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => 'false',
                    'message' => "Validation Fails",
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();


            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The old password does not match our records.'
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.'
            ], 200);
        
    }
}
