@extends('frontend.layouts.app')

@section('title', 'Products - E-Commerce Store')

@push('styles')
<style>
    /* ========================================
   Products Page - Complete Responsive Styles
   Color Palette: Red (#DC2626) & Black (#000000)
   White Background (#FFFFFF)
   ======================================== */

/* ==================== PRODUCTS PAGE LAYOUT ==================== */

/* Main Products Container */
.products-container {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    width: 100%;
}

/* ==================== SIDEBAR FILTERS ==================== */

.filters-sidebar {
    flex: 1;
    min-width: 250px;
    max-width: 300px;
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 1.75rem;
    height: fit-content;
    position: sticky;
    top: 100px;
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.filters-sidebar:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.filters-sidebar h2 {
    font-size: 1.35rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #000000;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #dc2626;
}

/* Filter Form */
.filter-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-label {
    display: block;
    margin-bottom: 0.65rem;
    font-weight: 500;
    color: #374151;
    font-size: 0.95rem;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-color: #ffffff;
    color: #1f2937;
    font-family: 'Instrument Sans', sans-serif;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.filter-input::placeholder {
    color: #9ca3af;
}

/* Filter Button */
.filter-button {
    width: 100%;
    padding: 0.95rem 1.5rem;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: #ffffff;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    font-family: 'Instrument Sans', sans-serif;
}

.filter-button:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
}

.filter-button:active {
    transform: translateY(0);
}

/* ==================== PRODUCTS LIST SECTION ==================== */

.products-section {
    flex: 3;
    min-width: 300px;
}

/* Products Header */
.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.products-header h1 {
    font-size: 2.25rem;
    font-weight: 600;
    color: #dc2626;
    margin: 0;
}

.products-count {
    color: #6b7280;
    font-size: 1rem;
    font-weight: 500;
}

/* ==================== PRODUCTS GRID ==================== */

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.75rem;
    margin-bottom: 2.5rem;
}

/* Product Card */
.product-card {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(220, 38, 38, 0.15);
    border-color: #dc2626;
}

/* Product Image */
.product-image-container {
    width: 100%;
    height: 240px;
    overflow: hidden;
    position: relative;
    background-color: #f9fafb;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.08);
}

.no-image {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 0.95rem;
    font-weight: 500;
}

/* Product Content */
.product-content {
    padding: 1.35rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1.15rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #1f2937;
    line-height: 1.4;
}

.product-title a {
    text-decoration: none;
    color: #1f2937;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: #dc2626;
}

.product-price {
    color: #dc2626;
    font-size: 1.35rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.product-button {
    width: 100%;
    padding: 0.85rem 1.25rem;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: #ffffff;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    display: inline-block;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
    margin-top: auto;
}

.product-button:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* ==================== EMPTY STATE ==================== */

.empty-state {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 3rem 2rem;
    text-align: center;
    border: 1px solid #f0f0f0;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1f2937;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 1.75rem;
    font-size: 1rem;
    line-height: 1.6;
}

.empty-state-icon {
    font-size: 4rem;
    color: #dc2626;
    margin-bottom: 1.5rem;
    opacity: 0.6;
}

/* ==================== PAGINATION ==================== */

.pagination-container {
    margin-top: 2.5rem;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination li {
    display: inline-block;
}

.pagination a,
.pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 42px;
    padding: 0 0.75rem;
    color: #374151;
    text-decoration: none;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
    background-color: #ffffff;
}

.pagination a:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.pagination .active span {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.pagination .disabled span {
    color: #d1d5db;
    cursor: not-allowed;
    background-color: #f9fafb;
}

.pagination .disabled span:hover {
    transform: none;
    box-shadow: none;
}

/* ==================== RESPONSIVE DESIGN ==================== */

/* Large Desktop (1440px+) */
@media (min-width: 1440px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }
    
    .product-image-container {
        height: 280px;
    }
    
    .filters-sidebar {
        padding: 2rem;
    }
}

/* Desktop (1200px - 1439px) */
@media (max-width: 1439px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

/* Tablet Landscape (1024px - 1199px) */
@media (max-width: 1199px) {
    .products-container {
        gap: 1.5rem;
    }
    
    .filters-sidebar {
        max-width: 280px;
        padding: 1.5rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }
    
    .product-image-container {
        height: 220px;
    }
}

/* Tablet Portrait (768px - 1023px) */
@media (max-width: 1023px) {
    .products-container {
        gap: 1.25rem;
    }
    
    .filters-sidebar {
        position: static;
        max-width: 100%;
        margin-bottom: 1.5rem;
    }
    
    .products-section {
        flex: 1;
        min-width: 100%;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.25rem;
    }
    
    .products-header h1 {
        font-size: 2rem;
    }
    
    .product-image-container {
        height: 200px;
    }
}

/* Mobile Landscape & Small Tablet (641px - 767px) */
@media (max-width: 767px) {
    .products-container {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .filters-sidebar {
        max-width: 100%;
        width: 100%;
        padding: 1.25rem;
    }
    
    .filters-sidebar h2 {
        font-size: 1.25rem;
    }
    
    .products-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .products-header h1 {
        font-size: 1.75rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
    }
    
    .product-content {
        padding: 1.15rem;
    }
    
    .product-title {
        font-size: 1.05rem;
    }
    
    .product-price {
        font-size: 1.25rem;
    }
}

/* Mobile Portrait (481px - 640px) */
@media (max-width: 640px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .product-image-container {
        height: 180px;
    }
    
    .filter-group {
        margin-bottom: 1.25rem;
    }
    
    .filter-input,
    .filter-select {
        padding: 0.75rem 0.85rem;
        font-size: 0.9rem;
    }
    
    .filter-button {
        padding: 0.85rem 1.25rem;
        font-size: 0.95rem;
    }
    
    .pagination a,
    .pagination span {
        min-width: 38px;
        height: 38px;
        padding: 0 0.65rem;
        font-size: 0.9rem;
    }
}

/* Small Mobile (376px - 480px) */
@media (max-width: 480px) {
    .filters-sidebar {
        padding: 1rem;
    }
    
    .filters-sidebar h2 {
        font-size: 1.15rem;
        margin-bottom: 1.25rem;
    }
    
    .products-header h1 {
        font-size: 1.5rem;
    }
    
    .products-count {
        font-size: 0.9rem;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .product-card {
        max-width: 100%;
    }
    
    .product-image-container {
        height: 220px;
    }
    
    .product-content {
        padding: 1rem;
    }
    
    .product-title {
        font-size: 1rem;
    }
    
    .product-price {
        font-size: 1.2rem;
    }
    
    .product-button {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .empty-state {
        padding: 2rem 1.25rem;
    }
    
    .empty-state h3 {
        font-size: 1.25rem;
    }
    
    .empty-state p {
        font-size: 0.95rem;
    }
    
    .pagination a,
    .pagination span {
        min-width: 36px;
        height: 36px;
        padding: 0 0.5rem;
        font-size: 0.85rem;
    }
}

/* Extra Small Mobile (< 375px) */
@media (max-width: 375px) {
    .filters-sidebar {
        padding: 0.85rem;
    }
    
    .filters-sidebar h2 {
        font-size: 1.05rem;
    }
    
    .products-header h1 {
        font-size: 1.35rem;
    }
    
    .filter-input,
    .filter-select {
        padding: 0.65rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .filter-button {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .product-image-container {
        height: 200px;
    }
    
    .product-content {
        padding: 0.85rem;
    }
    
    .product-title {
        font-size: 0.95rem;
    }
    
    .product-price {
        font-size: 1.15rem;
    }
    
    .product-button {
        padding: 0.65rem 0.85rem;
        font-size: 0.85rem;
    }
    
    .pagination a,
    .pagination span {
        min-width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    
    .pagination {
        gap: 0.35rem;
    }
}

/* Landscape Orientation on Mobile */
@media (max-height: 500px) and (orientation: landscape) {
    .filters-sidebar {
        position: static;
    }
    
    .product-image-container {
        height: 160px;
    }
    
    .products-header h1 {
        font-size: 1.5rem;
    }
}

/* ==================== ANIMATIONS ==================== */

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeIn 0.5s ease-out;
}

.product-card:nth-child(1) { animation-delay: 0.05s; }
.product-card:nth-child(2) { animation-delay: 0.1s; }
.product-card:nth-child(3) { animation-delay: 0.15s; }
.product-card:nth-child(4) { animation-delay: 0.2s; }
.product-card:nth-child(5) { animation-delay: 0.25s; }
.product-card:nth-child(6) { animation-delay: 0.3s; }

/* ==================== ACCESSIBILITY ==================== */

.filter-input:focus,
.filter-select:focus,
.filter-button:focus,
.product-button:focus {
    outline: 2px solid #dc2626;
    outline-offset: 2px;
}

/* Skip to main content link */
.skip-to-content {
    position: absolute;
    top: -40px;
    left: 0;
    background: #dc2626;
    color: white;
    padding: 8px;
    text-decoration: none;
    z-index: 100;
}

.skip-to-content:focus {
    top: 0;
}

/* ==================== PRINT STYLES ==================== */

@media print {
    .filters-sidebar,
    .pagination-container {
        display: none;
    }
    
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .product-card {
        break-inside: avoid;
    }
}
</style>
@endpush

@section('content')
    <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
        <!-- Sidebar Filters -->
        <div style="flex: 1; min-width: 250px; background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">Filters</h2>
            
            <form method="GET" action="{{ route('products.index') }}">
                <div style="margin-bottom: 1.5rem;">
                    <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="category" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Category</label>
                    <select id="category" name="category" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (is_object(request('category')) ? request('category')->id == $cat->id : request('category') == $cat->id) || (isset($category) && $category->id == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Apply Filters</button>
            </form>
        </div>
        
        <!-- Products List -->
        <div style="flex: 3; min-width: 300px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h1 class="section-title" style="text-align: left;">Products</h1>
                <p style="color: #6b7280;">
                    {{ $products->total() }} products found
                </p>
            </div>
            
            @if($products->count() > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                    @foreach($products as $product)
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
                
                <!-- Pagination -->
                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; text-align: center;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">No products found</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        Try adjusting your filters or search terms.
                    </p>
                    <a href="{{ route('products.index') }}" class="btn">View All Products</a>
                </div>
            @endif
        </div>
    </div>
@endsection