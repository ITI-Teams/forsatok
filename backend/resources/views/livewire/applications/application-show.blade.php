<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Application Details</h2>
        <a wire:navigate href="{{ route('job.app.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($application)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Application #{{ $application->id }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Candidate Information</h6>
                        <p><strong>Name:</strong> {{ $application->candidate->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $application->candidate->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $application->candidate->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Job Information</h6>
                        <p><strong>Job Title:</strong> {{ $application->jobPost->title ?? 'N/A' }}</p>
                        <p><strong>Company:</strong> {{ $application->jobPost->employer->name ?? 'N/A' }}</p>
                        <p><strong>Location:</strong> {{ $application->jobPost->location->country->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr>

                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Cover Letter</h6>
                        <div class="border p-3 rounded bg-light">
                            @if($application->cover_letter)
                                {{ $application->cover_letter }}
                            @else
                                <em class="text-muted">No cover letter provided.</em>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Application Status</h6>
                        <span class="badge
                            @if($application->status == 'pending') bg-warning
                            @elseif($application->status == 'accepted') bg-success
                            @elseif($application->status == 'rejected') bg-danger
                            @else bg-secondary @endif fs-6">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6>Applied Date</h6>
                        <p>{{ $application->created_at->format('M d, Y \\a\\t h:i A') }}</p>
                    </div>
                </div>

                @if($application->resume_path)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Resume / CV</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ asset('storage/' . $application->resume_path) }}"
                                   target="_blank"
                                   class="btn btn-success">
                                    <i class="fa-solid fa-eye"></i> View Resume
                                </a>
                                <a href="{{ asset('storage/' . $application->resume_path) }}"
                                   download
                                   class="btn btn-outline-success">
                                    <i class="fa-solid fa-download"></i> Download Resume
                                </a>
                            </div>

                            <!-- Preview for PDF -->
                            @if(pathinfo($application->resume_path, PATHINFO_EXTENSION) === 'pdf')
                                <div class="mt-3">
                                    <iframe src="{{ asset('storage/' . $application->resume_path) }}"
                                            width="100%"
                                            height="500px"
                                            style="border: 1px solid #ddd; border-radius: 5px;">
                                    </iframe>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Resume / CV</h6>
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                                No resume uploaded for this application.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h6>Application Actions</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a wire:navigate href="{{ route('job.app.edit', $application->id) }}"
                               class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-edit"></i> Edit Application
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <i class="fa-solid fa-exclamation-triangle"></i>
            Application not found or you don't have permission to view it.
        </div>
    @endif




    <!-- Display flash messages -->
    @if (session()->has('message'))
        <script>
            Swal.fire({
                title: "Success!",
                text: "{{ session('message') }}",
                icon: "success",
                confirmButtonColor: "#28a745"
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonColor: "#dc3545"
            });
        </script>
    @endif
</div>
