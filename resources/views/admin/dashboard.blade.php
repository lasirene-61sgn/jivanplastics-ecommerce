@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . config('app.name', 'Laravel'))

@section('header', 'Welcome, ' . Auth::guard('admin')->user()->name . '!')

@section('content')
    <div class="stats-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Total Users (Individuals) -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #8B0000;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #8B0000; margin-bottom: 0.5rem;">{{ $totalUsers }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Total Users</div>
        </div>

        <!-- Total Dealers -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #8B0000;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #8B0000; margin-bottom: 0.5rem;">{{ $totalDealers }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Total Dealers</div>
        </div>
        
        <!-- Total Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #8B0000;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #8B0000; margin-bottom: 0.5rem;">{{ $totalOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Total Orders</div>
        </div>

        <!-- Completed Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #28a745;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #28a745; margin-bottom: 0.5rem;">{{ $completedOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Completed Orders</div>
        </div>

        <!-- Processing Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #ffc107;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #ffc107; margin-bottom: 0.5rem;">{{ $processingOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Processing Orders</div>
        </div>

        <!-- Rejected Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #dc3545;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #dc3545; margin-bottom: 0.5rem;">{{ $rejectedOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Rejected Orders</div>
        </div>

        <!-- Manufacturing Team -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #17a2b8;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #17a2b8; margin-bottom: 0.5rem;">{{ $manufacturingTeamCount }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Manufacturing Team</div>
        </div>

        <!-- Sales Team -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #6610f2;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #6610f2; margin-bottom: 0.5rem;">{{ $salesTeamCount }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Sales Team</div>
        </div>

        <!-- B2B Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #fd7e14;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #fd7e14; margin-bottom: 0.5rem;">{{ $b2bOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">B2B Orders</div>
        </div>

        <!-- B2C Orders -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #20c997;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #20c997; margin-bottom: 0.5rem;">{{ $b2cOrders }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">B2C Orders</div>
        </div>

        <!-- Pending Return Requests -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #f59e0b;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #f59e0b; margin-bottom: 0.5rem;">{{ $pendingReturnRequests }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Pending Returns</div>
        </div>

        <!-- Completed Return Requests -->
        <div class="stat-card" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); padding: 1.5rem; text-align: center; border-top: 4px solid #10b981;">
            <div class="stat-value" style="font-size: 2rem; font-weight: 600; color: #10b981; margin-bottom: 0.5rem;">{{ $completedReturnRequests }}</div>
            <div class="stat-label" style="font-size: 1rem; color: #6b7280;">Completed Returns</div>
        </div>
        
    </div>
    
    <div class="content-section" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
        <h2 class="section-title" style="font-size: 1.25rem; font-weight: 600; color: #111827; margin-bottom: 1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem;">Latest Orders</h2>
        
        @if($latestOrders->count() > 0)
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f3f4f6; text-align: left;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Order No</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Customer</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Total</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Date</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestOrders as $order)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #111827;">{{ $order->order_number }}</td>
                                <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #111827;">{{ $order->customer ? $order->customer->name : 'N/A' }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; 
                                        @if($order->status == 'completed') background-color: #d1fae5; color: #065f46; 
                                        @elseif($order->status == 'processing') background-color: #fef3c7; color: #92400e; 
                                        @elseif($order->status == 'cancelled' || $order->status == 'rejected') background-color: #fee2e2; color: #991b1b; 
                                        @else background-color: #e5e7eb; color: #374151; @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #111827;">₹{{ number_format($order->total, 2) }}</td>
                                <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">{{ $order->created_at->format('M d, Y') }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" style="color: #8B0000; font-weight: 500; font-size: 0.875rem; text-decoration: none;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: #6b7280; padding: 2rem;">No recent orders found.</p>
        @endif
    </div>
@endsection