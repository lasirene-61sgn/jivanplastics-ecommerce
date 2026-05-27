@extends('frontend.layouts.app')

@section('title', 'Home - E-Commerce Store')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to Our E-Commerce Store</h1>
            <p>Discover amazing products at great prices with fast delivery and excellent customer service.</p>
            <a href="#categories" class="btn">Shop by Category</a>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section id="categories" style="padding: 3rem 0;">
        <h2 class="section-title">Shop by Category</h2>
        
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center;">
            @foreach($categories as $category)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 350px; margin: 0 auto;">
                    <!-- Category Header -->
                    <div style="padding: 1.5rem; text-align: center;">
                        <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <a href="{{ route('categories.show', $category) }}" style="text-decoration: none; color: #1f2937;">
                                {{ $category->name }}
                            </a>
                        </h3>
                        <p style="color: #6b7280;">
                            {{ $category->subcategories->count() }} subcategories
                        </p>
                        <a href="{{ route('categories.show', $category) }}" class="btn" style="margin-top: 1rem;">View Subcategories</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    
    <!-- Featured Products Section -->
    <section style="padding: 3rem 0; background-color: #f9fafb;">
        <h2 class="section-title">Featured Products</h2>
        
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center;">
            @foreach($featuredProducts as $product)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; width: 250px;">
                    @if($product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                             alt="{{ $product->name }}" 
                             style="width: 100%; height: 200px; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 200px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                            <span>No Image</span>
                        </div>
                    @endif
                    
                    <div style="padding: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <a href="{{ route('products.show', $product) }}" style="text-decoration: none; color: #1f2937;">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p style="color: #6b7280; margin-bottom: 1rem;">
                            ${{ number_format($product->price, 2) }}
                        </p>
                        <a href="{{ route('products.show', $product) }}" class="btn" style="width: 100%; text-align: center;">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    
    <!-- B2B & B2C Section -->
    <section style="padding: 3rem 0;">
        <h2 class="section-title">Our Services</h2>
        
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; width: 350px; text-align: center;">
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #3b82f6;">B2C (Business to Customer)</h3>
                <p style="color: #6b7280; margin-bottom: 1.5rem;">
                    Shop directly as an individual customer. Register, browse products, add to cart, and checkout with cash on delivery.
                </p>
                <a href="{{ route('register') }}?type=individual" class="btn">Register as Customer</a>
            </div>
            
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; width: 350px; text-align: center;">
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #10b981;">B2B (Business to Business)</h3>
                <p style="color: #6b7280; margin-bottom: 1.5rem;">
                    For businesses looking to purchase in bulk. Register as a dealer and our team will review your application.
                </p>
                <a href="{{ route('register') }}?type=dealer" class="btn" style="background-color: #10b981;">Register as Dealer</a>
            </div>
        </div>
    </section>
@endsection