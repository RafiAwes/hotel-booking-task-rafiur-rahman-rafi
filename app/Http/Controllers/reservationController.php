<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\room;
use App\Models\booking;
use App\Models\roomCategory;
use App\Models\availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class reservationController extends Controller
{
    public function index(Request $request){
        $categories = roomCategory::all();

        $availability = [];

        // Only run validation and calculate availability if the user submitted the form
        if ($request->has('check_in') && $request->has('check_out')) {
            $today = Carbon::today()->format('Y-m-d');

            $validated = $request->validate([
                'check_in'  => ['required', 'date', 'date_format:Y-m-d', "after_or_equal:$today"],
                'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
            ], [
                'check_in.after_or_equal' => 'Can not choose past date for check-in.',
                'check_out.after' => 'Check-out date must be after check-in date.',
            ]);

            $from = Carbon::parse($validated['check_in']);
            $to   = Carbon::parse($validated['check_out']);

            $bookingData = [
                'check_in_date'  => $from->toDateString(),
                'check_out_date' => $to->toDateString(),
            ];

            // Check room availability
            foreach ($categories as $category) {
                $room  = $this->getAvailableRoom($category->id, $from, $to);
                $price = $category->calculateTotalPrice($from, $to);

                $availability[] = [
                    'category'  => $category,
                    'available' => $room ? true : false,
                    'price'     => $price,
                ];
            }
        }
        else{
            $bookingData = [];
        }


    return view('reservation', [
        'categories'   => $categories,
        'bookingData'  => $bookingData,
        'availability' => $availability,
    ]);
}


    public function validateBooking(Request $request){

        $today = Carbon::today()->format('Y-m-d');
        return $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email',
            'phone'=>['required','regex:/^(?:\+?8801|01)[0-9]{9}$/','max:20'], // BD numbers: 01XXXXXXXXX or +8801XXXXXXXXX
            'check_in_date'=>['required','date','date_format:Y-m-d',"after_or_equal:$today"],
            'check_out_date'=>['required','date','date_format:Y-m-d','after:check_in_date'],
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
        Log::info('checkAvailability called', [
            'query' => $request->query(),
            'input' => $request->all()
        ]);
        // Validate only the search fields used by the form
        $today = Carbon::today()->format('Y-m-d');

        try {
            $validated = $request->validate([
                'check_in'  => ['required', 'date', 'date_format:Y-m-d', "after_or_equal:$today"],
                'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
            ], [
                'check_in.after_or_equal' => 'Can not choose past date for check-in.',
                'check_out.after' => 'Check-out date must be after check-in date.',
            ]);
            Log::info('checkAvailability validation passed', [
                'validated' => $validated,
                'today' => $today,
            ]);
        } catch (ValidationException $e) {
            Log::warning('checkAvailability validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'today' => $today,
            ]);
            throw $e;
        }

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
        Log::info('checkAvailability returning view', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'availability_count' => count($availability),
        ]);

        return view('reservation', [
            'availability' => $availability,
            'bookingData'  => $bookingData,
            'categories'   => $categories,
            'checkIn'      => $from->toDateString(),
            'checkOut'     => $to->toDateString(),
        ]);
    }

    public function confirmBooking(Request $request){
        Log::info('confirmBooking called', [
            'input' => $request->all()
        ]);

        try {
            $validated = $this->validateBooking($request);
            Log::info('confirmBooking validation passed', [
                'validated' => $validated,
            ]);
        } catch (ValidationException $e) {
            Log::warning('confirmBooking validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);
            throw $e;
        }

        $category = roomCategory::findOrFail($request->category_id);
        $from = Carbon::parse($validated['check_in_date']);
        $to = Carbon::parse($validated['check_out_date']);

        $room = $this->getAvailableRoom($category->id, $from, $to);

        if(!$room){
            Log::warning('confirmBooking no available room', [
                'category_id' => $category->id,
                'from' => $from->toDateString(),
                'to' => $to->toDateString()
            ]);
            return back()
                ->withErrors(['error'=>'No available rooms in the selected category for the chosen dates. Please select different dates or category.'])
                ->withInput();
        }

        $total = $category->calculateTotalPrice($from, $to);

        try {
            $booking = booking::create([
                'name'           => $validated['name'],
                'email'          => $validated['email'],
                'phone_number'   => $validated['phone'],
                'room_id'        => $room->id,
                'check_in_date'  => $from->toDateString(),
                'check_out_date' => $to->toDateString(),
                'total_price'    => $total,
            ]);
        } catch (QueryException $e) {
            Log::error('confirmBooking DB error', [
                'message'  => $e->getMessage(),
                'sql'      => method_exists($e, 'getSql') ? $e->getSql() : null,
                'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : null,
            ]);
            return back()
                ->withErrors(['error' => 'Could not complete booking at this time. Please review your inputs and try again.'])
                ->withInput();
        }

        // Keep explicit marking; Booking model also marks on created event
        availability::markAsBooked($room->id, $from, $to);

        $room->update(['status'=>'booked']);

        Log::info('confirmBooking created booking', [
            'booking_id' => $booking->id
        ]);

        return redirect()->route('booking.success', ['id' => $booking->id]);
    }

    public function bookingSuccess($id)
    {
        $booking = booking::with('room.category')->findOrFail($id);
        return view('thankyou', ['booking' => $booking]);
    }


}
