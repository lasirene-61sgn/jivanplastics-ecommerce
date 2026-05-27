@extends('frontend.b2b.layouts.app')

@section('title', (ucfirst($returnNote->type) . ' Note #' . $returnNote->note_number) . ' - Jivan Plastics')
@section('header', ucfirst($returnNote->type) . ' Note #' . $returnNote->note_number)

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 2rem;" class="print:hidden">
        <button onclick="window.print()" style="background-color: #1f2937; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 800; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Note
        </button>
        <a href="{{ route('b2b.orders.show', $order) }}" style="background-color: white; color: #4b5563; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 800; border: 1px solid #d1d5db; text-decoration: none;">
            Back to Order
        </a>
    </div>

    <div class="print:m-0">
        @include('components.return-note-display', ['returnNote' => $returnNote])
    </div>
</div>

<style>
    @media print {
        header, footer, .b2b-sidebar, .print:hidden, .b2b-header-nav { display: none !important; }
        body { background: white !important; }
        .b2b-content { padding: 0 !important; margin: 0 !important; }
        .print\:m-0 { width: 100% !important; }
    }
</style>
@endsection
