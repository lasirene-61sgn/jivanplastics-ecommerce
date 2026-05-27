@extends('frontend.b2b.layouts.app')

@section('title', 'Rewards - E-Commerce Store')
@section('header', 'Discount & Claim')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">Available Rewards</h2>
        <div style="text-align: right;">
            <p style="margin: 0; font-weight: 600; color: #1f2937;">Your Points: <span style="color: #8B0000;">{{ $customer->loyalty_points }}</span></p>
        </div>
    </div>
    
    @if($rewards->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach($rewards as $reward)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column;">
                    @if($reward->type === 'product' && $reward->product && $reward->product->images->count() > 0)
                        <img src="{{ asset('storage/' . $reward->product->images->first()->image_path) }}" 
                             alt="{{ $reward->name }}" 
                             style="width: 100%; height: 200px; object-fit: cover;">
                    @elseif($reward->type === 'travel_package')
                        <div style="width: 100%; height: 200px; background-color: #8B0000; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 600;">
                            Travel Package
                        </div>
                    @else
                        <div style="width: 100%; height: 200px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                            No Image Available
                        </div>
                    @endif
                    
                    <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0 0 0.5rem 0;">
                            {{ $reward->name }}
                        </h3>
                        
                        @if($reward->description)
                            <p style="color: #6b7280; margin: 0 0 1rem 0; flex-grow: 1;">
                                {{ Str::limit($reward->description, 100) }}
                            </p>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
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
                            
                            <div style="font-weight: 600; color: #8B0000;">
                                {{ $reward->required_points }} pts
                            </div>
                        </div>
                        
                        <div style="margin-top: auto;">
                            @if($customer->loyalty_points >= $reward->required_points)
                                @php
                                    $existingClaim = $customer->rewardClaims()->where('reward_id', $reward->id)->whereIn('status', ['pending', 'approved'])->first();
                                @endphp
                                
                                @if($existingClaim)
                                    <button disabled style="width: 100%; padding: 0.75rem; background-color: #e5e7eb; color: #6b7280; border: none; border-radius: 0.375rem; font-weight: 500; cursor: not-allowed;">
                                        Already Claimed
                                    </button>
                                @else
                                    <a href="{{ route('b2b.rewards.claim.form', $reward) }}" 
                                       style="display: block; width: 100%; padding: 0.75rem; background-color: #8B0000; color: white; text-align: center; text-decoration: none; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out;"
                                       onmouseover="this.style.backgroundColor='#A9A9A9'" 
                                       onmouseout="this.style.backgroundColor='#8B0000'">
                                        Claim Reward
                                    </a>
                                @endif
                            @else
                                <button disabled style="width: 100%; padding: 0.75rem; background-color: #f3f4f6; color: #9ca3af; border: none; border-radius: 0.375rem; font-weight: 500; cursor: not-allowed;">
                                    Not Enough Points
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 3rem; text-align: center;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">No Rewards Available</h3>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">Check back later for new rewards and promotions.</p>
        </div>
    @endif
</div>
@endsection