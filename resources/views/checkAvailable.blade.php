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

                {{-- <div class="col-md-6 mb-3 mb-md-0 col-lg-3">
                  <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                      <label for="adults" class="font-weight-bold text-black">Adults</label>
                      <div class="field-icon-wrap">
                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                        <select name="adults" id="adults" class="form-control">
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4+</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0">
                      <label for="children" class="font-weight-bold text-black">Children</label>
                      <div class="field-icon-wrap">
                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                        <select name="children" id="children" class="form-control">
                          <option value="0">0</option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4+</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div> --}}

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
