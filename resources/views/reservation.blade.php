@extends('master')

@section('content')
    @yield('checkAvailabilityForm')
    <section>
        <div class="container my-5">
            <h2 class="text-center mb-4">Available Rooms</h2>

            @if(isset($availability) && count($availability) > 0)
                <div class="row">
                    @foreach($availability as $item)
                        @php
                            $category = $item['category'];
                            $isAvailable = $item['available'];
                            $price = $item['price'];
                        @endphp

                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $category->name }}</h4>
                                    <p class="mb-2">Base Price: <strong>{{ number_format($category->base_price) }} BDT</strong></p>
                                    <p class="mb-2">Calculated Price:
                                        <strong>{{ number_format($price) }} BDT</strong>
                                    </p>

                                    @if($isAvailable)
                                        <form action="{{ route('booking.confirm') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                                            <input type="date" name="check_in_date" value="{{ $bookingData['check_in_date'] ?? '' }}">
                                            <input type="date" name="check_out_date" value="{{ $bookingData['check_out_date'] ?? '' }}">
                                            <input type="string" name="name" value="{{ $bookingData['name'] ?? '' }}">
                                            <input type="email" name="email" value="{{ $bookingData['email'] ?? '' }}">
                                            <input type="string" name="phone" value="{{ $bookingData['phone'] ?? '' }}">

                                            <button type="submit" class="btn btn-primary btn-block">
                                                Book This Room
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary btn-block" disabled>
                                            No Room Available
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning text-center">
                    No rooms available for the selected dates.
                </div>
            @endif
        </div>
    </section>
@endsection
