document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary DOM elements
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const fileInput = document.getElementById('file-input');
    const attachBtn = document.getElementById('attach-btn');
    const chatMessages = document.getElementById('chat-messages');
    const chatToggle = document.getElementById('chat-toggle');
    const chatWrapper = document.getElementById('chat-wrapper');
    const closeChat = document.getElementById('close-chat');
    const themeSwitch = document.getElementById('theme-switch');
    const menuToggle = document.querySelector('.menu-toggle');
    const navContent = document.querySelector('.nav-content');
    const slides = document.querySelectorAll('.slide');
    const counters = document.querySelectorAll('.counter');

    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeSwitch.checked = true;
    }

    // Chat toggle with genie effect
    chatToggle.addEventListener('click', () => {
        if (!chatWrapper.classList.contains('active')) {
            // Opening
            chatWrapper.classList.remove('closing');
            chatWrapper.classList.add('active');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
            // Closing
            chatWrapper.classList.add('closing');
            chatWrapper.addEventListener('animationend', () => {
                if (chatWrapper.classList.contains('closing')) {
                    chatWrapper.classList.remove('active');
                    chatWrapper.classList.remove('closing');
                }
            }, { once: true });
        }
    });

    // Close chat with X button
    closeChat.addEventListener('click', (e) => {
        e.stopPropagation();
        chatWrapper.classList.add('closing');
        chatWrapper.addEventListener('animationend', () => {
            if (chatWrapper.classList.contains('closing')) {
                chatWrapper.classList.remove('active');
                chatWrapper.classList.remove('closing');
            }
        }, { once: true });
    });

    // Close chat when clicking outside
    document.addEventListener('click', (e) => {
        if (!chatWrapper.contains(e.target) && 
            !chatToggle.contains(e.target) && 
            chatWrapper.classList.contains('active')) {
            chatWrapper.classList.add('closing');
            chatWrapper.addEventListener('animationend', () => {
                if (chatWrapper.classList.contains('closing')) {
                    chatWrapper.classList.remove('active');
                    chatWrapper.classList.remove('closing');
                }
            }, { once: true });
        }
    });

    // Handle file attachment
    attachBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // Load messages initially and set interval
    loadMessages();
    setInterval(loadMessages, 2000);

    // Handle chat form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) {
            alert('Please enter a message');
            return;
        }

        const formData = new FormData(chatForm);
        
        try {
            const response = await fetch('send_message.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                messageInput.value = '';
                fileInput.value = '';
                loadMessages();
            } else {
                alert('Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message');
        }
    });

    // Theme toggle handler
    themeSwitch.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });

    // Mobile navigation toggle
    if (menuToggle && navContent) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            navContent.classList.toggle('active');
            console.log('Menu toggled:', navContent.classList.contains('active')); // Debug
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!menuToggle.contains(e.target) && !navContent.contains(e.target) && navContent.classList.contains('active')) {
                menuToggle.classList.remove('active');
                navContent.classList.remove('active');
                console.log('Menu closed (outside click)');
            }
        });

        // Prevent menu from closing when clicking inside
        navContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Smooth scroll functionality
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: targetId === '#top' ? 0 : offsetPosition,
                    behavior: 'smooth'
                });

                // Close mobile menu after clicking a link
                if (menuToggle && navContent) {
                    menuToggle.classList.remove('active');
                    navContent.classList.remove('active');
                }
            }
        });
    });

    // Hero Slider
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (i === index) {
                slide.classList.add('active');
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    function startSlideshow() {
        slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
    }

    startSlideshow();

    // Handle visibility change for slideshow
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(slideInterval);
        } else {
            startSlideshow();
        }
    });

    // Counter animation
    const speed = 1000;

    const updateCount = (counter) => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const count = parseFloat(counter.innerText);
        const increment = target / speed;

        if (count < target) {
            if (target % 1 !== 0) {
                counter.innerText = (count + increment).toFixed(1);
            } else {
                counter.innerText = Math.ceil(count + increment);
            }
            setTimeout(() => updateCount(counter), 10);
        } else {
            counter.innerText = target;
        }
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                updateCount(counter);
                observer.unobserve(counter);
            }
        });
    });

    counters.forEach(counter => observer.observe(counter));

    // Debugging logs
    console.log('DOM Loaded, Scripts Initialized');
    console.log('Slides found:', slides.length);
});

// Function to load messages
async function loadMessages() {
    try {
        const response = await fetch('get_messages.php');
        if (!response.ok) throw new Error('Network response was not ok');
        const messages = await response.json();
        
        let html = '';
        messages.forEach(message => {
            const messageClass = message.is_own ? 'sent' : 'received';
            let attachmentHtml = '';
            
            if (message.file_path) {
                attachmentHtml = `
                    <div class="file-attachment">
                        <a href="${message.file_path}" target="_blank">
                            📎 ${message.file_name}
                        </a>
                    </div>
                `;
            }

            html += `
                <div class="message ${messageClass}">
                    <strong>${message.username}</strong>
                    <p>${message.message}</p>
                    ${attachmentHtml}
                    <div class="message-info">
                        ${message.created_at}
                    </div>
                </div>
            `;
        });

        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = html;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    } catch (error) {
        console.error('Error loading messages:', error);
        document.getElementById('chat-messages').innerHTML = '<p>Error loading messages. Please try again later.</p>';
    }
}