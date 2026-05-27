@extends('frontend.b2b.layouts.app')

@section('title', 'Claim Reward - E-Commerce Store')
@section('header', 'Claim Reward')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">Claim Reward</h2>
        <a href="{{ route('b2b.rewards.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem; border: 2px solid #8B0000; color: #8B0000;">Back to Rewards</a>
    </div>
    
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; margin-bottom: 2rem;">
        @if($reward->type === 'product' && $reward->product && $reward->product->images->count() > 0)
            <img src="{{ asset('storage/' . $reward->product->images->first()->image_path) }}" 
                 alt="{{ $reward->name }}" 
                 style="width: 100%; height: 300px; object-fit: cover;">
        @elseif($reward->type === 'travel_package')
            <div style="width: 100%; height: 300px; background-color: #8B0000; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 600;">
                Travel Package
            </div>
        @else
            <div style="width: 100%; height: 300px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 1.5rem;">
                No Image Available
            </div>
        @endif
        
        <div style="padding: 1.5rem;">
            <h3 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0 0 1rem 0;">
                {{ $reward->name }}
            </h3>
            
            @if($reward->description)
                <p style="color: #6b7280; margin: 0 0 1.5rem 0;">
                    {{ $reward->description }}
                </p>
            @endif
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background-color: #f9fafb; border-radius: 0.375rem;">
                <div>
                    @if($reward->type === 'product')
                        <span style="background-color: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            Free Product
                        </span>
                    @elseif($reward->type === 'travel_package')
                        <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            Travel Package
                        </span>
                    @endif
                </div>
                
                <div style="font-weight: 600; color: #8B0000; font-size: 1.25rem;">
                    {{ $reward->required_points }} Points
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background-color: #f9fafb; border-radius: 0.375rem;">
                <div style="font-weight: 500; color: #1f2937;">
                    Your Current Points:
                </div>
                <div style="font-weight: 600; color: #8B0000; font-size: 1.25rem;">
                    {{ $customer->loyalty_points }} Points
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background-color: #dcfce7; border-radius: 0.375rem;">
                <div style="font-weight: 500; color: #1f2937;">
                    Points After Claim:
                </div>
                <div style="font-weight: 600; color: #166534; font-size: 1.25rem;">
                    {{ $customer->loyalty_points - $reward->required_points }} Points
                </div>
            </div>
            
            <form action="{{ route('b2b.rewards.claim.submit', $reward) }}" method="POST">
                @csrf
                
                <div style="margin-top: 2rem;">
                    <p style="color: #6b7280; margin: 0 0 1rem 0;">
                        By claiming this reward, {{ $reward->required_points }} points will be deducted from your account. This action cannot be undone.
                    </p>
                    
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('b2b.rewards.index') }}" class="btn btn-outline" style="flex: 1; padding: 0.75rem; text-align: center; border: 1px solid #d1d5db; color: #374151; text-decoration: none; border-radius: 0.375rem;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.75rem; background-color: #8B0000; color: white; border: none; border-radius: 0.375rem; font-weight: 500; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#A9A9A9'" 
                                onmouseout="this.style.backgroundColor='#8B0000'">
                            Confirm Claim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-outline {
    background-color: #f3f4f6;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid #e5e7eb;
}

.btn-outline:hover {
    background-color: #e5e7eb;
}

.btn-primary:hover {
    background-color: #6b0000;
}
</style>
@endsection