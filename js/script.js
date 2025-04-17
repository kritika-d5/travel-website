
document.addEventListener('DOMContentLoaded', function() {

    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
    
    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    if (profileTabs.length > 0 && tabContents.length > 0) {
        profileTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                profileTabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
    
    // Handle contact form submission
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic form validation
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;
            
            if (!name || !email || !message) {
                alert('Please fill in all required fields');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }
            
            alert('Thank you for your message! We will get back to you soon.');
            contactForm.reset();
        });
    }
    
    const testimonials = document.querySelectorAll('.testimonial');
    let currentTestimonial = 0;
    
    if (testimonials.length > 1) {
        for (let i = 1; i < testimonials.length; i++) {
            testimonials[i].style.display = 'none';
        }
  
        setInterval(function() {
            testimonials[currentTestimonial].style.display = 'none';
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            testimonials[currentTestimonial].style.display = 'block';
        }, 5000);
    }
});

  const hero = document.querySelector('.hero');
  const images = [
    'images/background1.jpg',
    'images/background2.jpg',
    'images/background3.jpg'
    
  ];
  let currentIndex = 0;

  function changeBackground() {
    hero.style.backgroundImage = `url('${images[currentIndex]}')`;
    currentIndex = (currentIndex + 1) % images.length;
  }

  changeBackground(); // Initial call
  setInterval(changeBackground, 5000);
