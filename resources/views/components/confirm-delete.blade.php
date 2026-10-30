@props(['id' => 'deleteConfirmModal'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-danger d-flex align-items-center" id="{{ $id }}Label">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-1 text-muted" id="{{ $id }}Message">
                    Are you sure you want to delete this record? This action cannot be undone.
                </p>
                <div class="alert alert-light border mt-3 mb-0 small text-secondary" id="{{ $id }}ItemName" style="display:none;"></div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <form id="{{ $id }}Form" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-3" id="{{ $id }}SubmitBtn">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('{{ $id }}');
        if (!modalEl) return;
        
        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            
            const action = button.getAttribute('data-action');
            const message = button.getAttribute('data-message');
            const name = button.getAttribute('data-name');
            
            const form = modalEl.querySelector('#{{ $id }}Form');
            const msgEl = modalEl.querySelector('#{{ $id }}Message');
            const nameEl = modalEl.querySelector('#{{ $id }}ItemName');
            
            if (action && form) form.action = action;
            if (message && msgEl) msgEl.innerText = message;
            if (nameEl) {
                if (name) {
                    nameEl.innerText = name;
                    nameEl.style.display = 'block';
                } else {
                    nameEl.style.display = 'none';
                }
            }
        });
    });
</script>
@endpush

