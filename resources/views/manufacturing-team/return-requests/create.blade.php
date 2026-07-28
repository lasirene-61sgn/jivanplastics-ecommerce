<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Return Request - Manufacturing Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --primary-red: #E31E24;
            --deep-red: #8B0000;
            --soft-bg: #F8FAFC;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--soft-bg);
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Select2 Customization to match Tailwind aesthetics */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            background-color: #F8FAFC;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #F43F5E; /* rose-500 */
            outline: none;
            box-shadow: 0 0 0 1px #F43F5E;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #334155;
            font-size: 0.875rem;
            font-weight: 600;
            padding-left: 1rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
        }
        .select2-dropdown {
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .select2-results__option {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #FFF1F2; /* rose-50 */
            color: #E11D48; /* rose-600 */
        }
    </style>
</head>

<body class="min-h-screen pb-20">

    <!-- Premium Header -->
    <header class="glass-header sticky top-0 z-50 px-4 sm:px-8">
        <div class="max-w-[1440px] mx-auto h-24 flex items-center justify-between">
            <div class="flex items-center gap-12">
                <a href="{{ route('manufacturing-team.dashboard') }}" class="flex items-center group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto transition-transform group-hover:scale-105">
                </a>
                <div class="hidden md:block h-8 w-px bg-slate-200"></div>
                <div class="hidden md:block">
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Manufacturing <span class="text-rose-600">Hub</span></h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-tight">V4 Kitchen Partner</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-sm font-black text-slate-900">{{ $manufacturingTeam->factory_name ?? 'Factory' }}</span>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Active Unit
                    </span>
                </div>

                <a href="{{ route('manufacturing-team.dashboard') }}" class="p-3 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 rounded-2xl transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1000px] mx-auto px-4 sm:px-8 py-10">
        
        <div class="mb-12 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-black text-slate-900 uppercase tracking-tight mb-2">Create <span class="text-rose-600">Return</span> Request</h2>
                <p class="text-slate-500 font-medium">Initiate a return without an order reference</p>
            </div>
            <a href="{{ route('manufacturing-team.dashboard', ['tab' => 'returns']) }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">
                Cancel
            </a>
        </div>

        @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-4">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-emerald-900 uppercase tracking-widest">Success</h4>
                <p class="text-emerald-700 text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-8 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-4">
            <div class="w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-rose-900 uppercase tracking-widest">Error</h4>
                <ul class="text-rose-700 text-sm mt-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('manufacturing-team.returns.store') }}" method="POST" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" id="returnForm">
            @csrf
            
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-6">Customer Details</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="customer_id" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Select Customer <span class="text-rose-500">*</span></label>
                        <select name="customer_id" id="customer_id" class="select2 w-full" required>
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }}) - {{ strtoupper($customer->customer_type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-8 border-b border-slate-50">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Products to Return</h3>
                    <button type="button" id="addProductBtn" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        + Add Product
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 mb-4">
                    <table class="w-full text-left" id="productsTable">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50">
                                <th class="py-4 px-6 w-12 text-center">Inc</th>
                                <th class="py-4 px-6">Product</th>
                                <th class="py-4 px-6 w-32 text-center">Qty (Units)</th>
                                <th class="py-4 px-6 w-32 text-center">Pieces</th>
                                <th class="py-4 px-6 w-20 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50" id="productsContainer">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div id="noProductsMsg" class="text-center py-8 text-slate-400 text-sm font-medium">
                    Click "+ Add Product" to add items to this return request.
                </div>
            </div>

            <div class="p-8 bg-slate-50/30">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-6">Return Reason</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Reason Category <span class="text-rose-500">*</span></label>
                        <select name="reason" id="reason" class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400 transition-colors" required>
                            <option value="">-- Select Reason --</option>
                            <option value="Defective">Defective</option>
                            <option value="Wrong Item">Wrong Item</option>
                            <option value="Damaged in Transit">Damaged in Transit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Additional Details</label>
                        <textarea name="description" id="description" rows="3" class="w-full p-4 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400 transition-colors" placeholder="Provide any additional context or details about the return..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-200 border-dashed mt-4">
                    <button type="submit" class="px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-rose-200 transition-all active:scale-95 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Product Options Data -->
    <script>
        const productsList = @json($products);
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            let rowCount = 0;

            function updateVisibility() {
                if ($('#productsContainer tr').length > 0) {
                    $('#productsTable').show();
                    $('#noProductsMsg').hide();
                } else {
                    $('#productsTable').hide();
                    $('#noProductsMsg').show();
                }
            }
            
            updateVisibility();

            $('#addProductBtn').on('click', function() {
                rowCount++;
                
                let productOptions = '<option value="">Select Product...</option>';
                productsList.forEach(p => {
                    productOptions += `<option value="${p.id}">${p.name}</option>`;
                });

                const tr = `
                    <tr class="product-row hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-center">
                            <input type="checkbox" name="items[${rowCount}][selected]" value="1" checked class="w-4 h-4 text-rose-600 border-slate-300 rounded focus:ring-rose-500 cursor-pointer">
                        </td>
                        <td class="py-4 px-6">
                            <select name="items[${rowCount}][product_id]" class="product-select w-full h-10 px-3 border border-slate-200 rounded-lg text-sm font-medium focus:border-rose-400 focus:outline-none" required>
                                ${productOptions}
                            </select>
                        </td>
                        <td class="py-4 px-6">
                            <input type="number" name="items[${rowCount}][quantity]" class="w-full h-10 px-3 text-center border border-slate-200 rounded-lg text-sm font-bold focus:border-rose-400 focus:outline-none" min="0" placeholder="0">
                        </td>
                        <td class="py-4 px-6">
                            <input type="number" name="items[${rowCount}][pieces]" class="w-full h-10 px-3 text-center border border-slate-200 rounded-lg text-sm font-bold focus:border-rose-400 focus:outline-none" min="0" placeholder="0">
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button type="button" class="remove-btn p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;

                $('#productsContainer').append(tr);
                // We can init select2 on the new select if desired, but standard select is often fine for rows.
                // To keep it simple, we use a styled standard select.
                
                updateVisibility();
            });

            $(document).on('click', '.remove-btn', function() {
                $(this).closest('tr').remove();
                updateVisibility();
            });
            
            // Initial empty row
            $('#addProductBtn').click();
        });
    </script>
</body>
</html>
