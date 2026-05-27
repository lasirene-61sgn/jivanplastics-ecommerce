# JivanPlatics B2B API Documentation

This document explains how to use the B2B API for the JivanPlatics application.

## Base URL
```
http://127.0.0.1:8000/api/b2b
```

## Authentication Flow

### 1. Login
Authenticate a B2B user to get an API token.

**Endpoint:** `POST /api/b2b/login`

**Request Body:**
```json
{
  "email": "dealer@gmail.com",
  "password": "your_password"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Dealer Name",
    "email": "dealer@gmail.com",
    "customer_type": "dealer"
  },
  "token": "your_api_token_here",
  "token_type": "Bearer"
}
```

**Headers for subsequent requests:**
```
Authorization: Bearer your_api_token_here
```

### 2. Logout
Log out the current B2B user and invalidate the token.

**Endpoint:** `POST /api/b2b/logout`

**Headers:**
```
Authorization: Bearer your_api_token_here
```

**Response:**
```json
{
  "message": "Logout successful"
}
```

## API Endpoints

### Dashboard
**GET /api/b2b/dashboard** - Get B2B dashboard data including recent orders and statistics

### Products
**GET /api/b2b/products** - Get all products with categories and B2B discounts applied

### Orders
**GET /api/b2b/orders** - Get all orders for the logged-in dealer
**GET /api/b2b/orders/{id}** - Get details of a specific order

### Profile
**GET /api/b2b/profile** - Get dealer profile information
**POST /api/b2b/profile** - Update dealer profile information

### Cart
**GET /api/b2b/cart** - Get cart contents
**POST /api/b2b/cart/add** - Add product to cart
**PUT /api/b2b/cart/update** - Update cart item quantity
**DELETE /api/b2b/cart/remove** - Remove item from cart
**DELETE /api/b2b/cart/clear** - Clear entire cart
**GET /api/b2b/cart/count** - Get cart item count

### Checkout
**GET /api/b2b/checkout** - Get checkout details (cart + delivery information)
**POST /api/b2b/checkout** - Place order with delivery address and payment method

### Return Requests
**GET /api/b2b/orders/{orderId}/items/{orderItemId}/return-request** - Show return request form
**POST /api/b2b/orders/{orderId}/items/{orderItemId}/return-request** - Submit a return request
**GET /api/b2b/orders/{orderId}/return-requests** - View return requests for an order

### Rewards
**GET /api/b2b/rewards** - Get available rewards for claiming
**GET /api/b2b/rewards/{id}/claim** - Show claim form for a specific reward
**POST /api/b2b/rewards/{id}/claim** - Submit a reward claim
**GET /api/b2b/reward-claims** - Get all reward claims made by the dealer

## Using Postman

1. Import the provided Postman collection (`postman_collection.json`)
2. Set the environment variable `baseUrl` to your API base URL (e.g., `http://127.0.0.1:8000`)
3. Execute the "B2B Login" request to get a token
4. Copy the token from the response and set it as the `b2b_token` environment variable
5. You can now use other endpoints that require authentication

## Testing the API

1. Start your Laravel application:
   ```
   php artisan serve
   ```

2. Open Postman and import the collection

3. First, test the login endpoint with your dealer credentials:
   - Method: POST
   - URL: `http://127.0.0.1:8000/api/b2b/login`
   - Body (JSON): 
     ```json
     {
       "email": "dealer@gmail.com",
       "password": "12345678"
     }
     ```

4. After successful login, use the returned token in the Authorization header for other requests

## Error Handling

- **401 Unauthorized**: Invalid or expired token
- **404 Not Found**: Resource doesn't exist
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server-side errors

## Notes

- All endpoints except login require authentication
- Make sure the user account exists and has `customer_type` set to 'dealer'
- The dealer account must be active (`is_active` = true) to log in
- B2B discounts are automatically applied to products based on category settings