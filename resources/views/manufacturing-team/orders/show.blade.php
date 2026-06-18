<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 24px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-allocated {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .badge-processing {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .badge-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .badge-rejected {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #FF9800, #EF6C00);
            color: white;
        }

        .btn-warning:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #F44336, #C62828);
            color: white;
        }

        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        address {
            font-style: normal;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 20px;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Details - {{ $order->order_number }}</h1>
            <a href="{{ route('manufacturing-team.dashboard') }}" class="back-btn">Back to Dashboard</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif
        
        <div class="card">
            <h3 class="card-title">Order Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Order Number</div>
                        <div class="info-value">{{ $order->order_number }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Order Date</div>
                        <div class="info-value">{{ $order->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Customer Name</div>
                        <div class="info-value">{{ $order->customer->name }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Customer Type</div>
                        <div class="info-value">{{ ucfirst($order->customer_type) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Manufacturing Status</div>
                        <div class="info-value">
                            <span class="badge badge-{{ $order->manufacturing_status }}">
                                {{ ucfirst($order->manufacturing_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Allocated At</div>
                        <div class="info-value">{{ $order->allocated_at ? $order->allocated_at->format('M d, Y H:i') : 'N/A' }}</div>
                    </div>
                    @if($order->tentative_dispatch_date)
                    <div class="info-group">
                        <div class="info-label">Tentative Dispatch Date</div>
                        <div class="info-value" style="color: #667eea; font-weight: bold;">{{ \Carbon\Carbon::parse($order->tentative_dispatch_date)->format('M d, Y') }}</div>
                    </div>
                    @endif
                    @if($order->completed_at)
                    <div class="info-group">
                        <div class="info-label">Completed At</div>
                        <div class="info-value">{{ $order->completed_at->format('M d, Y H:i') }}</div>
                    </div>
                    @endif
                    @if($order->dispatched_at)
                    <div class="info-group">
                        <div class="info-label">Dispatched At</div>
                        <div class="info-value">{{ $order->dispatched_at->format('M d, Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3 class="card-title">Customer Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Name</div>
                        <div class="info-value">{{ $order->customer->name }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $order->customer->email }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $order->customer->phone }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Billing Address</div>
                        <address class="info-value">
                            {{ $order->billing_address }}<br>
                            {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}<br>
                            {{ $order->billing_country }}
                        </address>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3 class="card-title">Order Items</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Ordered</th>
                            <th>Manufactured</th>
                            <th>Rejected</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if($item->size || $item->thickness || $item->color)
                                        <div style="font-size: 10px; color: #4338ca; margin-top: 4px; font-weight: 700; text-transform: uppercase;">
                                            @if($item->size) Size: {{ $item->size }} @endif
                                            @if($item->thickness) | Thk: {{ $item->thickness }} @endif
                                            @if($item->color) | Color: {{ $item->color }} @endif
                                        </div>
                                    @endif
                                    <div style="font-size: 11px; color: #666; font-style: italic;">
                                        ({{ $item->per_unit_pieces }} pcs per unit)
                                    </div>
                                </td>
                                <td>{{ $item->product_sku }}</td>
                                <td>
                                    <strong>{{ $item->total_pieces }} pcs</strong>
                                    <div style="font-size: 11px; color: #888; font-style: italic;">= {{ $item->quantity }} units</div>
                                </td>
                                <td style="color: #28a745; font-weight: 700;">
                                    {{ $item->manufactured_pieces }} pcs
                                    <div style="font-size: 11px; color: #888; font-style: italic; font-weight: normal;">
                                        @if($item->per_unit_pieces > 0)
                                            = {{ number_format($item->manufactured_pieces / $item->per_unit_pieces, 2) }} units
                                        @endif
                                    </div>
                                </td>
                                <td style="color: #dc3545;">
                                    {{ $item->rejected_pieces }} pcs
                                    <div style="font-size: 11px; color: #888; font-style: italic;">
                                        @if($item->per_unit_pieces > 0)
                                            = {{ number_format($item->rejected_pieces / $item->per_unit_pieces, 2) }} units
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($item->manufacturing_pending_pieces > 0)
                                        <span class="badge badge-warning">{{ $item->manufacturing_pending_pieces }} pcs pending</span>
                                        <div style="font-size: 11px; color: #888; font-style: italic; margin-top: 4px;">
                                            @if($item->per_unit_pieces > 0)
                                                = {{ number_format($item->manufacturing_pending_pieces / $item->per_unit_pieces, 2) }} units
                                            @endif
                                        </div>
                                    @elseif($item->rejected_pieces > 0 && $item->manufactured_pieces > 0)
                                        <span class="badge badge-success" style="background: rgba(111, 66, 193, 0.2); color: #6f42c1; border-color: rgba(111, 66, 193, 0.3);">Closed (Shortfall)</span>
                                    @elseif($item->rejected_pieces > 0 && $item->manufactured_pieces == 0)
                                        <span class="badge badge-rejected">Fully Rejected</span>
                                    @else
                                        <span class="badge badge-completed">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <h3 class="card-title">Log Manufacturing Progress</h3>
            @if($order->manufacturing_status == 'processing')
                <form action="{{ route('manufacturing-team.orders.partial-complete', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>How many pieces have been processed now?</label>
                        @foreach($order->items as $item)
                        @if($item->has_manufacturing_pending)
                                <div class="form-group" style="background: #fdfdfd; padding: 15px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 15px;">
                                    <label style="display: block; font-weight: 700; color: #333; margin-bottom: 8px;">
                                        {{ $item->product_name }}
                                        @if($item->size || $item->thickness || $item->color)
                                            <span style="font-size: 10px; color: #4338ca; display: block; text-transform: uppercase;">
                                                @if($item->size) Size: {{ $item->size }} @endif
                                                @if($item->thickness) | Thk: {{ $item->thickness }} @endif
                                                @if($item->color) | Color: {{ $item->color }} @endif
                                            </span>
                                        @endif
                                        <span style="color: #666; font-weight: normal; float: right;">
                                            Pending: <strong>{{ $item->manufacturing_pending_pieces }} pcs</strong>
                                            <em style="font-size: 11px; color: #aaa;">(= {{ number_format($item->manufacturing_pending_pieces / max($item->per_unit_pieces, 1), 2) }} units)</em>
                                        </span>
                                    </label>
                                    <div style="display: flex; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label style="font-size: 12px; color: #666; font-weight: 600;">Completed Pieces</label>
                                            <input type="text" 
                                                   name="completed_pieces[{{ $item->id }}]" 
                                                   class="form-control" 
                                                   min="0" 
                                                   max="{{ $item->manufacturing_pending_pieces }}" 
                                                   value="0"
                                                   placeholder="Pieces">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="font-size: 12px; color: #dc3545; font-weight: 600;">Rejected Pieces</label>
                                            <input type="text" 
                                                   name="rejected_pieces[{{ $item->id }}]" 
                                                   class="form-control" 
                                                   min="0" 
                                                   max="{{ $item->manufacturing_pending_pieces }}" 
                                                   value="0"
                                                   placeholder="Pieces"
                                                   style="border-color: #dc3545;">
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <input type="text" 
                                               name="rejection_reasons[{{ $item->id }}]" 
                                               class="form-control" 
                                               placeholder="Reason for rejection (optional)"
                                               style="font-size: 14px;">
                                    </div>
                                    <small style="color: #888; display: block; margin-top: 5px;">
                                        Total pieces (Completed + Rejected) cannot exceed pending pieces ({{ $item->manufacturing_pending_pieces }}).
                                    </small>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px;">Submit Manufacturing Progress (In Pieces)</button>
                </form>
            @else
                <p style="color: #666; font-style: italic;">Order must be in 'Processing' status to log manufacturing progress.</p>
            @endif
        </div>

        {{-- ===== EDIT PERMISSION SECTION ===== --}}
        @if($order->manufacturing_status == 'processing')
        <div class="card" style="border: 2px solid {{ $order->mfg_edit_permission_granted ? '#28a745' : ($order->mfg_edit_request_note ? '#ffc107' : '#dee2e6') }};">

            {{-- Permission is ACTIVE — show correction form --}}
            @if($order->mfg_edit_permission_granted)
                <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border-radius: 10px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">✅</span>
                    <div>
                        <strong style="color: #155724; font-size: 16px;">Admin has granted you edit permission!</strong>
                        <p style="color: #155724; margin: 4px 0 0; font-size: 13px;">You can now correct your manufactured and rejected piece counts below. This permission will be consumed once you submit.</p>
                    </div>
                </div>

                <h3 class="card-title" style="color: #155724;">✏️ Correct Piece Counts</h3>

                <form action="{{ route('manufacturing-team.orders.submit-correction', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" style="color: #dc3545;">* Reason for correction (required)</label>
                        <input type="text" name="correction_reason" class="form-control" placeholder="Briefly explain what you are correcting..." required style="border-color: #dc3545;">
                    </div>

                    @foreach($order->items as $item)
                    <div class="form-group" style="background: #f8fff8; padding: 15px; border-radius: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">
                        <label style="display: block; font-weight: 700; color: #333; margin-bottom: 12px; font-size: 14px;">
                            {{ $item->product_name }}
                            @if($item->size || $item->thickness || $item->color)
                                <span style="font-size: 10px; color: #4338ca; display: block; text-transform: uppercase; font-weight: 600;">
                                    @if($item->size) Size: {{ $item->size }} @endif
                                    @if($item->thickness) | Thk: {{ $item->thickness }} @endif
                                    @if($item->color) | Color: {{ $item->color }} @endif
                                </span>
                            @endif
                            <span style="float: right; font-size: 12px; color: #666; font-weight: normal;">
                                Ordered: <strong>{{ $item->total_pieces }} pcs</strong> ({{ $item->quantity }} units)
                            </span>
                        </label>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label style="font-size: 12px; color: #28a745; font-weight: 700;">
                                    ✅ Correct Manufactured Pieces
                                    <span style="color: #aaa; font-weight: normal;">(currently: {{ $item->manufactured_pieces }} pcs)</span>
                                </label>
                                <input type="number" 
                                       name="corrected_manufactured_pieces[{{ $item->id }}]" 
                                       class="form-control" 
                                       min="0" 
                                       max="{{ $item->total_pieces }}"
                                       value="{{ $item->manufactured_pieces }}"
                                       style="border-color: #28a745; font-size: 18px; font-weight: 700;">
                                <small style="color: #888;">= {{ number_format($item->manufactured_pieces / max($item->per_unit_pieces, 1), 2) }} units currently</small>
                            </div>
                            <div style="flex: 1;">
                                <label style="font-size: 12px; color: #dc3545; font-weight: 700;">
                                    ❌ Correct Rejected Pieces
                                    <span style="color: #aaa; font-weight: normal;">(currently: {{ $item->rejected_pieces }} pcs)</span>
                                </label>
                                <input type="number" 
                                       name="corrected_rejected_pieces[{{ $item->id }}]" 
                                       class="form-control" 
                                       min="0" 
                                       max="{{ $item->total_pieces }}"
                                       value="{{ $item->rejected_pieces }}"
                                       style="border-color: #dc3545; font-size: 18px; font-weight: 700;">
                                <small style="color: #888;">= {{ number_format($item->rejected_pieces / max($item->per_unit_pieces, 1), 2) }} units currently</small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                        <strong style="color: #856404;">⚠️ Important:</strong>
                        <span style="color: #856404; font-size: 13px;"> These values REPLACE your existing totals — enter the correct final counts, not additional amounts. This permission will be used up after you submit.</span>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 16px;" onclick="return confirm('Are you sure? This will replace your current piece counts and consume your edit permission.')">
                        ✅ Submit Correction (Uses 1 Permission)
                    </button>
                </form>

            {{-- Request pending — waiting for admin approval --}}
            @elseif($order->mfg_edit_request_note && !$order->mfg_edit_permission_granted)
                <h3 class="card-title">Request Edit Permission</h3>
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                    <strong style="color: #856404;">⏳ Edit Request Pending Admin Approval</strong>
                    <p style="color: #856404; margin: 8px 0 0; font-size: 13px;">Your request has been sent to admin. Please wait for their approval.</p>
                    <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px; font-size: 13px; color: #555; font-style: italic;">
                        "{{ $order->mfg_edit_request_note }}"
                        <span style="display: block; font-size: 11px; color: #aaa; margin-top: 4px;">Sent: {{ $order->mfg_edit_request_at?->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                <div style="text-align: center; font-size: 13px; color: #888;">
                    Edit permissions used: <strong>{{ $order->mfg_edit_permission_count }}/2</strong>
                </div>

            {{-- No active request — show request form (if not exhausted) --}}
            @elseif($order->mfg_edit_permission_count < 2)
                <h3 class="card-title">Request Edit Permission</h3>
                <p style="color: #666; font-size: 13px; margin-bottom: 15px;">
                    If you entered pieces incorrectly, you can request admin permission to correct them. 
                    Admin can grant this <strong>up to 2 times per order</strong>.
                    Remaining: <strong>{{ 2 - $order->mfg_edit_permission_count }}</strong> permission(s).
                </p>
                <form action="{{ route('manufacturing-team.orders.request-edit', $order) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Explain what was entered incorrectly *</label>
                        <textarea name="edit_request_note" class="form-control" rows="3" required maxlength="500"
                                  placeholder="e.g. I accidentally entered 50 manufactured pieces instead of 30. The correct values are 30 manufactured and 5 rejected."></textarea>
                        <small style="color: #888;">This note will be visible to the admin.</small>
                    </div>
                    <button type="submit" class="btn btn-warning" style="width: 100%;">Send Edit Request to Admin</button>
                </form>

            {{-- Exhausted — no more permissions --}}
            @else
                <h3 class="card-title" style="color: #dc3545;">Edit Permissions Exhausted</h3>
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px;">
                    <strong style="color: #721c24;">⛔ No More Edit Permissions Available</strong>
                    <p style="color: #721c24; margin: 8px 0 0; font-size: 13px;">You have used both edit permissions (2/2) for this order. If there is still an issue, please contact admin directly.</p>
                </div>
            @endif
        </div>
        @endif
        
        @if(!in_array($order->manufacturing_status, ['completed', 'rejected']))
        <div class="card">
            <h3 class="card-title">Update Manufacturing Status</h3>
            <form action="{{ route('manufacturing-team.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="manufacturing_status" class="form-label">Select New Status</label>
                    @php
                        $hasPendingManufacturing = $order->items->sum('manufacturing_pending_quantity') > 0;
                    @endphp
                    <select name="manufacturing_status" id="manufacturing_status" class="form-control" required>
                        <option value="">Select status</option>
                        <option value="processing" {{ $order->manufacturing_status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->manufacturing_status == 'completed' ? 'selected' : '' }} {{ $hasPendingManufacturing ? 'disabled' : '' }}>Completed {{ $hasPendingManufacturing ? '(Process all items first)' : '' }}</option>
                        <option value="rejected" {{ $order->manufacturing_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @if($hasPendingManufacturing)
                        <small class="text-danger" style="color: #dc3545; display: block; margin-top: 5px;">
                            * You cannot mark this order as Completed until all item quantities are logged (Manufactured or Rejected).
                        </small>
                    @endif
                </div>
                
                <div class="form-group" id="tentativeDateGroup" style="{{ $order->manufacturing_status == 'allocated' ? 'display: none;' : 'display: none;' }}">
                    <label for="tentative_dispatch_date" class="form-label">Tentative Dispatch Date</label>
                    <input type="date" name="tentative_dispatch_date" id="tentative_dispatch_date" class="form-control" value="{{ $order->tentative_dispatch_date }}" min="{{ date('Y-m-d') }}">
                    <small style="color: #666; display: block; margin-top: 5px;">Required when accepting an order (Processing status).</small>
                </div>
                
                <script>
                    document.getElementById('manufacturing_status').addEventListener('change', function() {
                        const dateGroup = document.getElementById('tentativeDateGroup');
                        const dateInput = document.getElementById('tentative_dispatch_date');
                        if (this.value === 'processing') {
                            dateGroup.style.display = 'block';
                            dateInput.required = true;
                        } else {
                            dateGroup.style.display = 'none';
                            dateInput.required = false;
                        }
                    });
                    
                    // Trigger on load if it's already processing or allocated
                    if (document.getElementById('manufacturing_status').value === 'processing') {
                        document.getElementById('tentativeDateGroup').style.display = 'block';
                    }
                </script>

                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
        @else
        <div class="card">
            <h3 class="card-title">Manufacturing Status</h3>
            <div class="alert alert-info">
                This order has been marked as <strong>{{ ucfirst($order->manufacturing_status) }}</strong>. The status can no longer be changed.
            </div>
        </div>
        @endif
    </div>
</body>
</html>
