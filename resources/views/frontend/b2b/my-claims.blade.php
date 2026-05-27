@extends('frontend.b2b.layouts.app')

@section('title', 'My Claims - E-Commerce Store')
@section('header', 'My Claims')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">My Reward Claims</h2>
        <a href="{{ route('b2b.rewards.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem; border: 2px solid #8B0000; color: #8B0000;">View Rewards</a>
    </div>
    
    @if($claims->count() > 0)
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #4b5563;">Reward</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Type</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Points</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Status</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Evidence</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Claimed On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($claims as $claim)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center;">
                                        @if($claim->reward->type === 'product' && $claim->reward->product && $claim->reward->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $claim->reward->product->images->first()->image_path) }}" 
                                                 alt="{{ $claim->reward->name }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem; margin-right: 1rem;">
                                        @else
                                            <div style="width: 60px; height: 60px; background-color: #e5e7eb; border-radius: 0.25rem; margin-right: 1rem; display: flex; align-items: center; justify-content: center;">
                                                @if($claim->reward->type === 'travel_package')
                                                    <span style="font-size: 0.75rem; text-align: center;">Travel</span>
                                                @else
                                                    <span style="font-size: 0.75rem;">No Img</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div>
                                            <h5 style="font-size: 1rem; font-weight: 500; color: #1f2937; margin: 0;">
                                                {{ $claim->reward->name }}
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    @if($claim->reward->type === 'product')
                                        <span style="background-color: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                                            Product
                                        </span>
                                    @elseif($claim->reward->type === 'travel_package')
                                        <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                                            Travel
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280; font-weight: 600;">
                                    {{ $claim->reward->required_points }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($claim->status === 'pending')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #fef3c7; color: #92400e;">
                                            Pending
                                        </span>
                                    @elseif($claim->status === 'approved')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #d1fae5; color: #065f46;">
                                            Approved
                                        </span>
                                    @elseif($claim->status === 'rejected')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #fee2e2; color: #991b1b;">
                                            Rejected
                                        </span>
                                    @elseif($claim->status === 'fulfilled')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #d1fae5; color: #065f46;">
                                            Fulfilled
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        @if($claim->dispatch_proof_image)
                                            <a href="{{ asset('storage/' . $claim->dispatch_proof_image) }}" target="_blank" title="Dispatch Proof" style="color: #059669;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                            </a>
                                        @endif
                                        @if($claim->invoice_id)
                                            <a href="{{ route('b2b.reward-claims.invoice', $claim) }}" target="_blank" title="System Invoice" style="color: #2563eb;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        @elseif($claim->invoice_proof_image)
                                            <a href="{{ asset('storage/' . $claim->invoice_proof_image) }}" target="_blank" title="Invoice Proof" style="color: #6b7280;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    {{ $claim->claimed_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($claims->hasPages())
                <div style="margin-top: 1.5rem;">
                    {{ $claims->links() }}
                </div>
            @endif
        </div>
    @else
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 3rem; text-align: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">No Claims Yet</h3>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">You haven't claimed any rewards yet.</p>
            <a href="{{ route('b2b.rewards.index') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; background-color: #8B0000; color: white; text-decoration: none; border-radius: 0.375rem;">View Available Rewards</a>
        </div>
    @endif
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

.btn-primary {
    background-color: #8B0000;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    border: none;
}

.btn-primary:hover {
    background-color: #6b0000;
}
</style>
@endsection