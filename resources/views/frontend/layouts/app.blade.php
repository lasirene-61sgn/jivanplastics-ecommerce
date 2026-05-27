<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'E-Commerce Store')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
/* Enhanced Responsive E-Commerce Store - White Background with Red & Black Accents */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    background-color: #ffffff;
    color: #1f2937;
    line-height: 1.6;
}

/* Header Styles */
.header {
    background-color: #ffffff;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 2px solid #dc2626;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.logo {
    font-size: 1.75rem;
    font-weight: 700;
    color: #dc2626;
    text-decoration: none;
    transition: all 0.3s ease;
}

.logo:hover {
    color: #b91c1c;
    transform: scale(1.05);
}

.logo img {
    height: 80px;
    transition: transform 0.3s ease;
}

.logo img:hover {
    transform: scale(1.05);
}

/* Navigation Links */
.nav-links {
    display: flex;
    list-style: none;
    gap: 2rem;
    align-items: center;
}

.nav-links li {
    position: relative;
}

.nav-links a {
    text-decoration: none;
    color: #1f2937;
    font-weight: 500;
    font-size: 1rem;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    position: relative;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: #dc2626;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-links a:hover {
    color: #dc2626;
}

.nav-links a:hover::after {
    width: 80%;
}

/* User Actions Section */
.navbar > div:last-child {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.navbar > div:last-child a {
    text-decoration: none;
    color: #1f2937;
    font-weight: 500;
    padding: 0.5rem 1.25rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.navbar > div:last-child a:hover {
    background-color: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}

/* Cart Icon */
.cart-icon {
    position: relative;
    color: #1f2937;
    text-decoration: none;
    padding: 0.5rem 1.25rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    background-color: rgba(220, 38, 38, 0.05);
    border: 1px solid #dc2626;
    font-weight: 500;
}

.cart-icon:hover {
    background-color: #dc2626;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border-radius: 50%;
    min-width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.6);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: white;
    padding: 5rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 50%, rgba(220, 38, 38, 0.2) 0%, transparent 70%);
}

.hero h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #ffffff, #dc2626);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
}

.hero p {
    font-size: 1.35rem;
    margin-bottom: 2.5rem;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    color: #e5e5e5;
    position: relative;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
}

.btn:active {
    transform: translateY(0);
}

.btn-outline {
    background: transparent;
    border: 2px solid #dc2626;
    color: #dc2626;
    box-shadow: none;
}

.btn-outline:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border-color: #dc2626;
}

/* Main Content */
.main-content {
    padding: 3rem 0;
    min-height: 60vh;
    background-color: #ffffff;
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    padding-bottom: 1rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, transparent, #dc2626, transparent);
}

/* Alerts */
.alert {
    padding: 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
    animation: slideInDown 0.3s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background-color: rgba(34, 197, 94, 0.1);
    color: #15803d;
    border-color: #22c55e;
}

.alert-error {
    background-color: rgba(220, 38, 38, 0.1);
    color: #b91c1c;
    border-color: #dc2626;
}

.alert ul {
    list-style: none;
    padding: 0;
}

.alert ul li {
    padding: 0.25rem 0;
}

/* Footer */
.footer {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: white;
    padding: 3rem 0 1rem;
    margin-top: 4rem;
    border-top: 3px solid #dc2626;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section {
    padding: 1rem;
}

.footer-section h3 {
    font-size: 1.35rem;
    margin-bottom: 1.25rem;
    color: #dc2626;
    font-weight: 600;
}

.footer-section p {
    color: #d1d5db;
    line-height: 1.8;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.75rem;
}

.footer-section a {
    color: #d1d5db;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-section a:hover {
    color: #dc2626;
    transform: translateX(5px);
}

.footer-bottom {
    text-align: center;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid #374151;
    color: #9ca3af;
}

/* Mobile Menu Toggle (Hidden by default) */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #1f2937;
    font-size: 1.75rem;
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.3s ease;
    line-height: 1;
}

.mobile-menu-toggle:hover {
    color: #dc2626;
}

/* ==================== RESPONSIVE DESIGN ==================== */

/* Tablet Devices (768px - 1024px) */
@media (max-width: 1024px) {
    .container {
        padding: 0 1.25rem;
    }
    
    .nav-links {
        gap: 1.25rem;
    }
    
    .nav-links a {
        font-size: 0.95rem;
        padding: 0.4rem 0.8rem;
    }
    
    .hero h1 {
        font-size: 2.5rem;
    }
    
    .hero p {
        font-size: 1.15rem;
    }
    
    .navbar > div:last-child {
        gap: 1rem;
    }
}

/* Tablet Portrait & Large Mobile (481px - 768px) */
@media (max-width: 768px) {
    .navbar {
        flex-wrap: wrap;
        justify-content: space-between;
        position: relative;
    }
    
    .logo {
        order: 1;
    }
    
    .logo img {
        height: 60px;
    }
    
    .mobile-menu-toggle {
        display: block;
        order: 2;
    }
    
    /* Hide user actions in mobile when menu is closed */
    .navbar > div:last-child {
        display: none;
    }
    
    .nav-links {
        display: none;
        flex-direction: column;
        width: 100%;
        order: 3;
        background-color: #ffffff;
        padding: 1rem 0;
        border-radius: 0.5rem;
        margin-top: 1rem;
        gap: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    .nav-links.active {
        display: flex;
    }
    
    /* Show user actions when menu is active */
    .nav-links.active ~ div:last-child {
        display: flex !important;
        width: 100%;
        order: 4;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
    }
    
    .nav-links li {
        width: 100%;
    }
    
    .nav-links a {
        display: block;
        width: 100%;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        color: #1f2937;
    }
    
    .nav-links li:last-child a {
        border-bottom: none;
    }
    
    .nav-links a::after {
        display: none;
    }
    
    .nav-links a:hover {
        background-color: rgba(220, 38, 38, 0.05);
        color: #dc2626;
    }
    
    .navbar > div:last-child a {
        padding: 0.75rem 1.25rem;
        font-size: 0.95rem;
        text-align: center;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .cart-icon {
        width: 100%;
        text-align: center;
    }
    
    .hero {
        padding: 3.5rem 0;
    }
    
    .hero h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .hero p {
        font-size: 1rem;
        margin-bottom: 1.5rem;
        padding: 0 1rem;
    }
    
    .btn {
        padding: 0.85rem 1.5rem;
        font-size: 0.95rem;
    }
    
    .section-title {
        font-size: 1.65rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

/* Mobile Devices (320px - 480px) */
@media (max-width: 480px) {
    .container {
        padding: 0 1rem;
    }
    
    .header {
        padding: 0.75rem 0;
    }
    
    .logo {
        font-size: 1.35rem;
    }
    
    .logo img {
        height: 50px;
    }
    
    .mobile-menu-toggle {
        font-size: 1.5rem;
    }
    
    .nav-links a {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }
    
    .navbar > div:last-child a {
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }
    
    .cart-icon {
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }
    
    .cart-count {
        min-width: 20px;
        height: 20px;
        font-size: 0.7rem;
        top: -6px;
        right: -6px;
    }
    
    .hero {
        padding: 2.5rem 0;
    }
    
    .hero h1 {
        font-size: 1.65rem;
        margin-bottom: 0.85rem;
    }
    
    .hero p {
        font-size: 0.95rem;
        margin-bottom: 1.25rem;
    }
    
    .btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .main-content {
        padding: 2rem 0;
    }
    
    .section-title {
        font-size: 1.45rem;
    }
    
    .alert {
        padding: 1rem;
        font-size: 0.9rem;
    }
    
    .footer {
        padding: 2rem 0 1rem;
        margin-top: 2.5rem;
    }
    
    .footer-section h3 {
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }
    
    .footer-section {
        padding: 0.5rem;
    }
    
    .footer-bottom {
        font-size: 0.85rem;
    }
}

/* Extra Small Devices (< 320px) */
@media (max-width: 319px) {
    .logo img {
        height: 40px;
    }
    
    .hero h1 {
        font-size: 1.4rem;
    }
    
    .hero p {
        font-size: 0.85rem;
    }
    
    .btn {
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
    }
    
    .nav-links a,
    .navbar > div:last-child a {
        font-size: 0.85rem;
    }
}

/* Landscape Mobile Devices */
@media (max-height: 500px) and (orientation: landscape) {
    .hero {
        padding: 2rem 0;
    }
    
    .hero h1 {
        font-size: 1.75rem;
    }
    
    .hero p {
        font-size: 0.95rem;
    }
    
    .main-content {
        padding: 1.5rem 0;
    }
}

/* Print Styles */
@media print {
    .header,
    .footer,
    .btn,
    .mobile-menu-toggle {
        display: none;
    }
    
    body {
        background: white;
        color: black;
    }
}

        </style>
    @endif
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('home') }}" class="logo"><img src="{{ asset('images/logo.png') }} " style="height: 80px;"></a>
                
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
                
                <div>
                    @auth
                        <a href="{{ route('cart.index') }}" class="cart-icon">
                            Cart
                            @php
                                $cartCount = count(session('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="cart-count">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           style="margin-left: 1rem;">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}" style="margin-left: 1rem;">Register</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Content Section -->
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>E-Commerce Store</h3>
                    <p>Your one-stop shop for all your needs.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('products.index') }}">Products</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Returns & Exchanges</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} E-Commerce Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelector('.nav-links');
    
    // Create mobile menu toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'mobile-menu-toggle';
    toggleBtn.innerHTML = '☰';
    toggleBtn.setAttribute('aria-label', 'Toggle menu');
    
    // Insert toggle button after logo
    const logo = navbar.querySelector('.logo');
    logo.parentNode.insertBefore(toggleBtn, logo.nextSibling);
    
    // Toggle menu on button click
    toggleBtn.addEventListener('click', function() {
        navLinks.classList.toggle('active');
        this.innerHTML = navLinks.classList.contains('active') ? '✕' : '☰';
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInside = navbar.contains(event.target);
        if (!isClickInside && navLinks.classList.contains('active')) {
            navLinks.classList.remove('active');
            toggleBtn.innerHTML = '☰';
        }
    });
    
    // Close menu when a link is clicked
    const menuLinks = navLinks.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                navLinks.classList.remove('active');
                toggleBtn.innerHTML = '☰';
            }
        });
    });
});
</script>
</body>
</html>