@extends('frontend.layouts.app')

@section('title', $category->name . ' - Categories')

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
                    <li style="color: #1f2937;">{{ $category->name }}</li>
                </ol>
            </nav>
            
            <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 1.5rem; color: #1f2937;">
                {{ $category->name }}
            </h1>
            
            @if($category->description)
                <p style="color: #6b7280; margin-bottom: 2rem; font-size: 1.125rem;">
                    {{ $category->description }}
                </p>
            @endif
            
            <!-- Subcategories Grid -->
            @if($category->subcategories->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                    @foreach($category->subcategories as $subcategory)
                        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s ease;">
                            <div style="padding: 1.5rem; text-align: center;">
                                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                                    <a href="{{ route('subcategories.show', $subcategory) }}" style="text-decoration: none; color: #1f2937;">
                                        {{ $subcategory->name }}
                                    </a>
                                </h3>
                                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                                    {{ $subcategory->products->count() }} products
                                </p>
                                <a href="{{ route('subcategories.show', $subcategory) }}" class="btn" style="display: inline-block; padding: 0.5rem 1rem;">
                                    View Products
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; text-align: center;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">No subcategories available</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        There are no subcategories in this category yet.
                    </p>
                    <a href="{{ route('home') }}#categories" class="btn">Back to Categories</a>
                </div>
            @endif
        </div>
    </div>
@endsection