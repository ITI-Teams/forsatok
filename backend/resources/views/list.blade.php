<x-app-layout>
    <div>
        <!-- ✅ Alerts -->
        <div class="alert alert-success d-none fade show align-items-center" role="alert" id="successAlert">
            <i class="fa-solid fa-circle-check me-2"></i> Item created/updated successfully!
        </div>
        <div class="alert alert-danger d-none fade show align-items-center" role="alert" id="deleteAlert">
            <i class="fa-solid fa-circle-xmark me-2"></i> Item deleted successfully!
        </div>

        <!-- ✅ Header Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0"><i class="fa-solid fa-table-list me-2 text-primary"></i>Items List</h4>
            <button class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i> New Item
            </button>
        </div>

        <!-- ✅ Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>Landing Page Design</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2025-11-05</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info me-2"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning me-2"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ✅ Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this item? This action cannot be undone.
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
