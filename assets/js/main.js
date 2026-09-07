document.addEventListener("DOMContentLoaded", function() {
    console.log("Kalp Interior Studio Main JS loaded.");
    
    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const header = item.querySelector('.faq-header');
        if (header) {
            header.addEventListener('click', () => {
                // Close other active items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.faq-body').style.display = 'none';
                        otherItem.querySelector('.faq-icon i').className = 'fa-solid fa-plus';
                    }
                });
                
                // Toggle current item
                item.classList.toggle('active');
                const body = item.querySelector('.faq-body');
                const icon = item.querySelector('.faq-icon i');
                
                if (item.classList.contains('active')) {
                    body.style.display = 'block';
                    icon.className = 'fa-solid fa-minus';
                } else {
                    body.style.display = 'none';
                    icon.className = 'fa-solid fa-plus';
                }
            });
        }
    });

    // Before/After Slider Logic (Multiple instances)
    const baContainers = document.querySelectorAll('.ba-slider-container');
    baContainers.forEach(container => {
        const baSlider = container.querySelector('.ba-slider-input');
        const beforeImage = container.querySelector('.ba-image-before');
        const sliderHandle = container.querySelector('.ba-slider-handle');
        
        if (baSlider && beforeImage && sliderHandle) {
            baSlider.addEventListener('input', function(e) {
                const sliderValue = e.target.value;
                // Update the width of the before image
                beforeImage.style.width = sliderValue + "%";
                // Move the custom handle position
                sliderHandle.style.left = sliderValue + "%";
            });
        }
    });
});
