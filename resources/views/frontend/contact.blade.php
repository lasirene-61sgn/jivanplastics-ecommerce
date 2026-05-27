@extends('frontend.layouts.app')

@section('title', 'Contact Us - E-Commerce Store')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem 0;">
        <h1 class="section-title">Contact Us</h1>
        
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Get in Touch</h2>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #4b5563;">Address</h3>
                        <p style="color: #6b7280;">
                            123 Commerce Street<br>
                            Business District<br>
                            City, State 12345
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #4b5563;">Phone</h3>
                        <p style="color: #6b7280;">
                            +1 (555) 123-4567
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #4b5563;">Email</h3>
                        <p style="color: #6b7280;">
                            info@ecommercestore.com
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #4b5563;">Business Hours</h3>
                        <p style="color: #6b7280;">
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Send us a Message</h2>
                    
                    <form style="margin-bottom: 1.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Name</label>
                            <input type="text" id="name" name="name" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Email</label>
                            <input type="email" id="email" name="email" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label for="subject" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Subject</label>
                            <input type="text" id="subject" name="subject" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Message</label>
                            <textarea id="message" name="message" rows="5" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
            
            <div style="margin-top: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">B2B Inquiries</h2>
                <p style="color: #6b7280; margin-bottom: 1rem; line-height: 1.6;">
                    For bulk purchasing and business partnerships, please contact our B2B team directly:
                </p>
                <p style="color: #6b7280; margin-bottom: 1rem;">
                    Email: b2b@ecommercestore.com<br>
                    Phone: +1 (555) 123-4568
                </p>
                <a href="{{ route('admin.customers.create') }}" class="btn" style="background-color: #10b981;">Register as Dealer</a>
            </div>
        </div>
    </div>
@endsection