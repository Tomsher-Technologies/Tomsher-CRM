<div class="payment-row mb-3 card">
    <div class="row card-body payment-section">
        <input type="hidden" name="payments[{{ $index }}][id]" value="{{ $payment->id ?? '' }}">
        
        <div class="col-md-4 mb-1">
            <label>Payment Title</label>
            <input type="text" name="payments[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $payment->payment_title ?? '' }}">
        </div>

        <div class="col-md-3 mb-1">
            <label>Payment Method</label>
            <select name="payments[{{ $index }}][method]" class="form-control form-control-sm">
                @foreach (['pdc', 'cash', 'cheque', 'bank_transfer'] as $method)
                    <option value="{{ $method }}" {{ ($payment->method ?? '') == $method ? 'selected' : '' }}>{{ ucfirst($method) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-1">
            <label>Status</label>
            <select name="payments[{{ $index }}][status]" class="form-control form-control-sm">
                <option value="pending" {{ ($payment->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="received" {{ ($payment->status ?? '') == 'received' ? 'selected' : '' }}>Received</option>
            </select>
        </div>

        <div class="col-md-3 mb-1">
            <label>Expected Date</label>
            <input type="date" name="payments[{{ $index }}][expected_date]" class="form-control form-control-sm" value="{{ $payment->expected_date ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Amount</label>
            <input type="number" name="payments[{{ $index }}][amount]" class="form-control form-control-sm payment-amount" step="0.01" value="{{ $payment->amount ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Percentage</label>
            <input type="number" name="payments[{{ $index }}][percentage]" class="form-control form-control-sm payment-percentage" step="0.01" value="{{ $payment->percentage ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Tax (5%)</label>
            <input type="number" name="payments[{{ $index }}][tax_amount]" class="form-control form-control-sm payment-tax" step="0.01" readonly value="{{ $payment->tax ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Total</label>
            <input type="number" name="payments[{{ $index }}][total_amount]" class="form-control form-control-sm payment-total" step="0.01" readonly value="{{ $payment->total_amount ?? '' }}">
        </div>

        <div class="col-md-3 mb-1">
            <label>Received Date</label>
            <input type="date" name="payments[{{ $index }}][received_date]" class="form-control form-control-sm" value="{{ $payment->received_date ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Received Amount</label>
            <input type="number" name="payments[{{ $index }}][received_amount]" class="form-control form-control-sm received-amount" step="0.01" value="{{ $payment->received_amount ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Received Tax</label>
            <input type="number" name="payments[{{ $index }}][received_tax]" class="form-control form-control-sm received-tax" step="0.01" readonly value="{{ $payment->received_tax ?? '' }}">
        </div>

        <div class="col-md-2 mb-1">
            <label>Total Received</label>
            <input type="number" name="payments[{{ $index }}][total_received]" class="form-control form-control-sm total-received" step="0.01" value="{{ $payment->received_total_amount ?? '' }}">
        </div>

        <div class="col-md-2 m-auto">
            <button type="button" class="btn btn-danger btn-sm remove-payment">Remove X</button>
        </div>
    </div>
</div>
