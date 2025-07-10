# Mini E-Commerce Website

A beginner-friendly e-commerce website built with HTML, CSS, JavaScript, PHP, and MySQL.

## 🚀 Features

- **Home Page**: Display list of products with images, prices, and stock
- **Product Details**: Individual product pages with full descriptions
- **Shopping Cart**: Add/remove items, update quantities
- **Contact Form**: Contact page with form validation
- **Admin Panel**: Add and manage products
- **Responsive Design**: Works on desktop and mobile devices

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Local Server**: XAMPP

## 📋 Prerequisites

- XAMPP (includes Apache, MySQL, PHP)
- Web browser
- Text editor (VS Code, Sublime Text, etc.)

## 🔧 Installation & Setup

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP and start Apache and MySQL services

### Step 2: Create Project Directory
1. Navigate to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
2. Create a new folder called `ecommerce`
3. Copy all project files into this folder

### Step 3: Setup Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database called `ecommerce`
3. Import the SQL file or run the queries from `ecommerce.sql`

### Step 4: Configure Database Connection
1. Open `config.php`
2. Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ecommerce');
   ```

### Step 5: Create Images Folder
1. Create an `images` folder in your project directory
2. Add product images or use placeholder images
3. Recommended image names:
   - `laptop.jpg`
   - `phone.jpg`
   - `headphones.jpg`
   - `tablet.jpg`
   - `watch.jpg`
   - `camera.jpg`
   - `placeholder.jpg` (fallback image)

### Step 6: Access the Website
1. Open your browser
2. Navigate to: `http://localhost/ecommerce`

## 📁 File Structure

```
ecommerce/
├── config.php              # Database configuration
├── index.php               # Home page
├── product.php             # Product details page
├── cart.php                # Shopping cart page
├── contact.php             # Contact form page
├── admin.php               # Admin panel
├── add_to_cart.php         # Add to cart functionality
├── remove_from_cart.php    # Remove from cart functionality
├── update_cart.php         # Update cart quantities
├── get_cart_count.php      # Get cart count for navigation
├── clear_cart.php          # Clear entire cart
├── style.css               # Main stylesheet
├── script.js               # JavaScript functionality
├── ecommerce.sql           # Database setup file
├── images/                 # Product images folder
└── README.md               # This file
```

## 🎯 How to Use

### For Customers:
1. **Browse Products**: Visit the home page to see all products
2. **View Details**: Click "View Details" to see product information
3. **Add to Cart**: Click "Add to Cart" to add items
4. **Manage Cart**: View cart, update quantities, or remove items
5. **Contact**: Use the contact form for inquiries

### For Admins:
1. **Access Admin Panel**: Navigate to `/admin.php`
2. **Add Products**: Fill out the form to add new products
3. **Manage Products**: View all products and delete if needed
4. **Upload Images**: Add product images to the `images` folder

## 🔧 Customization

### Adding New Products
1. Use the Admin panel to add products through the web interface
2. Or manually insert into the database:
   ```sql
   INSERT INTO products (name, description, price, image, stock) 
   VALUES ('Product Name', 'Description', 99.99, 'image.jpg', 10);
   ```

### Styling Changes
- Edit `style.css` to modify the appearance
- The CSS uses flexbox and grid for responsive design
- Color scheme uses CSS custom properties for easy theming

### Adding Features
- **User Authentication**: Add login/register functionality
- **Order Management**: Create orders table and checkout process
- **Payment Integration**: Add payment gateway (Stripe, PayPal)
- **Email Notifications**: Send emails for orders and contact forms
- **Search & Filter**: Add product search and category filters

## 🚨 Security Notes

⚠️ **Important**: This is a beginner project for learning purposes. For production use, implement:

- Input validation and sanitization
- SQL injection protection (prepared statements are used)
- XSS protection
- CSRF protection
- User authentication and authorization
- Password hashing
- HTTPS/SSL encryption
- File upload validation

## 📱 Responsive Design

The website is fully responsive and includes:
- Mobile-first design approach
- Flexible grid layouts
- Touch-friendly buttons
- Optimized images
- Readable typography on all devices

## 🐛 Troubleshooting

### Common Issues:

1. **Database Connection Error**
   - Check if MySQL is running in XAMPP
   - Verify database credentials in `config.php`
   - Ensure database `ecommerce` exists

2. **Images Not Loading**
   - Check if `images` folder exists
   - Verify image file names match database entries
   - Ensure proper file permissions

3. **Cart Not Working**
   - Check if sessions are enabled in PHP
   - Verify JavaScript is enabled in browser
   - Check browser console for errors

4. **Page Not Found**
   - Ensure all files are in the correct directory
   - Check file names and extensions
   - Verify Apache is running

## 📚 Learning Resources

To better understand the code:
- **PHP**: [PHP.net Documentation](https://www.php.net/docs.php)
- **MySQL**: [MySQL Documentation](https://dev.mysql.com/doc/)
- **JavaScript**: [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- **CSS**: [CSS-Tricks](https://css-tricks.com/)

## 🔄 Next Steps

After completing this project, consider:
1. Adding user authentication
2. Implementing a proper checkout system
3. Adding product categories and search
4. Creating an inventory management system
5. Adding product reviews and ratings
6. Implementing email notifications
7. Adding payment gateway integration

## 📄 License

This project is open source and available under the MIT License.

---

**Happy Coding! 🎉**

If you encounter any issues or have questions, feel free to modify the code and experiment with new features!