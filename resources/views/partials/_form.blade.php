    @csrf

    <div class="row mb-1">
        <div class="col-md-6 mb-1">
            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
            <select name="customer_id" id="customer_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                <option value="">-- Select Customer --</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ old('customer_id', $enquiry->customer_id ?? $customer_id) == $customer->id ? 'selected' : '' }}>
                        {{ $customer->customer_code }} - {{ $customer->company_name  }} 
                    </option>
                @endforeach
            </select>
            @error('customer_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        @php
            $today = \Carbon\Carbon::now()->toDateString();
        @endphp
        <div class="col-md-6 mb-1">
            <label for="enquiry_date" class="form-label">Enquiry Date <span class="text-danger">*</span></label>
            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control form-control-sm"  value="{{ old('enquiry_date', $enquiry->enquiry_date ?? $today) }}">
            @error('enquiry_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6 mb-1">
            <label for="enquiry_source_id" class="form-label">Enquiry Source <span class="text-danger">*</span></label>
            <select name="enquiry_source_id" id="enquiry_source_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                <option value="">-- Select Source --</option>
                @foreach ($sources as $source)
                    <option value="{{ $source->id }}"
                        {{ old('enquiry_source_id', $enquiry->enquiry_source_id ?? '') == $source->id ? 'selected' : '' }}>
                        {{ $source->name }}
                    </option>
                @endforeach
            </select>
            @error('enquiry_source_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6 mb-1">
            <label for="project_type_id" class="form-label">Project Category</label>
            <select name="project_type_id[]" id="project_type_id" multiple class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                {{-- <option value="">-- Select Type --</option> --}}
                @foreach ($projectTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ old('project_type_id', ( isset($enquiry) ? $enquiry->projectTypes->contains($type->id) : '')) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-1 col-md-12">
            <label for="project_details" class="form-label">Project Details <span class="text-danger">*</span></label>
            <textarea name="project_details" id="project_details" class="form-control form-control-sm" rows="8"
                placeholder="Enter project requirements, expectations, etc.">{{ old('project_details', $enquiry->project_details ?? '') }}</textarea>
            @error('project_details')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    
        <div class="mb-1 col-md-12">
            <label for="comments" class="form-label">Internal Comments</label>
            <textarea name="comments" id="comments" class="form-control form-control-sm" rows="3"
                placeholder="Any internal notes or comments">{{ old('comments', $enquiry->comments ?? '') }}</textarea>
        </div>

        @can('transfer_enquiries')
            <div class="col-md-6 mb-1">
                <label for="user_id" class="form-label">Enquiry Owner <span class="text-danger">*</span></label>
                <select name="user_id" id="user_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                    <option value="">-- Select User --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id', $enquiry->owner_id ?? '') == $user->id ? 'selected' : '' }}>
                            {{ $user->name  }} 
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        @endcan

    </div>

    


