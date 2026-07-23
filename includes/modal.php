    <!-- Get a Quote Modal -->
    <div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="quoteModalLabel">Request a Free Quote</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="quoteForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name <span class="required-star">*</span></label>
                                <input type="text" class="form-control" id="formName" placeholder="John Doe" required>
                                <div class="invalid-feedback">Letters and spaces only, please.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address <span class="required-star">*</span></label>
                                <input type="email" class="form-control" id="formEmail" placeholder="john@example.com" required>
                                <div class="invalid-feedback">Invalid email address provide the test@sample.com.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone Number <span class="required-star">*</span></label>
                                <input type="tel" class="form-control" id="formPhone" placeholder="1234567890" maxlength="10" required>
                                <div class="invalid-feedback">Enter 10 digit phone number (numbers only).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Company Name</label>
                                <input type="text" class="form-control" id="formCompany" placeholder="Acme Corp">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Address</label>
                                <input type="text" class="form-control" id="formAddress" placeholder="123 Main St, City, Country">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Project Description <span class="required-star">*</span></label>
                                <textarea class="form-control" id="formDesc" rows="4" placeholder="How can we help you?" required></textarea>
                                <div class="invalid-feedback">Please provide a short description.</div>
                            </div>
                            <div class="col-12">
                                <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Popup Modal -->
    <style>
        #notifModal .modal-dialog {
            max-width: 800px;
            width: 90%;
            margin: 1.75rem auto;
        }
        #notifModal .modal-content {
            background-color: transparent;
            border: none;
            box-shadow: none;
        }
        #notifModal .modal-body {
            position: relative;
            padding: 0;
        }
        #notifModal .close-btn-wrapper {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1070;
        }
        #notifModal .btn-close-custom {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 0, 0, 0.9); /* Solid red with slight transparency */
            color: #ffffff;
            border-radius: 50%;
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 0;
            font-size: 24px;
            font-weight: bold;
            line-height: 1;
            text-decoration: none;
        }
        #notifModal .btn-close-custom:hover {
            background-color: #ff0000;
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 6px 20px rgba(0,0,0,0.6);
        }
        #notifModal img {
            width: 100%;
            height: auto;
            max-height: 85vh;
            display: block;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.7);
            border: 3px solid rgba(255,255,255,0.1);
        }
        @media (max-width: 576px) {
            #notifModal .close-btn-wrapper {
                top: 10px;
                right: 10px;
            }
            #notifModal .btn-close-custom {
                width: 35px;
                height: 35px;
                font-size: 20px;
            }
        }
    </style>

    <div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(240, 240, 240, 0.85);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="close-btn-wrapper">
                        <div class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" title="Close Notification">
                            &times;
                        </div>
                    </div>
                    <img id="notifImage" src="" alt="Notification">
                </div>
            </div>
        </div>
    </div>

    <!-- Service Detail Modal -->
    <div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-labelledby="serviceDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--border-radius); overflow: hidden;">
                <div class="modal-header bg-primary text-white border-0 position-relative pb-2">
                    <h5 class="modal-title fw-bold" id="serviceDetailModalLabel">Service Details</h5>
                    <button type="button" class="btn-close btn-close-white position-absolute" style="top: 20px; right: 20px;" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    <div class="text-center mb-3" id="serviceDetailImageWrapper">
                        <img id="serviceDetailImage" src="" alt="" class="img-fluid rounded" style="max-height: 250px; object-fit: cover; width: 100%; border-radius: var(--border-radius);">
                    </div>
                    <p id="serviceDetailDescription" class="text-muted" style="white-space: pre-line; line-height: 1.8; font-size: 0.95rem;"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Detail Modal -->
    <div class="modal fade" id="testimonialDetailModal" tabindex="-1" aria-labelledby="testimonialDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--border-radius); overflow: hidden;">
                <div class="modal-header border-0 pb-0 position-relative">
                    <h5 class="modal-title fw-bold pt-3 px-2 text-primary" id="testimonialDetailModalLabel">Client Feedback</h5>
                    <button type="button" class="btn-close position-absolute" style="top: 20px; right: 20px;" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <img id="testimonialDetailImage" src="" alt="" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid var(--primary-color);">
                        <div>
                            <h6 id="testimonialDetailName" class="mb-0 fw-bold text-dark">Client Name</h6>
                            <span id="testimonialDetailPosition" class="small text-muted">Client Position</span>
                        </div>
                    </div>
                    <div class="position-relative">
                        <i class="bi bi-quote text-primary fs-1 opacity-25 position-absolute" style="top: -20px; left: -10px; z-index: 0;"></i>
                        <p id="testimonialDetailContent" class="text-dark font-italic ps-4 position-relative" style="z-index: 1; font-style: italic; line-height: 1.8; font-size: 1.05rem;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
