@extends('master')

@section('checkAvailabilityForm')
    <section class="section bg-light pb-0" id="next">
      <div class="container">
        <div class="row check-availabilty">
          <div class="block-32" data-aos="fade-up" data-aos-offset="-200">
            <form action="{{ route('booking.checkAvailability') }}" method="GET">
              {{-- @csrf --}}
              <div class="row">
                <div class="col-md-6 mb-3 mb-lg-0 col-lg-3">
                  <label for="checkin_date" class="font-weight-bold text-black">Check In</label>
                  <div class="field-icon-wrap">
                    <div class="icon"><span class="icon-calendar"></span></div>
                    <input type="text" name="check_in" id="checkin_date" class="form-control datepicker" value="{{ $bookingData['check_in_date'] ?? '' }}" placeholder="Select date" required>
                  </div>
                </div>

                <div class="col-md-6 mb-3 mb-lg-0 col-lg-3">
                  <label for="checkout_date" class="font-weight-bold text-black">Check Out</label>
                  <div class="field-icon-wrap">
                    <div class="icon"><span class="icon-calendar"></span></div>
                    <input type="text" name="check_out" id="checkout_date" class="form-control datepicker" value="{{ $bookingData['check_out_date'] ?? '' }}" placeholder="Select date" required>
                  </div>
                </div>

                <div class="col-md-6 col-lg-3 align-self-end">
                  <button type="submit" class="btn btn-primary btn-block text-white">Check Availability</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
@endsection

@section('content')
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
                                        <form action="{{ route('booking.confirm') }}" method="POST" class="booking-form-card">
                                            @csrf
                                            <input type="hidden" name="category_id" value="{{ $category->id }}">

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="check_in_{{ $category->id }}" class="font-weight-bold text-black">Check In</label>
                                                    <div class="field-icon-wrap">
                                                        <div class="icon"><span class="icon-calendar"></span></div>
                                                        <input type="date" name="check_in_date" id="check_in_{{ $category->id }}" class="form-control" value="{{ $bookingData['check_in_date'] ?? '' }}" required>
                                                        {{-- <input type="date" name="check_in_date" id="check_in_{{ $category->id }}" class="form-control datepicker" value="{{ $bookingData['check_in_date'] ?? '' }}" placeholder="Select date" required> --}}
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="check_out_{{ $category->id }}" class="font-weight-bold text-black">Check Out</label>
                                                    <div class="field-icon-wrap">
                                                        <div class="icon"><span class="icon-calendar"></span></div>
                                                        <input type="date"
                                                               name="check_out_date"
                                                               id="check_out_{{ $category->id }}"
                                                               class="form-control"
                                                               value="{{ $bookingData['check_out_date'] ?? '' }}"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="name_{{ $category->id }}" class="font-weight-bold text-black">Full Name</label>
                                                    <div class="field-icon-wrap">
                                                        <div class="icon"><span class="icon-user"></span></div>
                                                        <input type="text"
                                                               name="name"
                                                               id="name_{{ $category->id }}"
                                                               class="form-control"
                                                               value="{{ $bookingData['name'] ?? '' }}"
                                                               placeholder="Enter your name"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="email_{{ $category->id }}" class="font-weight-bold text-black">Email</label>
                                                    <div class="field-icon-wrap">
                                                        <div class="icon"><span class="icon-envelope"></span></div>
                                                        <input type="email"
                                                               name="email"
                                                               id="email_{{ $category->id }}"
                                                               class="form-control"
                                                               value="{{ $bookingData['email'] ?? '' }}"
                                                               placeholder="your@email.com"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="phone_{{ $category->id }}" class="font-weight-bold text-black">Phone</label>
                                                    <div class="field-icon-wrap">
                                                        <div class="icon"><span class="icon-phone"></span></div>
                                                        <input type="tel"
                                                               name="phone"
                                                               id="phone_{{ $category->id }}"
                                                               class="form-control"
                                                               value="{{ $bookingData['phone'] ?? '' }}"
                                                               placeholder="+880 1XXX XXXXXX"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-primary btn-block text-white">
                                                        Book This Room
                                                    </button>
                                                </div>
                                            </div>
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

    <style>
        .booking-form-card {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .field-icon-wrap {
            position: relative;
        }

        .field-icon-wrap .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
        }

        .field-icon-wrap input {
            padding-left: 45px;
        }

        .booking-form-card label {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .booking-form-card .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            height: 45px;
        }

        .booking-form-card .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection

{{-- @section('content')
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
@endsection --}}
