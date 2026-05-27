<div class="invoice-viewer-component">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">
            @if(isset($invoice))
                Invoice #{{ $invoice->invoice_number }}
            @elseif(isset($order))
                Invoice #{{ $order->order_number }}
            @else
                Invoice
            @endif
        </h2>
        <button onclick="window.print()" class="btn" style="background-color: #8B0000; color: white;">
            Print Invoice
        </button>
    </div>
    
    @if(isset($invoice))
        @include('components.invoice-display', ['order' => $order, 'invoice' => $invoice])
    @else
        @include('components.invoice-display', ['order' => $order])
    @endif
</div>

<style>
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background-color: #8B0000; /* Dark Red */
    color: white;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.15s ease-in-out;
    border: none;
    cursor: pointer;
    text-align: center;
}

.btn:hover {
    background-color: #A9A9A9; /* Dark Grey */
}
</style>