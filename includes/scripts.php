    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scroll Interactivity -->
    <script>
        // Reveal on Scroll Animation
        const reveals = document.querySelectorAll('.reveal');
        
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1 });

        reveals.forEach(el => revealObserver.observe(el));

        // Smooth Scroll for Nav Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Navbar Shadow on Scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
                nav.style.padding = '10px 0';
            } else {
                nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
                nav.style.padding = '15px 0';
            }
        });

        // Project Filtering Logic
        const filterBtns = document.querySelectorAll('.filter-btn');
        const projectItems = document.querySelectorAll('.project-item');
        const emptyState = document.querySelector('.empty-state');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.getAttribute('data-filter');
                let visibleCount = 0;

                projectItems.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.classList.remove('d-none');
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'scale(1)';
                        }, 50);
                        visibleCount++;
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            item.classList.add('d-none');
                        }, 300);
                    }
                });
                
                if (emptyState) {
                    if (visibleCount === 0) {
                        setTimeout(() => {
                            emptyState.classList.remove('d-none');
                        }, 300);
                    } else {
                        emptyState.classList.add('d-none');
                    }
                }
            });
        });

        projectItems.forEach(item => {
            item.style.transition = 'all 0.3s ease-out';
        });



        // Quote Form Validation
        const quoteForm = document.getElementById('quoteForm');
        const modalElement = document.getElementById('quoteModal');
        
        if (modalElement && quoteForm) {
            modalElement.addEventListener('show.bs.modal', () => {
                quoteForm.reset();
                const inputs = quoteForm.querySelectorAll('.is-invalid');
                inputs.forEach(input => input.classList.remove('is-invalid'));
                if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
            });

            quoteForm.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', () => {
                    input.classList.remove('is-invalid');
                });
            });

            const validateEmail = (input) => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim()) && input.value.trim() !== '') {
                    input.classList.add('is-invalid');
                    return false;
                }
                input.classList.remove('is-invalid');
                return true;
            };

            const validatePhone = (input) => {
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(input.value.trim()) && input.value.trim() !== '') {
                    input.classList.add('is-invalid');
                    return false;
                }
                input.classList.remove('is-invalid');
                return true;
            };

            document.getElementById('formEmail').addEventListener('blur', function() {
                validateEmail(this);
            });

            document.getElementById('formPhone').addEventListener('blur', function() {
                validatePhone(this);
            });

            quoteForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = document.getElementById('formName');
                const email = document.getElementById('formEmail');
                const phone = document.getElementById('formPhone');
                const desc = document.getElementById('formDesc');
                let isValid = true;

                const nameRegex = /^[A-Za-z\s]+$/;
                if (!nameRegex.test(name.value.trim())) {
                    name.classList.add('is-invalid');
                    isValid = false;
                } else {
                    name.classList.remove('is-invalid');
                }

                if (!validateEmail(email)) isValid = false;
                if (!validatePhone(phone)) isValid = false;

                if (desc.value.trim() === '') {
                    desc.classList.add('is-invalid');
                    isValid = false;
                } else {
                    desc.classList.remove('is-invalid');
                }

                const response = (typeof grecaptcha !== 'undefined') ? grecaptcha.getResponse() : 'dummy';
                if (response.length === 0) {
                    alert('Please complete the reCAPTCHA.');
                    isValid = false;
                }

                if (isValid) {
                    const formData = new FormData();
                    formData.append('name', name.value.trim());
                    formData.append('email', email.value.trim());
                    formData.append('phone', phone.value.trim());
                    formData.append('description', desc.value.trim());
                    formData.append('company', document.getElementById('formCompany').value.trim());
                    formData.append('address', document.getElementById('formAddress').value.trim());
                    formData.append('g-recaptcha-response', response);

                    fetch('process_quote.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            modal.hide();
                            quoteForm.reset();
                            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again later.');
                    });
                }
            });
        }

        // Notification Popup Logic
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($notifImage) && !empty($notifImage)): ?>
            const notifImage = '<?php echo $notifImage; ?>';
            const hasShownNotif = sessionStorage.getItem('hasShownNotif');
            
            if (!hasShownNotif && notifImage) {
                const notifModalElem = document.getElementById('notifModal');
                if (notifModalElem) {
                    const notifModal = new bootstrap.Modal(notifModalElem);
                    const modalImage = document.getElementById('notifImage');
                    if (modalImage) {
                        modalImage.src = notifImage;
                        notifModal.show();
                        sessionStorage.setItem('hasShownNotif', 'true');
                    }
                }
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>
