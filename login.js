document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');
    const themeSwitch = document.getElementById('theme-switch');

    // Check for saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeSwitch.checked = true;
    }

    // Register/Login toggle
    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });
    }

    if (loginBtn) {
        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    }

    // Theme toggle
    themeSwitch.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#ff3860';
                } else {
                    input.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });
});

function sendOTP() {
    const method = document.querySelector('.option-btn.active').dataset.method;
    const contact = method === 'phone' ? 
        document.querySelector('input[name="phone"]').value :
        document.querySelector('input[name="email"]').value;

    fetch('verify_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=send_otp&method=${method}&contact=${encodeURIComponent(contact)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        }
    });
}

function verifyOTP() {
    const otp = document.querySelector('input[name="otp"]').value;
    const newPassword = document.querySelector('input[name="new_password"]').value;

    fetch('verify_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=verify_otp&otp=${otp}&new_password=${encodeURIComponent(newPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            document.getElementById('forgotPasswordModal').style.display = 'none';
            // Optionally redirect to login page
            // window.location.href = 'login.php';
        }
    });
} 