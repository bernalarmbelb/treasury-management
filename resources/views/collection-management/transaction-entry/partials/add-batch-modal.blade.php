<div class="form-batch-modal-overlay" id="formBatchModalOverlay">
    <div class="form-batch-modal">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="formBatchCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title" id="formBatchModalTitle">Add new batch of Form</h2>

        <form id="formBatchForm" method="POST" action="">
            @csrf

            <div class="form-batch-field-group">
                <div class="form-batch-field-header">
                    <p>Date of Registration</p>
                </div>
                <div class="form-batch-field-row">
                    <select name="registration_month" class="form-batch-input" required>
                        <option value="" disabled selected>Month</option>
                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $i => $month)
                            <option value="{{ $i + 1 }}" @selected(($i + 1) === now()->month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="registration_day" class="form-batch-input" placeholder="Day" min="1" max="31" value="{{ now()->day }}" required>
                    <input type="number" name="registration_year" class="form-batch-input" placeholder="Year" min="1900" max="2100" value="{{ now()->year }}" required>
                </div>
            </div>

            <div class="form-batch-field-group">
                <div class="form-batch-field-header">
                    <p>Date of Purchase</p>
                </div>
                <div class="form-batch-field-row">
                    <select name="purchase_month" class="form-batch-input" required>
                        <option value="" disabled selected>Month</option>
                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $i => $month)
                            <option value="{{ $i + 1 }}" @selected(($i + 1) === now()->month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="purchase_day" class="form-batch-input" placeholder="Day" min="1" max="31" value="{{ now()->day }}" required>
                    <input type="number" name="purchase_year" class="form-batch-input" placeholder="Year" min="1900" max="2100" value="{{ now()->year }}" required>
                </div>
            </div>

            <div class="form-batch-fields-stack">
                <div class="form-batch-field-group">
                    <div class="form-batch-field-header">
                        <p>Starting OR Serial Number</p>
                    </div>
                    <input type="text" name="starting_serial_number" class="form-batch-input form-batch-input-full" placeholder="Input whole OR serial number" required>
                </div>

                <div class="form-batch-field-group">
                    <div class="form-batch-field-header">
                        <p>Ending OR Serial Number</p>
                    </div>
                    <input type="text" name="ending_serial_number" class="form-batch-input form-batch-input-full" placeholder="Input the last 3 digit of OR serial number" required>
                </div>
            </div>

            <div class="form-batch-actions">
                <button type="submit" class="form-batch-save-btn">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="form-batch-alert-success" id="formBatchSuccessAlert" role="status">
    <span class="form-batch-alert-icon">
        <x-bx-check class="icon" />
    </span>
    <div class="form-batch-alert-text">
        <p class="form-batch-alert-title">Successfully Added!</p>
        <p class="form-batch-alert-subtitle" id="formBatchSuccessAlertSubtitle"></p>
    </div>
</div>
