{{-- Persistent bulk action bar (lives outside #archive-table-container so it
     survives AJAX table reloads; driven by delegated JS in index.blade.php). --}}
<div class="bulk-action-bar" id="bulkActionBar" style="display:none;">
    <span class="bulk-action-count" id="bulkActionCount">0 selected</span>
    <div class="bulk-action-btns">
        <button type="button" class="bulk-btn bulk-btn--archive" id="bulkUnarchiveBtn">Unarchive Selected</button>
        <button type="button" class="bulk-btn bulk-btn--clear"   id="bulkClearBtn">Clear Selection</button>
    </div>
</div>
