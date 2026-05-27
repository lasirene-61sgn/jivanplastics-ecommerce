@extends('frontend.layouts.app')

@section('title', $subcategory->name . ' - Subcategories')

@section('content')
    <div style="padding: 2rem 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
            <!-- Breadcrumb -->
            <nav style="margin-bottom: 2rem;">
                <ol style="display: flex; list-style: none; padding: 0;">
                    <li>
                        <a href="{{ route('home') }}" style="text-decoration: none; color: #4b5563;">Home</a>
                    </li>
                    <li style="margin: 0 0.5rem; color: #9ca3af;">/</li>
                    <li>
                        <a href="{{ route('categories.show', $subcategory->category) }}" style="text-decoration: none; color: #4b5563;">
                            {{ $subcategory->category->name }}
                        </a>
                    </li>
                    <li style="margin: 0 0.5rem; color: #9ca3af;">/</li>
                    <li style="color: #1f2937;">{{ $subcategory->name }}</li>
                </ol>
            </nav>
            
            <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 1.5rem; color: #1f2937;">
                {{ $subcategory->name }}
            </h1>
            
            @if($subcategory->description)
                <p style="color: #6b7280; margin-bottom: 2rem; font-size: 1.125rem;">
                    {{ $subcategory->description }}
                </p>
            @endif
            
            <!-- Sub-subcategories Grid -->
            @if($subcategory->subSubcategories->count() > 0)
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 2rem 0 1.5rem; color: #1f2937;">
                    Sub-subcategories
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    @foreach($subcategory->subSubcategories as $subSubcategory)
                        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s ease;">
                            <div style="padding: 1.5rem; text-align: center;">
                                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                                    <a href="{{ route('sub-subcategories.show', $subSubcategory) }}" style="text-decoration: none; color: #1f2937;">
                                        {{ $subSubcategory->name }}
                                    </a>
                                </h3>
                                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                                    {{ $subSubcategory->products->count() }} products
                                </p>
                                <a href="{{ route('sub-subcategories.show', $subSubcategory) }}" class="btn" style="display: inline-block; padding: 0.5rem 1rem;">
                                    View Products
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Products Section -->
            @if($subcategory->products->count() > 0)
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 2rem 0 1.5rem; color: #1f2937;">
                    Products in {{ $subcategory->name }}
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                    @foreach($subcategory->products as $product)
                        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
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
            @elseif($subcategory->subSubcategories->count() == 0)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; text-align: center;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">No products available</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        There are no products in this subcategory yet.
                    </p>
                    <a href="{{ route('categories.show', $subcategory->category) }}" class="btn">Back to {{ $subcategory->category->name }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection