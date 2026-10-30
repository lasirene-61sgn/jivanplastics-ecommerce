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

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .rejection-badge-box {
            margin-top: 6px;
            padding: 6px 10px;
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-radius: 6px;
            font-size: 11px;
            color: #c53030;
            text-align: left;
            line-height: 1.4;
            word-break: break-word;
        }

        .rejection-badge-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            color: #9b2c2c;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 2px;
        }

        address {
            font-style: normal;
            line-height: 1.6;
        }

        .unit-badge {
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
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

        @if(session('error'))
            <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">❌</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $rejectedItemsList = $order->items->where('status', 'accepted')->filter(function($i) {
                return $i->rejected_pieces > 0 || !empty($i->rejection_reason);
            });
        @endphp
        @if($rejectedItemsList->isNotEmpty())
            <div class="alert alert-danger" style="background: #fff5f5; border: 1px solid #fecaca; border-left: 6px solid #dc3545; border-radius: 8px; padding: 16px; margin-bottom: 25px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <span style="font-size: 24px; line-height: 1;">⚠️</span>
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 6px 0; color: #991b1b; font-size: 16px; font-weight: 700;">Rejection Logged For This Order</h4>
                        <p style="margin: 0 0 10px 0; color: #7f1d1d; font-size: 13px;">The following items have rejected quantities and reasons recorded:</p>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($rejectedItemsList as $rejItem)
                                <div style="background: white; border: 1px solid #fca5a5; border-radius: 6px; padding: 10px 14px; font-size: 13px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                                        <strong style="color: #1f2937;">{{ $rejItem->product_name }}</strong>
                                        <span style="background: #fee2e2; color: #b91c1c; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                            ❌ {{ $rejItem->rejected_pieces }} pcs rejected
                                            @if($rejItem->per_unit_pieces > 0)
                                                ({{ number_format($rejItem->rejected_pieces / $rejItem->per_unit_pieces, 2) }} units)
                                            @endif
                                        </span>
                                    </div>
                                    @if($rejItem->rejection_reason)
                                    <div style="margin-top: 6px; font-size: 12px; color: #b91c1c; background: #fff1f2; padding: 6px 10px; border-radius: 4px; border: 1px solid #fecdd3;">
                                        <strong>Rejection Message:</strong> {{ $rejItem->rejection_reason }}
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
                        <div class="info-label">Allocated At (Asia/Kolkata)</div>
                        <div class="info-value">{{ $order->allocated_at ? $order->allocated_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') : 'N/A' }}</div>
                    </div>
                    @if($order->tentative_dispatch_date)
                    <div class="info-group">
                        <div class="info-label">Tentative Dispatch Date (Asia/Kolkata)</div>
                        <div class="info-value" style="color: #667eea; font-weight: bold;">
                            {{ \Carbon\Carbon::parse($order->tentative_dispatch_date)->timezone('Asia/Kolkata')->format('M d, Y') }}
                        </div>
                    </div>
                    @endif
                    @if($order->completed_at)
                    <div class="info-group">
                        <div class="info-label">Completed At (Asia/Kolkata)</div>
                        <div class="info-value">{{ $order->completed_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') }}</div>
                    </div>
                    @endif
                    @if($order->dispatched_at)
                    <div class="info-group">
                        <div class="info-label">Dispatched At (Asia/Kolkata)</div>
                        <div class="info-value">{{ $order->dispatched_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') }}</div>
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
            <h3 class="card-title">Allocated Order Items <span style="font-size: 12px; color: #059669; font-weight: normal; margin-left: 8px;">(Only Admin-Accepted Items Allocated)</span></h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Details</th>
                            <th>SKU</th>
                            <th>Ordered</th>
                            <th>Manufactured</th>
                            <th>Rejected</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items->where('status', 'accepted') as $item)
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
                                    <div style="margin-top: 4px;">
                                        <span class="unit-badge">1 Unit = {{ $item->per_unit_pieces ?? 1 }} pcs</span>
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
                                <td style="color: #dc3545; vertical-align: top;">
                                    @if($item->rejected_pieces > 0)
                                        <strong>{{ $item->rejected_pieces }} pcs</strong>
                                        <div style="font-size: 11px; color: #888; font-style: italic;">
                                            @if($item->per_unit_pieces > 0)
                                                = {{ number_format($item->rejected_pieces / $item->per_unit_pieces, 2) }} units
                                            @endif
                                        </div>
                                        @if($item->rejection_reason)
                                            <div class="rejection-badge-box">
                                                <div class="rejection-badge-title">
                                                    <span>⚠️ Reason:</span>
                                                </div>
                                                <div>{{ $item->rejection_reason }}</div>
                                            </div>
                                        @endif
                                    @elseif(!empty($item->rejection_reason))
                                        <span style="color: #666; font-weight: 500;">0 pcs</span>
                                        <div class="rejection-badge-box">
                                            <div class="rejection-badge-title">
                                                <span>⚠️ Reason:</span>
                                            </div>
                                            <div>{{ $item->rejection_reason }}</div>
                                        </div>
                                    @else
                                        <span style="color: #aaa;">0 pcs</span>
                                    @endif
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
                <form action="{{ route('manufacturing-team.orders.partial-complete', $order) }}" method="POST" id="mfgProgressForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label style="font-weight: 600; margin-bottom: 12px; display: block;">How many pieces have been processed now?</label>
                        @foreach($order->items->where('status', 'accepted') as $item)
                        @if($item->has_manufacturing_pending)
                            @php
                                $perUnit = max($item->per_unit_pieces ?? 1, 1);
                            @endphp
                            <div class="form-group mfg-item-card" id="mfg_card_{{ $item->id }}" data-item-id="{{ $item->id }}" data-pending="{{ $item->manufacturing_pending_pieces }}" data-per-unit="{{ $perUnit }}" data-name="{{ $item->product_name }}" style="background: #fdfdfd; padding: 18px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 18px; transition: all 0.3s ease;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <div>
                                        <strong style="color: #1f2937; font-size: 15px;">{{ $item->product_name }}</strong>
                                        <div style="margin-top: 3px;">
                                            <span class="unit-badge">1 Unit = {{ $perUnit }} Pcs</span>
                                        </div>
                                        @if($item->size || $item->thickness || $item->color)
                                            <span style="font-size: 10px; color: #4338ca; display: block; text-transform: uppercase; margin-top: 4px; font-weight: 600;">
                                                @if($item->size) Size: {{ $item->size }} @endif
                                                @if($item->thickness) | Thk: {{ $item->thickness }} @endif
                                                @if($item->color) | Color: {{ $item->color }} @endif
                                            </span>
                                        @endif
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 12px; color: #64748b;">
                                            Ordered: <strong>{{ $item->total_pieces }} pcs</strong> | Made: <strong style="color: #16a34a;">{{ $item->manufactured_pieces }}</strong> | Rej: <strong style="color: #dc2626;">{{ $item->rejected_pieces }}</strong>
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="color: #475569; font-size: 13px;">
                                                Pending to log: <strong style="color: #ea580c; font-size: 15px;" id="pending_val_{{ $item->id }}">{{ $item->manufacturing_pending_pieces }} pcs</strong>
                                            </span>
                                            <div style="font-size: 11px; color: #888; font-style: italic;">
                                                (= {{ number_format($item->manufacturing_pending_pieces / $perUnit, 2) }} units)
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($item->rejection_reason)
                                    <div style="margin-bottom: 12px; font-size: 12px; color: #b91c1c; background: #fff5f5; border: 1px dashed #fca5a5; border-radius: 6px; padding: 6px 10px;">
                                        <strong>Previous Rejection Message:</strong> {{ $item->rejection_reason }}
                                    </div>
                                @endif

                                <div style="display: flex; gap: 15px;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 12px; color: #16a34a; font-weight: 700; display: block; margin-bottom: 4px;">
                                            ✅ Completed Pieces
                                        </label>
                                        <input type="number" 
                                               name="completed_pieces[{{ $item->id }}]" 
                                               id="completed_{{ $item->id }}"
                                               class="form-control mfg-comp-input" 
                                               min="0" 
                                               max="{{ $item->manufacturing_pending_pieces }}" 
                                               value="0"
                                               placeholder="Pieces"
                                               data-item-id="{{ $item->id }}"
                                               style="font-size: 16px; font-weight: 600;">
                                        <small style="color: #888; display: block; margin-top: 4px;">
                                            = <span id="comp_unit_{{ $item->id }}">0.00</span> units
                                        </small>
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="font-size: 12px; color: #dc2626; font-weight: 700; display: block; margin-bottom: 4px;">
                                            ❌ Rejected Pieces
                                        </label>
                                        <input type="number" 
                                               name="rejected_pieces[{{ $item->id }}]" 
                                               id="rejected_{{ $item->id }}"
                                               class="form-control mfg-rej-input" 
                                               min="0" 
                                               max="{{ $item->manufacturing_pending_pieces }}" 
                                               value="0"
                                               placeholder="Pieces"
                                               data-item-id="{{ $item->id }}"
                                               style="border-color: #f87171; font-size: 16px; font-weight: 600;">
                                        <small style="color: #888; display: block; margin-top: 4px;">
                                            = <span id="rej_unit_{{ $item->id }}">0.00</span> units
                                        </small>
                                    </div>
                                </div>

                                {{-- Live dynamic alert / status badge --}}
                                <div id="live_status_{{ $item->id }}" style="display: none; margin-top: 10px; padding: 8px 12px; border-radius: 6px; font-size: 12px; line-height: 1.4;"></div>

                                {{-- Rejection reason group --}}
                                <div id="rejection_group_{{ $item->id }}" style="margin-top: 10px;">
                                    <label style="font-size: 12px; color: #dc2626; font-weight: 700; display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                        <span>
                                            ⚠️ Rejection Message / Reason 
                                            <span id="rej_star_{{ $item->id }}" style="display: none; color: #dc2626;">* (Required for rejected pieces)</span>
                                        </span>
                                    </label>
                                    <input type="text" 
                                           name="rejection_reasons[{{ $item->id }}]" 
                                           id="rejection_reason_{{ $item->id }}"
                                           class="form-control mfg-reason-input" 
                                           placeholder="Enter reason why pieces were rejected (e.g. Dimensions incorrect, Cracks, Surface defect...)"
                                           data-item-id="{{ $item->id }}"
                                           style="font-size: 13px; border-color: #cbd5e1;">
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 11px; color: #64748b;">
                                    <span>Total to process cannot exceed pending pieces ({{ $item->manufacturing_pending_pieces }} pcs).</span>
                                    <span id="sum_counter_{{ $item->id }}" style="font-weight: 600; color: #475569;">Sum: 0 / {{ $item->manufacturing_pending_pieces }} pcs</span>
                                </div>
                            </div>
                        @endif
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success" id="mfgSubmitBtn" style="width: 100%; padding: 15px; font-size: 16px;">Submit Manufacturing Progress (In Pieces)</button>
                </form>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('mfgProgressForm');
                    const submitBtn = document.getElementById('mfgSubmitBtn');
                    const cards = document.querySelectorAll('.mfg-item-card');

                    function checkItem(card) {
                        const itemId = card.dataset.itemId;
                        const pending = parseInt(card.dataset.pending, 10) || 0;
                        const perUnit = parseFloat(card.dataset.perUnit) || 1;
                        const name = card.dataset.name;

                        const compInput = document.getElementById('completed_' + itemId);
                        const rejInput = document.getElementById('rejected_' + itemId);
                        const reasonInput = document.getElementById('rejection_reason_' + itemId);
                        const statusBox = document.getElementById('live_status_' + itemId);
                        const star = document.getElementById('rej_star_' + itemId);
                        const sumCounter = document.getElementById('sum_counter_' + itemId);
                        const compUnitSpan = document.getElementById('comp_unit_' + itemId);
                        const rejUnitSpan = document.getElementById('rej_unit_' + itemId);

                        const compVal = parseInt(compInput.value, 10) || 0;
                        const rejVal = parseInt(rejInput.value, 10) || 0;
                        const total = compVal + rejVal;

                        if (compUnitSpan) compUnitSpan.innerText = (compVal / perUnit).toFixed(2);
                        if (rejUnitSpan) rejUnitSpan.innerText = (rejVal / perUnit).toFixed(2);
                        if (sumCounter) sumCounter.innerText = 'Sum: ' + total + ' / ' + pending + ' pcs';

                        // Check rejection reason requirement
                        if (rejVal > 0) {
                            star.style.display = 'inline';
                            reasonInput.style.borderColor = '#dc2626';
                            if (!reasonInput.value.trim()) {
                                reasonInput.style.backgroundColor = '#fff5f5';
                            } else {
                                reasonInput.style.backgroundColor = '#ffffff';
                            }
                        } else {
                            star.style.display = 'none';
                            reasonInput.style.borderColor = '#cbd5e1';
                            reasonInput.style.backgroundColor = '#ffffff';
                        }

                        // Check if sum exceeds pending
                        if (total > pending) {
                            card.style.borderColor = '#ef4444';
                            card.style.backgroundColor = '#fef2f2';
                            compInput.style.borderColor = '#ef4444';
                            rejInput.style.borderColor = '#ef4444';
                            statusBox.style.display = 'block';
                            statusBox.style.backgroundColor = '#fee2e2';
                            statusBox.style.border = '1px solid #f87171';
                            statusBox.style.color = '#991b1b';

                            if (compVal >= pending && rejVal > 0) {
                                statusBox.innerHTML = '⚠️ <strong>Exceeded!</strong> You have entered <strong>' + compVal + ' completed</strong> pieces (max is ' + pending + '). You cannot also add <strong>' + rejVal + ' rejected</strong> pieces. If ' + rejVal + ' pieces were rejected, reduce completed to <strong>' + Math.max(0, pending - rejVal) + '</strong>.';
                            } else {
                                statusBox.innerHTML = '⚠️ <strong>Exceeded!</strong> Completed (' + compVal + ') + Rejected (' + rejVal + ') = <strong>' + total + ' pcs</strong>, which exceeds ' + pending + ' pending pieces by <strong>' + (total - pending) + ' pcs</strong>! Total cannot exceed ' + pending + ' pcs.';
                            }
                            return { valid: false, error: 'Quantities for "' + name + '" exceed pending pieces (' + total + ' > ' + pending + ').' };
                        } else if (total === pending && pending > 0) {
                            card.style.borderColor = '#86efac';
                            card.style.backgroundColor = '#f0fdf4';
                            compInput.style.borderColor = '#22c55e';
                            rejInput.style.borderColor = rejVal > 0 ? '#ef4444' : '#cbd5e1';
                            statusBox.style.display = 'block';
                            statusBox.style.backgroundColor = '#dcfce7';
                            statusBox.style.border = '1px solid #86efac';
                            statusBox.style.color = '#166534';
                            statusBox.innerHTML = '✅ <strong>Full Batch:</strong> All ' + pending + ' pending pieces accounted for (' + compVal + ' completed, ' + rejVal + ' rejected). This item will be 100% finished!';
                        } else if (total > 0) {
                            card.style.borderColor = '#93c5fd';
                            card.style.backgroundColor = '#f8fafc';
                            compInput.style.borderColor = '#3b82f6';
                            rejInput.style.borderColor = rejVal > 0 ? '#ef4444' : '#cbd5e1';
                            statusBox.style.display = 'block';
                            statusBox.style.backgroundColor = '#eff6ff';
                            statusBox.style.border = '1px solid #bfdbfe';
                            statusBox.style.color = '#1e40af';
                            statusBox.innerHTML = 'ℹ️ Logging <strong>' + total + ' pcs</strong> (' + compVal + ' completed, ' + rejVal + ' rejected). <strong>' + (pending - total) + ' pcs</strong> will remain pending.';
                        } else {
                            card.style.borderColor = '#e2e8f0';
                            card.style.backgroundColor = '#fdfdfd';
                            compInput.style.borderColor = '#cbd5e1';
                            rejInput.style.borderColor = '#f87171';
                            statusBox.style.display = 'none';
                        }

                        if (rejVal > 0 && !reasonInput.value.trim()) {
                            return { valid: false, error: 'Please enter a rejection message for "' + name + '".', focusInput: reasonInput };
                        }

                        return { valid: true };
                    }

                    function checkAll() {
                        let allValid = true;
                        let anyInput = false;
                        let firstError = null;

                        cards.forEach(function(card) {
                            const compInput = card.querySelector('.mfg-comp-input');
                            const rejInput = card.querySelector('.mfg-rej-input');
                            const compVal = parseInt(compInput.value, 10) || 0;
                            const rejVal = parseInt(rejInput.value, 10) || 0;
                            if (compVal > 0 || rejVal > 0) {
                                anyInput = true;
                            }
                            const res = checkItem(card);
                            if (!res.valid) {
                                allValid = false;
                                if (!firstError) firstError = res;
                            }
                        });

                        if (submitBtn) {
                            if (!allValid) {
                                submitBtn.disabled = true;
                                submitBtn.style.opacity = '0.6';
                                submitBtn.style.cursor = 'not-allowed';
                            } else {
                                submitBtn.disabled = false;
                                submitBtn.style.opacity = '1';
                                submitBtn.style.cursor = 'pointer';
                            }
                        }

                        return { allValid: allValid, anyInput: anyInput, firstError: firstError };
                    }

                    cards.forEach(function(card) {
                        const compInput = card.querySelector('.mfg-comp-input');
                        const rejInput = card.querySelector('.mfg-rej-input');
                        const reasonInput = card.querySelector('.mfg-reason-input');

                        if (compInput) compInput.addEventListener('input', checkAll);
                        if (rejInput) rejInput.addEventListener('input', checkAll);
                        if (reasonInput) reasonInput.addEventListener('input', checkAll);
                    });

                    if (form) {
                        form.addEventListener('submit', function(e) {
                            const status = checkAll();
                            if (!status.anyInput) {
                                e.preventDefault();
                                alert('Please enter completed or rejected pieces (greater than 0) for at least one item before submitting.');
                                return false;
                            }
                            if (!status.allValid) {
                                e.preventDefault();
                                alert(status.firstError ? status.firstError.error : 'Please resolve validation errors before submitting.');
                                if (status.firstError && status.firstError.focusInput) {
                                    status.firstError.focusInput.focus();
                                }
                                return false;
                            }
                        });
                    }

                    // Initial check
                    checkAll();
                });
                </script>
            @else
                <p style="color: #666; font-style: italic;">Order must be in 'Processing' status to log manufacturing progress.</p>
            @endif
        </div>

        {{-- ===== PRODUCTION & REJECTION HISTORY LOG CARD ===== --}}
        <div class="card">
            <h3 class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>📋 Production & Rejection History Log</span>
                <span style="font-size: 12px; color: #666; font-weight: normal;">
                    {{ $order->productionLogs ? $order->productionLogs->count() : 0 }} entry(ies) recorded
                </span>
            </h3>
            @if($order->productionLogs && $order->productionLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date & Time (IST)</th>
                                <th>Item Details</th>
                                <th>Completed</th>
                                <th>Rejected</th>
                                <th>Rejection Message / Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->productionLogs->sortByDesc('created_at') as $log)
                                <tr>
                                    <td style="white-space: nowrap; font-size: 13px; color: #555;">
                                        {{ $log->created_at ? $log->created_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') : 'N/A' }}
                                    </td>
                                    <td>
                                        <strong>{{ $log->orderItem->product_name ?? 'Item #' . $log->order_item_id }}</strong>
                                        @if($log->orderItem)
                                            <div style="font-size: 11px; color: #666; margin-top: 2px;">
                                                SKU: {{ $log->orderItem->product_sku }}
                                                @if($log->orderItem->size || $log->orderItem->thickness || $log->orderItem->color)
                                                    <span style="font-size: 10px; color: #4338ca; text-transform: uppercase; margin-left: 5px;">
                                                        @if($log->orderItem->size) Size: {{ $log->orderItem->size }} @endif
                                                        @if($log->orderItem->thickness) | Thk: {{ $log->orderItem->thickness }} @endif
                                                        @if($log->orderItem->color) | Color: {{ $log->orderItem->color }} @endif
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td style="color: #28a745; font-weight: 700;">
                                        @if($log->pieces_completed > 0)
                                            +{{ $log->pieces_completed }} pcs
                                            @if($log->orderItem && $log->orderItem->per_unit_pieces > 0)
                                                <div style="font-size: 11px; color: #888; font-weight: normal;">
                                                    (= {{ number_format($log->pieces_completed / $log->orderItem->per_unit_pieces, 2) }} units)
                                                </div>
                                            @endif
                                        @else
                                            <span style="color: #aaa; font-weight: normal;">0 pcs</span>
                                        @endif
                                    </td>
                                    <td style="color: #dc3545; font-weight: 700;">
                                        @if($log->pieces_rejected > 0)
                                            +{{ $log->pieces_rejected }} pcs
                                            @if($log->orderItem && $log->orderItem->per_unit_pieces > 0)
                                                <div style="font-size: 11px; color: #888; font-style: italic; font-weight: normal;">
                                                    (= {{ number_format($log->pieces_rejected / $log->orderItem->per_unit_pieces, 2) }} units)
                                                </div>
                                            @endif
                                        @else
                                            <span style="color: #aaa; font-weight: normal;">0 pcs</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->pieces_rejected > 0 || !empty($log->rejection_reason))
                                            <div style="background: #fff5f5; border: 1px solid #fecaca; border-left: 3px solid #dc3545; border-radius: 6px; padding: 6px 10px; font-size: 12px; color: #b91c1c;">
                                                <strong style="display: block; font-size: 10px; text-transform: uppercase; color: #991b1b;">Rejection Message:</strong>
                                                {{ $log->rejection_reason ?: 'No message recorded' }}
                                            </div>
                                        @else
                                            <span style="color: #aaa; font-style: italic; font-size: 12px;">— None —</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #666; font-style: italic; font-size: 13px; margin: 10px 0;">No production entries have been logged for this order yet.</p>
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

                    @foreach($order->items->where('status', 'accepted') as $item)
                    @php
                        $perUnitCorr = max($item->per_unit_pieces ?? 1, 1);
                    @endphp
                    <div class="form-group" style="background: #f8fff8; padding: 15px; border-radius: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">
                        <label style="display: block; font-weight: 700; color: #333; margin-bottom: 12px; font-size: 14px;">
                            {{ $item->product_name }}
                            <span class="unit-badge" style="margin-left: 5px;">1 Unit = {{ $perUnitCorr }} Pcs</span>
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
                                       style="border-color: #28a745; font-size: 18px; font-weight: 700;"
                                       oninput="document.getElementById('corr_comp_{{ $item->id }}').innerText = (this.value / {{ $perUnitCorr }}).toFixed(2)">
                                <small style="color: #888;">
                                    = <span id="corr_comp_{{ $item->id }}">{{ number_format($item->manufactured_pieces / $perUnitCorr, 2) }}</span> units
                                </small>
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
                                       style="border-color: #dc3545; font-size: 18px; font-weight: 700;"
                                       oninput="document.getElementById('corr_rej_{{ $item->id }}').innerText = (this.value / {{ $perUnitCorr }}).toFixed(2)">
                                <small style="color: #888;">
                                    = <span id="corr_rej_{{ $item->id }}">{{ number_format($item->rejected_pieces / $perUnitCorr, 2) }}</span> units
                                </small>
                            </div>
                        </div>
                        <div style="margin-top: 10px;">
                            <label style="font-size: 12px; color: #dc3545; font-weight: 600;">
                                Rejection Message / Reason (if rejected pieces > 0)
                            </label>
                            <input type="text" 
                                   name="corrected_rejection_reasons[{{ $item->id }}]" 
                                   class="form-control" 
                                   value="{{ $item->rejection_reason }}"
                                   placeholder="Enter or update rejection message..."
                                   style="font-size: 13px;">
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
                        <span style="display: block; font-size: 11px; color: #aaa; margin-top: 4px;">
                            Sent: {{ $order->mfg_edit_request_at ? $order->mfg_edit_request_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') : 'N/A' }}
                        </span>
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
                        $hasPendingManufacturing = $order->items->where('status', 'accepted')->sum('manufacturing_pending_pieces') > 0;
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
                
                <div class="form-group" id="tentativeDateGroup" style="{{ $order->manufacturing_status == 'processing' ? 'display: block;' : 'display: none;' }}">
                    <label for="tentative_dispatch_date" class="form-label">Tentative Dispatch Date (IST)</label>
                    <input type="date" 
                           name="tentative_dispatch_date" 
                           id="tentative_dispatch_date" 
                           class="form-control" 
                           value="{{ $order->tentative_dispatch_date ? \Carbon\Carbon::parse($order->tentative_dispatch_date)->timezone('Asia/Kolkata')->format('Y-m-d') : '' }}" 
                           min="{{ \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d') }}">
                    <small style="color: #666; display: block; margin-top: 5px;">Required when accepting or keeping the order in 'Processing' status.</small>
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
                    
                    if (document.getElementById('manufacturing_status').value === 'processing') {
                        document.getElementById('tentativeDateGroup').style.display = 'block';
                        document.getElementById('tentative_dispatch_date').required = true;
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