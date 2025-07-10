// Add to cart functionality
function addToCart(productId) {
    // Create FormData to send POST request
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart!');
            updateCartCount();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product to cart');
    });
}

// Remove from cart
function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        const formData = new FormData();
        formData.append('product_id', productId);
        
        fetch('remove_from_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing product from cart');
        });
    }
}

// Update cart count in navigation
function updateCartCount() {
    fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        const cartLinks = document.querySelectorAll('a[href="cart.php"]');
        cartLinks.forEach(link => {
            link.textContent = `Cart (${data.count})`;
        });
    })
    .catch(error => console.error('Error updating cart count:', error));
}

// Update quantity in cart
function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQuantity);
    
    fetch('update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating quantity');
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#e74c3c';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return isValid;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add loading animation to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit') {
                this.innerHTML = 'Loading...';
                this.disabled = true;
            }
        });
    });
    
    // Update cart count on page load
    updateCartCount();
});