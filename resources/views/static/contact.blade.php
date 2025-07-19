@extends('layouts.app')

@section('title', 'Contact Us - UNZA Carbon Calculator')

@section('content')
<div class="contact-container py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bold mb-3">Contact Us</h1>
                <p class="lead text-muted">Get in touch with the UNZA Carbon Calculator team</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Send Us a Message</h2>
                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="" selected disabled>Select a subject</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Technical Support">Technical Support</option>
                                    <option value="Data Questions">Data Questions</option>
                                    <option value="Partnership">Partnership Opportunities</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Our Location</h2>
                        <div class="mb-4">
                            <h5 class="fw-bold"><i class="fas fa-university me-2 text-primary"></i> University of Zambia</h5>
                            <p class="mb-1">Great East Road Campus</p>
                            <p class="mb-1">School of Engineering</p>
                            <p>Lusaka, Zambia</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold"><i class="fas fa-envelope me-2 text-primary"></i> Email</h5>
                            <p class="mb-1">General Inquiries: <a href="mailto:info@climateyanga.com">info@climateyanga.com</a></p>
                            <p>Technical Support: <a href="mailto:support@climateyanga.com">support@climateyanga.com</a></p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold"><i class="fas fa-phone me-2 text-primary"></i> Phone</h5>
                            <p class="mb-1">Main Office: +260 211 123 4567111</p>
                            <p>Mobile: +260 973 021118</p>
                        </div>

                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">Follow Us</h5>
                            <div class="d-flex gap-3">
                                <a href="#" class="social-icon text-primary"><i class="fab fa-facebook-f fa-2x"></i></a>
                                <a href="#" class="social-icon text-info"><i class="fab fa-twitter fa-2x"></i></a>
                                <a href="#" class="social-icon text-danger"><i class="fab fa-instagram fa-2x"></i></a>
                                <a href="#" class="social-icon text-primary"><i class="fab fa-linkedin-in fa-2x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .contact-container {
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .social-icon {
        transition: transform 0.3s ease;
    }
    .social-icon:hover {
        transform: translateY(-3px);
    }
</style>
@endsection

@section('scripts')
<script>
    // Simple form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address');
            }
        });
    });
</script>
@endsection
