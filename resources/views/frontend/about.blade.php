@extends('frontend.layouts.app')

@section('title', 'About Us - E-Commerce Store')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem 0;">
        <h1 class="section-title">About Us</h1>
        
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Our Story</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
                Founded in 2023, our e-commerce store began with a simple mission: to make quality products accessible to everyone. 
                What started as a small online shop has grown into a comprehensive platform serving both individual customers (B2C) 
                and businesses (B2B) across the region.
            </p>
            
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Our Mission</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
                We are committed to providing exceptional products and services to our customers. For individual shoppers, 
                we offer a seamless shopping experience with cash on delivery options. For businesses, we provide bulk purchasing 
                solutions with personalized service and competitive pricing.
            </p>
            
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">B2B Services</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
                Our B2B platform is designed for dealers and businesses looking to purchase products in bulk. After registering 
                as a dealer, our team will review your application and provide you with access to wholesale pricing, bulk ordering 
                options, and dedicated account management.
            </p>
            
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">B2C Services</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
                For individual customers, we offer a user-friendly shopping experience with a wide range of products. 
                Browse our catalog, add items to your cart, and checkout with cash on delivery for your convenience.
            </p>
            
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 2rem;">
                <div style="flex: 1; min-width: 200px; text-align: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #3b82f6;">B2C</h3>
                    <p style="color: #6b7280;">
                        Direct shopping for individuals with cash on delivery
                    </p>
                </div>
                
                <div style="flex: 1; min-width: 200px; text-align: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #10b981;">B2B</h3>
                    <p style="color: #6b7280;">
                        Bulk purchasing for businesses with wholesale pricing
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection