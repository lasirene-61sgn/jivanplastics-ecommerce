@extends('frontend.b2b.layouts.app')

@section('title', (isset($order) ? 'Invoice #' . $order->order_number : (isset($invoice) ? 'Invoice #' . $invoice->invoice_number : 'Invoice')) . ' - E-Commerce Store')
@section('header', isset($order) ? 'Invoice #' . $order->order_number : (isset($invoice) ? 'Invoice #' . $invoice->invoice_number : 'Invoice'))

@section('content')
<div class="b2b-content">
    @include('components.invoice-viewer', ['order' => $order, 'invoice' => $invoice])
</div>
@endsection