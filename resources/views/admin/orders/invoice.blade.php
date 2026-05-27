@extends('layouts.admin')

@section('title', isset($invoice) ? 'Invoice #' . $invoice->invoice_number : (isset($order) ? 'Invoice #' . $order->order_number : 'Invoice'))

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 print:hidden">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Document View</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">
                    @if(isset($invoice))
                        Invoice #{{ $invoice->invoice_number }}
                    @elseif(isset($order))
                        Invoice #{{ $order->order_number }}
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-rose-800 hover:bg-rose-900 text-white text-xs font-black uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-rose-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Invoice
            </button>
            @if(isset($order))
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 text-xs font-black uppercase tracking-[0.2em] rounded-xl shadow-sm hover:bg-slate-50 transition-all">
                    Back to Order
                </a>
            @else
                <a href="{{ route('admin.reward-claims.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 text-xs font-black uppercase tracking-[0.2em] rounded-xl shadow-sm hover:bg-slate-50 transition-all">
                    Back to Claims
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-2 md:p-4 print:p-0 print:shadow-none print:border-none">
        <div class="print:m-0">
            @if(isset($invoice))
                @include('components.invoice-display', ['order' => $order, 'invoice' => $invoice])
            @else
                @include('components.invoice-display', ['order' => $order])
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        /* Hide everything except the invoice container */
        body * {
            visibility: hidden;
        }
        .print\:m-0, .print\:m-0 * {
            visibility: visible;
        }
        .print\:m-0 {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        /* Remove shadows and rounded corners for a clean paper look */
        .bg-white {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
        }
    }
</style>
@endsection