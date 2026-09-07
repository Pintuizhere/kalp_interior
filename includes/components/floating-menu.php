<!-- Floating Action Menu -->
<div id="floating-menu-wrapper" class="floating-wrapper hidden">
    
    <!-- Expanded Menu Pill -->
    <div id="floating-pill" class="floating-pill">
        <a href="appointment.php" class="floating-icon" title="Book Appointment">
            <i class="fa-regular fa-calendar-check"></i>
        </a>
        <a href="contact.php" class="floating-icon" title="Location">
            <i class="fa-solid fa-location-dot"></i>
        </a>
        <a href="#" class="floating-icon" title="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://wa.me/919234772288" class="floating-icon whatsapp" title="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
        <a href="mailto:info@kalpinteriors.com" class="floating-icon" title="Email">
            <i class="fa-regular fa-envelope"></i>
        </a>
        <a href="tel:+919234772288" class="floating-icon" title="Call Us">
            <i class="fa-solid fa-phone"></i>
        </a>
        <button id="floating-close" class="floating-icon close-btn" title="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Main Pulse Trigger Button -->
    <button id="floating-trigger" class="floating-trigger">
        <i class="fa-solid fa-message"></i>
    </button>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.getElementById('floating-menu-wrapper');
        const trigger = document.getElementById('floating-trigger');
        const pill = document.getElementById('floating-pill');
        const closeBtn = document.getElementById('floating-close');

        // Show/Hide based on scroll depth (Past Hero section ~800px)
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                wrapper.classList.remove('hidden');
                wrapper.classList.add('visible');
            } else {
                wrapper.classList.remove('visible');
                wrapper.classList.add('hidden');
                // Auto close if scrolling back up
                pill.classList.remove('active');
                trigger.classList.remove('hidden-trigger');
            }
        });

        // Toggle Expand Menu
        trigger.addEventListener('click', () => {
            trigger.classList.add('hidden-trigger');
            pill.classList.add('active');
        });

        // Close Expand Menu
        closeBtn.addEventListener('click', () => {
            pill.classList.remove('active');
            trigger.classList.remove('hidden-trigger');
        });
    });
</script>
