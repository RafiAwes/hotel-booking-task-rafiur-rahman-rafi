<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\room;
use App\Models\booking;
use App\Models\roomCategory;
use App\Models\availability;
use Illuminate\Http\Request;

class reservationController extends Controller
{
    public function index(){
        $categories = roomCategory::all();
        $checkIn = Carbon::today()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->format('Y-m-d');
        return view('checkAvailable', [
            'categories' => $categories,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
        ]);
    }

    public function validateBooking(Request $request){

        $today = Carbon::today()->format('Y-m-d');
        return $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email',
            'phone'=>['required','regex:/^01[0-9]{9}+$/','max:20'], // regex for BD numbers
            'check_in_date'=>['required','date',"after_or_equal:$today"],
            'check_out_date'=>['required','date','after:check_in_date'],
            'category_id'=>'required|exists:room_categories,id',
        ],
        [
            'check_in_date.after_or_equal'=> 'Can not choose past date for check-in.',
            'check_out_date.after'=> 'Check-out date must be after check-in date.',
        ]);
    }

    private function getAvailableRoom($categoryId, $checkIn, $checkOut){
        $rooms = room::where('room_category_id', $categoryId)->get();

        foreach($rooms as $room){
            $isAvailable = availability::isAvailableForRange($room->id, $checkIn, $checkOut);
            if($isAvailable){
                return $room;
            }
        }
        return null;
    }

    public function getFullyBookedDates(){
        $dates = [];
        $allRooms = room::count();

        $start = Carbon::today();
        $end = Carbon::today()->addDays(30);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $availableRooms = availability::whereDate('date', $date)
                ->where('is_available', true)
                ->count();

            if ($availableRooms === 0) {
                $dates[] = $date->toDateString();
            }
        }

        return response()->json($dates);
    }

    public function checkAvailability(Request $request){
        // Validate only the search fields used by the form
        $today = Carbon::today()->format('Y-m-d');
        $validated = $request->validate([
            'check_in'  => ['required', 'date', "after_or_equal:$today"],
            'check_out' => ['required', 'date', 'after:check_in'],
        ], [
            'check_in.after_or_equal' => 'Can not choose past date for check-in.',
            'check_out.after' => 'Check-out date must be after check-in date.',
        ]);

        $from = Carbon::parse($validated['check_in']);
        $to   = Carbon::parse($validated['check_out']);

        $categories   = roomCategory::all();
        $availability = [];
        foreach ($categories as $category) {
            $room  = $this->getAvailableRoom($category->id, $from, $to);
            $price = $category->calculateTotalPrice($from, $to);

            $availability[] = [
                'category'  => $category,
                'available' => $room ? true : false,
                'price'     => $price,
            ];
        }

        // Map to keys expected by reservation.blade.php hidden/inputs
        $bookingData = [
            'check_in_date'  => $from->toDateString(),
            'check_out_date' => $to->toDateString(),
        ];

        return view('reservation', [
            'availability' => $availability,
            'bookingData'  => $bookingData,
            'categories'   => $categories,
            'checkIn'      => $from->toDateString(),
            'checkOut'     => $to->toDateString(),
        ]);
    }

    public function confirmBooking(Request $request){
        $validated = $this->validateBookingInput($request);

        $category = roomCategory::findOrFail($request->category_id);
        $from = Carbon::parse($validated['check_in_date']);
        $to = Carbon::parse($validated['check_out_date']);

        $room = $this->getAvailableRoom($category->id, $from, $to);

        if(!$room){
            return back()->withErrors(['error'=>'No available rooms in the selected category for the chosen dates. Please select different dates or category.']);
        }

        $total = $category->calculateTotalPrice($from, $to);

        $booking = booking::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'phone'=>$validated['phone'],
            'room_id'=>$room->id,
            'check_in_date'=>$from->toDateString(),
            'check_out_date'=>$to->toDateString(),
            'total_price'=>$total,
        ]);

        availability::markAsBooked($room->id, $from, $to);

        $room->update(['status'=>'booked']);

        return redirect()->route('booking.success')->with('id', $booking->id);
    }

    public function bookingSuccess($id)
    {
        $booking = booking::with('room.category')->findOrFail($id);
        return view('thankyou', ['booking' => $booking]);
    }


}
