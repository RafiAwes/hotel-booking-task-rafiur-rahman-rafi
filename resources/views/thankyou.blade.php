@extends('master')

@section('content')
    <section class="thank-you-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="thank-you-card text-center" data-aos="fade-up">
                        <!-- Success Icon -->
                        <div class="success-icon-wrapper">
                            <div class="success-icon">
                                <span class="checkmark">
                                    <div class="checkmark-circle"></div>
                                    <div class="checkmark-stem"></div>
                                    <div class="checkmark-kick"></div>
                                </span>
                            </div>
                        </div>

                        <!-- Thank You Message -->
                        <h1 class="thank-you-title">Thank You!</h1>
                        <h3 class="booking-confirmed">Your Booking is Confirmed</h3>

                        <p class="thank-you-text">
                            We've received your booking request and sent a confirmation email to
                            <strong>{{ $booking->email ?? 'your email address' }}</strong>
                        </p>

                        <!-- Booking Details Card -->
                        @if(isset($booking))
                        <div class="booking-details-card">
                            <h4 class="details-title">Booking Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="detail-item">
                                        <span class="icon-calendar detail-icon"></span>
                                        <div class="detail-content">
                                            <small class="detail-label">Check In</small>
                                            <p class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="detail-item">
                                        <span class="icon-calendar detail-icon"></span>
                                        <div class="detail-content">
                                            <small class="detail-label">Check Out</small>
                                            <p class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="detail-item">
                                        <span class="icon-user detail-icon"></span>
                                        <div class="detail-content">
                                            <small class="detail-label">Guest Name</small>
                                            <p class="detail-value">{{ $booking->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="detail-item">
                                        <span class="icon-home detail-icon"></span>
                                        <div class="detail-content">
                                            <small class="detail-label">Room Type</small>
                                            <p class="detail-value">{{ $booking->category->name ?? 'Standard Room' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-item total-price">
                                        <span class="icon-credit-card detail-icon"></span>
                                        <div class="detail-content">
                                            <small class="detail-label">Total Amount</small>
                                            <p class="detail-value price">{{ number_format($booking->total_price ?? 0) }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Booking Reference -->
                        @if(isset($booking->booking_reference))
                        <div class="booking-reference">
                            <small>Booking Reference</small>
                            <h5>#{{ $booking->booking_reference }}</h5>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="{{ route('booking.index') }}" class="btn btn-primary btn-lg">
                                <span class="icon-home"></span> Back to Home
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="additional-info">
                            <p class="info-text">
                                <span class="icon-info"></span>
                                Need to make changes? Contact us at
                                <a href="tel:+8801XXXXXXXXX">+880 1XXX XXXXXX</a> or
                                <a href="mailto:info@hotel.com">info@hotel.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .thank-you-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
        }

        .thank-you-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* Success Icon Animation */
        .success-icon-wrapper {
            margin-bottom: 30px;
        }

        .success-icon {
            display: inline-block;
        }

        .checkmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            stroke-width: 3;
            stroke: #4caf50;
            stroke-miterlimit: 10;
            box-shadow: inset 0 0 0 #4caf50;
            animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
            position: relative;
            margin: 0 auto;
        }

        .checkmark-circle {
            width: 100px;
            height: 100px;
            position: absolute;
            border-radius: 50%;
            border: 3px solid #4caf50;
            top: 0;
            left: 0;
        }

        .checkmark-stem {
            position: absolute;
            width: 3px;
            height: 50px;
            background-color: #4caf50;
            left: 48px;
            top: 48px;
            transform: rotate(45deg);
            transform-origin: top left;
            animation: stem 0.3s ease-in-out 0.6s forwards;
        }

        .checkmark-kick {
            position: absolute;
            width: 3px;
            height: 25px;
            background-color: #4caf50;
            left: 40px;
            top: 71px;
            transform: rotate(-45deg);
            transform-origin: top left;
            animation: kick 0.3s ease-in-out 0.7s forwards;
        }

        @keyframes stem {
            0% { height: 0; }
            100% { height: 50px; }
        }

        @keyframes kick {
            0% { height: 0; }
            100% { height: 25px; }
        }

        @keyframes scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        @keyframes fill {
            100% { box-shadow: inset 0 0 0 50px #4caf50; }
        }

        /* Typography */
        .thank-you-title {
            font-size: 3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .booking-confirmed {
            font-size: 1.5rem;
            color: #4caf50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .thank-you-text {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        /* Booking Details Card */
        .booking-details-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: left;
        }

        .details-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .detail-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .detail-icon {
            font-size: 24px;
            color: #667eea;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            color: #999;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .detail-value.price {
            font-size: 1.5rem;
            color: #4caf50;
        }

        .total-price {
            margin-top: 15px;
            border: 2px solid #4caf50;
        }

        /* Booking Reference */
        .booking-reference {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .booking-reference small {
            display: block;
            opacity: 0.9;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .booking-reference h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .action-buttons .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .action-buttons .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .action-buttons .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }

        .action-buttons .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        /* Additional Info */
        .additional-info {
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }

        .info-text {
            color: #666;
            font-size: 0.95rem;
            margin: 0;
        }

        .info-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .info-text a:hover {
            text-decoration: underline;
        }

        .info-text .icon-info {
            color: #667eea;
            margin-right: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .thank-you-card {
                padding: 40px 25px;
            }

            .thank-you-title {
                font-size: 2rem;
            }

            .booking-confirmed {
                font-size: 1.2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .detail-item {
                flex-direction: column;
                text-align: center;
            }

            .detail-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection
