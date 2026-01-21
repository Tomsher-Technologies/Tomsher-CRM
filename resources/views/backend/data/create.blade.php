@extends('backend.layouts.app',['title' => 'Create Data'])

@section('content')

<div class="container">
    <div class="bg-white shadow-lg rounded-xl p-4">
        <h4 class="text-center mb-4">➕ Add New Data</h4>

        <form action="{{ route('data.store') }}" method="POST" id="dataForm">
            @csrf

            <!-- COMPANY DETAILS -->
            <div class="mb-4">
                <div class="row">
                    @php
                        $today = \Carbon\Carbon::now()->toDateString();
                    @endphp
                    <div class="col-md-4 mb-1">
                        <label for="entry_date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" id="entry_date" class="form-control form-control-sm"  value="{{ old('entry_date', $data->entry_date ?? $today) }}">
                        
                    </div>

                    <div class="col-md-4 mb-1 form-group">
                        <label for="source_id" class="form-label">Source <span class="text-danger">*</span></label>
                        <select name="source_id" id="source_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                            <option value="">--Select Source--</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}"
                                    {{ old('source_id', $data->source_id ?? '') == $source->id ? 'selected' : '' }}>
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                       <span class="sales_error"></span>
                    </div>

                    <div class="col-md-4 mb-1">
                        <label for="status" class="form-label">Status </label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="">-- Select status --</option>
                            @foreach ($status as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('status', $data->status ?? '') == $st->id ? 'selected' : '' }}>
                                    {{ $st->label }}
                                </option>
                            @endforeach
                        </select>
                       
                    </div>

                    <div class="col-md-12 mb-1">
                        <label for="requirement" class="form-label">Requirement</label>
                        <textarea class="form-control form-control-sm" id="requirement" name="requirement" rows="4"></textarea>
                    </div>

                </div>

                <h5 class=" mb-3">🏢 Company Details</h5>
                <div class="row ">
                    <div class="col-md-4 mb-1">
                        <label for="data_code" class="form-label">Data Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="data_code" name="data_code"  value="{{ $dataCode }}" readonly >
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="company_name" name="company_name">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="company_email" class="form-label">Company Email</label>
                        <input type="email" class="form-control form-control-sm" id="company_email" name="company_email">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="industry" class="form-label">Industry</label>
                        <select class="form-control form-control-sm aiz-selectpicker" id="industry" name="industry" data-live-search="true" data-placeholder="Select Industry">
                            <option value="">Select Industry</option>
                            @foreach ($industries as $ind)
                                <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" class="form-control form-control-sm" id="website" name="website">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="registered_country" class="form-label">Registered Country</label>
                        <select class="form-control form-control-sm aiz-selectpicker" id="registered_country" name="registered_country"  data-live-search="true">
                            <option value="">Select Country</option>
                            @foreach ($countries as $con)
                                <option value="{{ $con->id }}">{{ $con->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-1">
                        <label for="google_map_link" class="form-label">Google Map Link</label>
                        <input type="url" class="form-control form-control-sm" id="google_map_link" name="google_map_link">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="emirate" class="form-label">Emirate (If Country is UAE)</label>
                        <select class="form-control form-control-sm aiz-selectpicker" id="emirate" name="emirate">
                            <option value="">Select Emirate</option>
                            @foreach ($emirates as $em)
                                <option value="{{ $em->id }}">{{ $em->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-1">
                        <label for="address" class="form-label">Company Address</label>
                        <textarea class="form-control form-control-sm" id="address" name="address" rows="3"></textarea>
                    </div>
                   
                    <div class="col-md-4 mb-1 form-group">
                        <label for="user_id" class="form-label">Salesperson <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name  }} 
                                </option>
                            @endforeach
                        </select>
                        <span class="sales_error"></span>
                        @error('user_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                </div>
            </div>

            <!-- CONTACT PERSONS -->
            <div>
                <h5 class=" mb-3">👥 Contact Persons</h5>
                <div id="contact-persons" class="mb-4"></div>
                <button type="button" id="add-contact" class="btn btn-outline-primary mt-3">+ Add Contact</button>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="mt-4 text-start">
                <button type="submit" class="btn btn-success" name="button" value="save">Save</button>
                
                <a href="{{ route('data.index') }}" class="btn btn-info" >Cancel</a>
            </div>
        </form>

    </div>
</div>

<template id="contact-template">
    <div class="contact-form border rounded bg-white shadow-sm mb-4">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <strong>Contact Person</strong>
            <span class="remove-btn-wrapper"></span>
        </div>
        <div class="p-3">
            <div class="row g-3 contact-person">
                <div class="col-md-4 mb-1">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control  form-control-sm contact-name" name="contacts[__INDEX__][name]">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control form-control-sm contact-email" name="contacts[__INDEX__][email]">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label">Landline</label>
                    <input type="text" class="form-control form-control-sm contact-landline" name="contacts[__INDEX__][landline]">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label">Mobile</label>
                    <input type="text" class="form-control form-control-sm contact-mobile" name="contacts[__INDEX__][mobile]">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" class="form-control form-control-sm contact-whatsapp" name="contacts[__INDEX__][whatsapp]">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control form-control-sm" name="contacts[__INDEX__][designation]">
                </div>
                <div class="col-md-4">
                    <div class="form-check pt-2">
                        <input class="form-check-input custom-checkbox" type="checkbox" name="contacts[__INDEX__][is_primary]" value="1" id="primary__INDEX__">
                        <label class="form-check-label" for="primary__INDEX__">Primary Contact</label>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</template>

<style>
    .custom-checkbox {
        transform: scale(1.5); /* Adjust the size */
    }
</style>
  
@endsection

@section('script')

<!-- jQuery (must be included first) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- jQuery Validation Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>


<script>
    let contactIndex = 0;

    function createContactPerson() {
        const template = document.getElementById('contact-template').innerHTML;
        const html = template.replaceAll(/__INDEX__/g, contactIndex++);
        const wrapper = document.getElementById('contact-persons');
        const div = document.createElement('div');
        div.innerHTML = html;

        const totalContacts = document.querySelectorAll('#contact-persons .contact-form').length;

        if (totalContacts > 0) {
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger';
            removeBtn.innerText = '✖ Remove';
            removeBtn.onclick = () => div.remove();

            const headerRight = div.querySelector('.remove-btn-wrapper');
            headerRight.appendChild(removeBtn);
        }

        wrapper.appendChild(div);
        //  // Revalidate the form when new fields are added
        //  $("#dataForm").validate();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('add-contact').addEventListener('click', createContactPerson);
        createContactPerson(); // Load first contact
    });

    $(document).ready(function () {
        $('#dataForm').validate({
            rules: {
                entry_date: { required: true },
                data_code: { required: true },
                source_id: { required: true },
                company_name: { required: true },
                user_id: { required: true }
            },
            messages: {
                entry_date: "Entry Date  is required",
                source_id: "Source is required",
                data_code: "Data Code is required",
                company_name: "Company Name is required"
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('aiz-selectpicker')) {
                    // Place error *before* the select element, inside the form-group
                    element.closest('.form-group').find('.sales_error').after(error);
                } else {
                    error.insertAfter(element);
                }
            },
            errorClass: 'is-invalid',
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            ignore: [], // do not ignore any fields
            submitHandler: function (form) {
                let isValid = true;

                $('#contact-persons .contact-person').each(function (index) {
                    const name = $(this).find('.contact-name').val().trim();
                    const email = $(this).find('.contact-email').val().trim();
                    const landline = $(this).find('.contact-landline').val().trim();
                    const mobile = $(this).find('.contact-mobile').val().trim();
                    const whatsapp = $(this).find('.contact-whatsapp').val().trim();

                    // Regex patterns
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const phonePattern = /^[+]?[0-9]{7,15}$/; 

                    if (name !== "") {
                        if (!email && !landline && !mobile && !whatsapp) {
                            AIZ.plugins.notify('danger', "At least one contact method is required for contact person #" + (index + 1));
                            isValid = false;
                            return false;
                        }

                         // Validate formats if values are provided
                        if (email && !emailPattern.test(email)) {
                            AIZ.plugins.notify('danger', `Invalid email format for contact person #${index + 1}`);
                            isValid = false;
                            return false;
                        }
                        if (landline && !phonePattern.test(landline)) {
                            AIZ.plugins.notify('danger', `Invalid landline format for contact person #${index + 1}`);
                            isValid = false;
                            return false;
                        }
                        if (mobile && !phonePattern.test(mobile)) {
                            AIZ.plugins.notify('danger', `Invalid mobile format for contact person #${index + 1}`);
                            isValid = false;
                            return false;
                        }
                        if (whatsapp && !phonePattern.test(whatsapp)) {
                            AIZ.plugins.notify('danger', `Invalid WhatsApp number format for contact person #${index + 1}`);
                            isValid = false;
                            return false;
                        }

                    } else if (email || landline || mobile || whatsapp) {
                        AIZ.plugins.notify('danger', "Name is required for contact person #" + (index + 1));
                        isValid = false;
                        return false;
                    }else{
                        AIZ.plugins.notify('danger', "Enter details for contact person #" + (index + 1));
                        isValid = false;
                        return false;
                    }
                });

                if (isValid) {
                    form.submit();
                }
            }
        });
    });
    
    $(document).on('change', 'input[type="checkbox"][name$="[is_primary]"]', function () {
        if (this.checked) {
            // Uncheck all other primary checkboxes
            $('input[type="checkbox"][name$="[is_primary]"]').not(this).prop('checked', false);
        }
    });
</script>
    
@endsection