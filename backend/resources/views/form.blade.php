<x-app-layout>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Create / Edit Item</h4>
            <a href="#" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back to List</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-heading me-2 text-primary"></i>Title</label>
                            <input type="text" class="form-control" placeholder="Enter title">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-tags me-2 text-primary"></i>Category</label>
                            <select class="form-select">
                                <option selected disabled>Choose category</option>
                                <option>Design</option>
                                <option>Development</option>
                                <option>Marketing</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-toggle-on me-2 text-primary"></i>Status</label>
                            <select class="form-select">
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-calendar me-2 text-primary"></i>Date</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Description</label>
                            <textarea class="form-control" rows="4" placeholder="Enter details..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-upload me-2 text-primary"></i>Upload File</label>
                            <input type="file" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary px-4"><i class="fa-solid fa-rotate-left me-2"></i>Reset</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
