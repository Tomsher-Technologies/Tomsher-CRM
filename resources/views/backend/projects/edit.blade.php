@extends('backend.layouts.app', ['title' => 'Edit Project'])
@section('content')
<div class="container">
    <div class="bg-white shadow-lg rounded-xl p-4">
        <h4 class="text-center mb-4">✏️ Edit Project</h4>
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group mb-3">
                    <label for="project_name">Project Name <span class="text-danger">*</span></label>
                    <input type="text"  name="project_name" id="project_name" class="form-control form-control-sm" value="{{ old('project_name', $project->project_name) }}">
                    @error('project_name')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="project_name">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control form-control-sm  aiz-selectpicker" data-live-search="true">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ old('customer_id',$project->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->customer_code }} - {{ $customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="project_name">Enquiry</label>
                    <select name="enquiry_id" id="enquiry_id" class="form-control form-control-sm">
                        <option value="">Select Enquiry</option>
                        @foreach($enquiries as $enquiry)
                            <option value="{{ $enquiry->id }}" {{ old('enquiry_id',$project->enquiry_id) == $enquiry->id ? 'selected' : '' }}>
                                {{ $enquiry->enquiry_code ?? 'No Code' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="start_date">Project Start Date</label>
                    <input type="date"  name="start_date" id="start_date" class="form-control form-control-sm" value="{{ old('start_date',$project->start_date) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="internal_deadline">Internal Deadline Date</label>
                    <input type="date"  name="internal_deadline" id="internal_deadline" class="form-control form-control-sm" value="{{ old('internal_deadline',$project->internal_deadline) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="client_deadline">Client Deadline Date</label>
                    <input type="date"  name="client_deadline" id="client_deadline" class="form-control form-control-sm" value="{{ old('client_deadline',$project->client_deadline) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="project_total_cost">Project Total Cost <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="project_total_cost" id="project_total_cost" class="form-control form-control-sm" value="{{old('project_total_cost', $project->project_total_cost) }}">
                    @error('project_total_cost')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    @php
                        $statuses = [
                            'pending' => ['label' => 'Pending', 'bg' => '#abe4fb', 'color' => '#000'],
                            'kickoff_completed' => ['label' => 'Kickoff Completed', 'bg' => '#4db2ff', 'color' => '#000'],
                            'design_stage' => ['label' => 'Design Stage', 'bg' => '#FFEB3B', 'color' => '#000'],
                            'static' => ['label' => 'Static', 'bg' => '#81f98f', 'color' => '#000'],
                            'beta' => ['label' => 'Beta', 'bg' => '#0b9c1c', 'color' => '#000'],
                            'on_hold' => ['label' => 'On Hold', 'bg' => '#FF9800', 'color' => '#000'],
                            'canceled' => ['label' => 'Canceled', 'bg' => '#F44336', 'color' => '#000'],
                        ];
                    @endphp
                
                    <select name="status" id="status" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        @foreach ($statuses as $key => $data)
                            <option value="{{ $key }}" {{ old('status',$project->status) == $key ? 'selected' : '' }}>
                                {{ $data['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="technology_ids" class="">Technologies</label>
                    <select name="technology_ids[]" id="technology_ids" multiple class="aiz-selectpicker form-control form-control-sm">
                        @foreach($allTechnologies as $technology)
                            <option value="{{ $technology->id }}"
                                {{ in_array($technology->id, old('technology_ids', $project->technologies->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $technology->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-12 form-group mb-3">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control form-control-sm" rows="5">{{ old('comment',$project->comment) }}</textarea>
                </div>
            </div>

            <hr>
    
            {{-- Payments --}}
            <div id="payments_section">
                <h5>Payment Details</h5>
                @php $paymentIndex = 0; @endphp
                @foreach ($project->payments as $payment)
                    @include('backend.projects._payment_row', ['payment' => $payment, 'index' => $paymentIndex++])
                @endforeach
            </div>
    
            <div class="col-md-6 d-flex ">
                <button type="button" class="btn btn-warning btn-sm mt-2 mb-1" id="add_payment_btn">Add Payment</button>
                <div class="mt-2 mb-1  ml-5">
                    <label><strong>Remaining Amount:</strong></label>
                    <div id="remaining-amount" style="font-weight:bold; color: green;font-size: 16px;">0</div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-save me-1"></i> Update Project
                </button>
                <a href="{{ Session::has('projects_last_url') ? Session::get('projects_last_url') : route('projects.index') }}" class="btn btn-info" >Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    let paymentIndex = {{ $paymentIndex ?? 0 }};

    $('#add_payment_btn').click(function() {
        const paymentRow = `
        <div class="payment-row mb-3 card">
            <div class="row card-body payment-section">
                <div class="col-md-4 mb-1">
                    <label class="form-label">Payment Title <span class="text-danger">*</span></label>
                    <input type="text" name="payments[${paymentIndex}][title]" class="form-control form-control-sm" placeholder="Payment Title" required>
                </div>

                <div class="col-md-3 mb-1">
                    <label class="form-label">Payment Method</label>
                    <select name="payments[${paymentIndex}][method]" class="form-control form-control-sm">
                        <option value="">Select Method</option>
                        <option value="pdc">PDC</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div class="col-md-3 mb-1">
                    <label class="form-label">Payment Status</label>
                    <select name="payments[${paymentIndex}][status]" class="form-control form-control-sm">
                        <option value="pending">Pending</option>
                        <option value="received">Received</option>
                    </select>
                </div>

                 <div class="col-md-3 mb-1">
                    <label class="form-label">Expected Date</label>
                    <input type="date" name="payments[${paymentIndex}][expected_date]" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Amount</label>
                    <input type="number" min="0" step="0.01" name="payments[${paymentIndex}][amount]" class="form-control form-control-sm payment-amount" placeholder="Amount">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Percentage</label>
                    <input type="number" min="0" step="0.01" name="payments[${paymentIndex}][percentage]" class="form-control form-control-sm payment-percentage" placeholder="Percentage">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Tax (5%)</label>
                    <input type="number" min="0" step="0.01"  name="payments[${paymentIndex}][tax_amount]" readonly class="form-control form-control-sm payment-tax" placeholder="Auto">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Total</label>
                    <input type="number" min="0" step="0.01"  name="payments[${paymentIndex}][total_amount]" readonly class="form-control form-control-sm payment-total" placeholder="Auto">
                </div>

                <div class="col-md-3 mb-1">
                    <label class="form-label">Received Date</label>
                    <input type="date" name="payments[${paymentIndex}][received_date]" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Received Amount</label>
                    <input type="number" min="0" step="0.01" name="payments[${paymentIndex}][received_amount]" class="form-control form-control-sm received-amount" readonly placeholder="Received">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Received Tax (5%)</label>
                    <input type="number" min="0" step="0.01" readonly  name="payments[${paymentIndex}][received_tax]" class="form-control form-control-sm received-tax" placeholder="Auto">
                </div>

                <div class="col-md-2 mb-1">
                    <label class="form-label">Total Received</label>
                    <input type="number" min="0" step="0.01" name="payments[${paymentIndex}][total_received]" class="form-control form-control-sm total-received" placeholder="Total">
                </div>

                <div class="col-md-2 m-auto">
                    <button type="button" class="btn btn-danger btn-sm remove-payment">Remove X</button>
                </div>
            </div>
        </div>`;

        $('#payments_section').append(paymentRow);
        paymentIndex++;
    });

    // Auto calculate tax & totals on input
    $(document).on('input', '.payment-amount, .payment-percentage, .received-amount, .total-received', function () {
        const section = $(this).closest('.payment-section');
        calculateValues(section);
    });

    // Remove payment row
    $(document).on('click', '.remove-payment', function () {
        $(this).closest('.payment-row').remove();
        calculateRemaining();
    });

    function calculateValues(section) {
        let totalCost = parseFloat($('#project_total_cost').val()) || 0;

        let amountField = section.find('.payment-amount');
        let percentageField = section.find('.payment-percentage');
        let taxField = section.find('.payment-tax');
        let totalField = section.find('.payment-total');

        let receivedAmountField = section.find('.received-amount');
        let receivedTaxField = section.find('.received-tax');
        let totalReceivedField = section.find('.total-received');

        let amount = parseFloat(amountField.val()) || 0;
        let percentage = parseFloat(percentageField.val()) || 0;
        let totalReceived = parseFloat(totalReceivedField.val()) || 0;

        // Update percentage based on amount
        if ($(document.activeElement).hasClass('payment-amount')) {
            if (totalCost > 0) {
                percentageField.val(((amount / totalCost) * 100).toFixed(2));
            } else {
                percentageField.val('0');
            }
        }

        // Update amount based on percentage
        if ($(document.activeElement).hasClass('payment-percentage')) {
            amount = (totalCost * percentage) / 100;
            amountField.val(amount.toFixed(2));
        }

        // Calculate Tax (5%) and Total
        let tax = amount * 0.05;
        tax = tax >= 0 ? tax : 0;
        let total = amount + tax;

        taxField.val(tax.toFixed(2));
        totalField.val(total.toFixed(2));

        // Calculate Received Tax from the Total Received (without the tax)
        let receivedTax = totalReceived * 0.05 / 1.05;  // Tax is calculated from the gross amount (total including tax)
        receivedTax = receivedTax >= 0 ? receivedTax : 0;

        // The received amount is the total amount minus the tax portion
        let receivedAmount = totalReceived - receivedTax;

        receivedTaxField.val(receivedTax.toFixed(2));
        receivedAmountField.val(receivedAmount.toFixed(2));

        // Final validation (no negative values)
        if (tax < 0 || total < 0 || receivedTax < 0 || receivedAmount < 0) {
            AIZ.plugins.notify('danger', 'Negative values detected in tax or totals.');
        }

        calculateRemaining();
    }

calculateRemaining();
    function calculateRemaining(){
        let totalCost = parseFloat($('#project_total_cost').val()) || 0;  // Assuming this is the total project cost field
        let extraCost = totalCost * 0.05; // 5% of totalCost
        let totalWithTax = totalCost + extraCost;
        let totalPaid = 0;
        $('.payment-row').each(function() {
            let totalReceived = parseFloat($(this).find('.received-amount').val()) || 0;
            totalPaid += totalReceived;
        });

        let remainingBalance = totalWithTax - totalPaid;
        if(remainingBalance < 0){
            $('#remaining-amount').css('color', 'red');
        }else{
            $('#remaining-amount').css('color', 'green');
        }
        $('#remaining-amount').text(remainingBalance.toFixed(2));
    }
    function recalculatePercentages(){
        let totalCost = parseFloat($('#project_total_cost').val()) || 0;

        $('.payment-section').each(function(){
            let amountField = $(this).find('.payment-amount');
            let percentageField = $(this).find('.payment-percentage');

            let amount = parseFloat(amountField.val()) || 0;

            if (totalCost > 0) {
                let calculatedPercentage = (amount / totalCost) * 100;
                percentageField.val(calculatedPercentage.toFixed(2));
            } else {
                percentageField.val('0');
            }
        });
    }

    $(document).ready(function(){
        $('#customer_id').change(function(){
            var customerId = $(this).val();
            $('#enquiry_id').html('<option value="">Select Enquiry</option>'); // Reset Enquiry dropdown

            if(customerId){
                $.ajax({
                    url: "{{ route('get.enquiries', '') }}/" + customerId,
                    type: "GET",
                    success: function(response){
                        if(response.length > 0){
                            $.each(response, function(key, enquiry){
                                $('#enquiry_id').append('<option value="'+ enquiry.id +'">'+ (enquiry.enquiry_code ?? 'No Code') +'</option>');
                            });
                        }
                    }
                });
            }
        });

        $('form').on('submit', function(e){
            let totalCost = parseFloat($('#project_total_cost').val()) || 0;
            let extraCost = totalCost * 0.05;
            let totalPayments = 0;

            $('.received-amount').each(function(){
                let amount = parseFloat($(this).val()) || 0;
                totalPayments += amount;
            });

            if(totalPayments > (totalCost + extraCost)){
                AIZ.plugins.notify('danger', 'Total Received Amount cannot be greater than Project Total Cost.');
                e.preventDefault();
            }
        });

        // Recalculate when project total cost changes
        $('#project_total_cost').on('input', function(){
            calculateRemaining();
            recalculatePercentages();
        });
        
    });
</script>

@endsection