# FoodExpress - Food Ordering Website

A modern, responsive food ordering website built with PHP, Bootstrap, and MySQL, featuring M-Pesa payment integration for the Kenyan market.

## Features

### 🍕 Core Features

- **Responsive Design**: Modern, mobile-first design using Bootstrap 5
- **User Authentication**: Secure login/registration system
- **Product Catalog**: Browse food items by categories
- **Shopping Cart**: Add, remove, and manage cart items
- **Order Management**: Complete order lifecycle from placement to delivery
- **Real-time Tracking**: Track order status with visual progress indicators

### 💳 Payment System

- **M-Pesa Integration**: Seamless mobile money payments
- **Multiple Payment Options**: Cash on delivery, credit/debit cards
- **Secure Transactions**: Encrypted payment processing
- **Payment Status Tracking**: Real-time payment confirmation

### 🚚 Delivery System

- **Delivery Zones**: Configurable delivery areas with different fees
- **Delivery Tracking**: Real-time order status updates
- **Delivery Instructions**: Custom delivery notes
- **Estimated Delivery Times**: Zone-based delivery time estimates

### 🎨 User Experience

- **Modern UI/UX**: Clean, intuitive interface
- **Mobile Responsive**: Optimized for all device sizes
- **Fast Loading**: Optimized performance
- **Search & Filter**: Easy product discovery
- **Order History**: Complete order tracking

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6
- **Payment**: M-Pesa API Integration

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Setup Instructions

1. **Clone the Repository**

   ```bash
   git clone https://github.com/yourusername/foodexpress.git
   cd foodexpress
   ```

2. **Database Setup**

   - Create a new MySQL database named `foodexpress`
   - Import the database schema:

   ```bash
   mysql -u root -p foodexpress < database/schema.sql
   ```

3. **Configuration**

   - Edit `config/database.php` with your database credentials:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'foodexpress');
   ```

4. **Web Server Configuration**

   - Point your web server document root to the project directory
   - Ensure PHP has write permissions for session handling

5. **M-Pesa Configuration** (Optional)

   - For production, configure M-Pesa API credentials in the payment integration files
   - Update API endpoints and authentication tokens

6. **Access the Application**
   - Open your browser and navigate to `http://localhost/foodexpress`
   - The application should be ready to use!

## Project Structure

```
foodexpress/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── ajax/
│   ├── add_to_cart.php
│   └── cancel_order.php
├── database/
│   └── schema.sql
├── index.php
├── menu.php
├── cart.php
├── checkout.php
├── login.php
├── register.php
├── orders.php
├── mpesa_payment.php
├── order_success.php
├── logout.php
└── README.md
```

## Database Schema

The application uses the following main tables:

- **users**: Customer account information
- **categories**: Food categories
- **products**: Food items with pricing
- **orders**: Order details and status
- **order_items**: Individual items in each order
- **mpesa_transactions**: M-Pesa payment records
- **delivery_zones**: Delivery area configurations

## Features in Detail

### User Management

- Secure registration and login
- Profile management
- Order history tracking
- Address management

### Product Management

- Category-based organization
- Product search and filtering
- Featured products highlighting
- Stock availability tracking

### Order Processing

- Multi-step checkout process
- Delivery zone selection
- Payment method selection
- Order confirmation and tracking

### Payment Integration

- M-Pesa STK push integration
- Payment status tracking
- Transaction history
- Secure payment processing

## Customization

### Styling

- Modify `assets/css/style.css` for custom styling
- Update color variables in CSS root for brand colors
- Customize Bootstrap components as needed

### Content

- Update product information in the database
- Modify delivery zones and fees
- Customize payment methods

### Features

- Add new payment gateways
- Implement additional delivery options
- Extend user profile features

## Security Features

- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization
- **CSRF Protection**: Form token validation
- **Password Hashing**: Secure password storage
- **Session Security**: Secure session management

## Performance Optimization

- **Database Indexing**: Optimized queries
- **Image Optimization**: Compressed product images
- **Caching**: Session-based caching
- **Minification**: Compressed CSS/JS files

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:

- Create an issue on GitHub
- Contact: support@foodexpress.com
- Documentation: [Wiki Link]

## Roadmap

### Version 2.0 (Planned)

- [ ] Admin dashboard
- [ ] Restaurant management
- [ ] Real-time delivery tracking
- [ ] Push notifications
- [ ] Multi-language support
- [ ] Advanced analytics

### Version 1.1 (In Progress)

- [ ] Email notifications
- [ ] SMS integration
- [ ] Loyalty program
- [ ] Customer reviews
- [ ] Social media integration

## Acknowledgments

- Bootstrap team for the amazing framework
- Font Awesome for the icons
- M-Pesa for payment integration
- All contributors and testers

---

**FoodExpress** - Delivering delicious food to your doorstep! 🍕🚚
