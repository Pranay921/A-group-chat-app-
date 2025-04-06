// Favorites functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize favorite buttons
    initializeFavoriteButtons();
});

function initializeFavoriteButtons() {
    // Get all favorite buttons
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        const courseCard = button.closest('.course-card');
        if (!courseCard) return;
        
        // Generate a unique course ID if not present
        if (!courseCard.dataset.courseId) {
            courseCard.dataset.courseId = generateCourseId(courseCard);
        }
        
        const courseId = courseCard.dataset.courseId;
        
        // Check if this course is already in favorites
        checkFavoriteStatus(courseId, button);
        
        // Add click event listener
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isActive = button.classList.contains('active');
            
            if (isActive) {
                // Remove from favorites
                removeFavorite(courseId, button);
            } else {
                // Add to favorites
                addFavorite(courseCard, button);
            }
        });
    });
}

function generateCourseId(courseCard) {
    // Generate a unique ID based on course title or other attributes
    const title = courseCard.querySelector('.course-title').textContent;
    return 'course_' + title.trim().toLowerCase().replace(/\s+/g, '_') + '_' + Date.now();
}

function checkFavoriteStatus(courseId, button) {
    fetch('handle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=check&course_id=${encodeURIComponent(courseId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.isFavorite) {
            button.classList.add('active');
            button.innerHTML = '<i class="fas fa-heart"></i>';
        } else {
            button.classList.remove('active');
            button.innerHTML = '<i class="far fa-heart"></i>';
        }
    })
    .catch(error => {
        console.error('Error checking favorite status:', error);
    });
}

function addFavorite(courseCard, button) {
    // Get course data
    const courseId = courseCard.dataset.courseId;
    const courseTitle = courseCard.querySelector('.course-title').textContent;
    const courseImage = courseCard.querySelector('.course-image img').src;
    const instructorName = courseCard.querySelector('.instructor-name').textContent;
    const instructorImage = courseCard.querySelector('.instructor-img').src;
    const courseTag = courseCard.querySelector('.course-tag').textContent;
    
    // Extract numeric values
    const lessonsText = courseCard.querySelector('.course-info span:nth-child(1)').textContent;
    const lessons = parseInt(lessonsText.match(/\d+/)[0]);
    
    const studentsText = courseCard.querySelector('.course-info span:nth-child(2)').textContent;
    const students = parseInt(studentsText);
    
    const ratingText = courseCard.querySelector('.course-info span:nth-child(3)').textContent;
    const ratingMatch = ratingText.match(/\d+(\.\d+)?/);
    const rating = ratingMatch ? parseFloat(ratingMatch[0]) : 0;
    
    const levelTag = courseCard.querySelector('.level-tag').textContent;
    const priceText = courseCard.querySelector('.price').textContent;
    const price = parseFloat(priceText.replace('$', ''));
    
    // Send data to server
    fetch('handle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&course_id=${encodeURIComponent(courseId)}&course_title=${encodeURIComponent(courseTitle)}&course_image=${encodeURIComponent(courseImage)}&instructor_name=${encodeURIComponent(instructorName)}&instructor_image=${encodeURIComponent(instructorImage)}&course_tag=${encodeURIComponent(courseTag)}&lessons=${lessons}&students=${students}&rating=${rating}&level_tag=${encodeURIComponent(levelTag)}&price=${price}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance
            button.classList.add('active');
            button.innerHTML = '<i class="fas fa-heart"></i>';
            
            // Show success message
            showNotification('Added to favorites!', 'success');
        } else {
            showNotification('Failed to add to favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding favorite:', error);
        showNotification('Error adding to favorites', 'error');
    });
}

function removeFavorite(courseId, button) {
    fetch('handle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&course_id=${encodeURIComponent(courseId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance
            button.classList.remove('active');
            button.innerHTML = '<i class="far fa-heart"></i>';
            
            // If we're on the favorites page, remove the card
            if (window.location.pathname.includes('favorites.php')) {
                const courseCard = button.closest('.course-card');
                if (courseCard) {
                    courseCard.style.opacity = '0';
                    setTimeout(() => {
                        courseCard.remove();
                        
                        // Check if there are no more favorites
                        if (document.querySelectorAll('.course-card').length === 0) {
                            const container = document.querySelector('.courses-container');
                            if (container) {
                                container.innerHTML = `
                                    <div class="no-favorites">
                                        <p>You haven't added any courses to your favorites yet.</p>
                                        <a href="index.php#courses-section" class="btn">Browse Courses</a>
                                    </div>
                                `;
                            }
                        }
                    }, 300);
                }
            }
            
            showNotification('Removed from favorites', 'success');
        } else {
            showNotification('Failed to remove from favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing favorite:', error);
        showNotification('Error removing from favorites', 'error');
    });
}

function showNotification(message, type) {
    // Create notification element if it doesn't exist
    let notification = document.querySelector('.notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.className = 'notification';
        document.body.appendChild(notification);
    }
    
    // Set message and type
    notification.textContent = message;
    notification.className = `notification ${type}`;
    
    // Show notification
    notification.style.display = 'block';
    notification.style.opacity = '1';
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }, 3000);
}