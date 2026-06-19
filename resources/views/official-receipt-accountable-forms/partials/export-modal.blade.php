@php
    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $currentMonth = now()->month;
    $currentYear  = now()->year;
@endphp

<div class="form-batch-modal-overlay" id="exportModalOverlay">
    <div class="form-batch-modal orabf-export-modal">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="exportModalCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title">Export Report Logs</h2>
        <p class="orabf-export-subtitle">Select a date range to export batch records as CSV.</p>
        <div class="orabf-export-divider"></div>

        <form id="exportForm" method="GET" action="">
            <div class="orabf-export-range-wrap">

                {{-- From --}}
                <div class="orabf-export-range-col">
                    <span class="orabf-export-range-label">From</span>
                    <div class="orabf-export-range-selects">
                        <select name="from_month" class="form-batch-input orabf-export-select" required>
                            @foreach ($months as $i => $month)
                                <option value="{{ $i + 1 }}" @selected($currentMonth === $i + 1)>{{ $month }}</option>
                            @endforeach
                        </select>
                        <select name="from_year" class="form-batch-input orabf-export-select orabf-export-select--year" required>
                            @for ($y = $currentYear; $y >= 2020; $y--)
                                <option value="{{ $y }}" @selected($currentYear === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="orabf-export-range-arrow" aria-hidden="true">
                    <x-bx-right-arrow-alt class="icon" />
                </div>

                {{-- To --}}
                <div class="orabf-export-range-col">
                    <span class="orabf-export-range-label">To</span>
                    <div class="orabf-export-range-selects">
                        <select name="to_month" class="form-batch-input orabf-export-select" required>
                            @foreach ($months as $i => $month)
                                <option value="{{ $i + 1 }}" @selected($currentMonth === $i + 1)>{{ $month }}</option>
                            @endforeach
                        </select>
                        <select name="to_year" class="form-batch-input orabf-export-select orabf-export-select--year" required>
                            @for ($y = $currentYear; $y >= 2020; $y--)
                                <option value="{{ $y }}" @selected($currentYear === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

            </div>

            <div class="form-batch-actions">
                <button type="submit" class="ctc-add-entry-btn orabf-export-submit-btn">
                    View
                </button>
            </div>
        </form>
    </div>
</div>
