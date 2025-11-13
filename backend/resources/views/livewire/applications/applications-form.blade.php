<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-file-lines me-2 text-primary"></i>
            @if ($applicationId) Edit Application @else Create Application @endif
        </h4>
        <a wire:navigate href="{{ route('job.app.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-user me-2 text-primary"></i>Candidate
                        </label>
                        <select wire:model="candidate_id"
                                class="form-select @error('candidate_id') is-invalid @enderror"
                                wire:change="$refresh">
                            <option value="">Select Candidate</option>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                            @endforeach
                        </select>
                        @error('candidate_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-briefcase me-2 text-primary"></i>Job Post
                        </label>
                        <select wire:model="job_post_id"
                                class="form-select @error('job_post_id') is-invalid @enderror"
                                wire:change="$refresh">
                            <option value="">Select Job Post</option>
                            @foreach($jobPosts as $jobPost)
                                <option value="{{ $jobPost->id }}">{{ $jobPost->title }}</option>
                            @endforeach
                        </select>
                        @error('job_post_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                        <!-- عرض رسالة التقديم الموجود -->
                        @if($candidate_id && $job_post_id)
                            @php
                                $existingApplication = \App\Domains\Applications\Models\JobApplication::where('candidate_id', $candidate_id)
                                    ->where('job_post_id', $job_post_id)
                                    ->first();
                            @endphp
                            @if($existingApplication && (!$applicationId || $existingApplication->id != $applicationId))
                                <div class="alert alert-warning mt-2 py-2 small d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Duplicate Application Found!</strong>
                                        <br>
                                        This candidate has already applied to this job.
                                        @if($existingApplication)
                                            <br>
                                            <small>
                                                Current status:
                                                <span class="badge
                                                    @if($existingApplication->status == 'pending') bg-warning
                                                    @elseif($existingApplication->status == 'accepted') bg-success
                                                    @elseif($existingApplication->status == 'rejected') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($existingApplication->status) }}
                                                </span>
                                                - Applied on: {{ $existingApplication->created_at->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-envelope me-2 text-primary"></i>Cover Letter
                        </label>
                        <textarea wire:model="cover_letter"
                                  class="form-control @error('cover_letter') is-invalid @enderror"
                                  rows="5"
                                  placeholder="Enter cover letter content..."></textarea>
                        @error('cover_letter') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-flag me-2 text-primary"></i>Status
                        </label>
                        <select wire:model="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Current Resume (if editing) -->
                    @if($applicationId && $currentResumePath)
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Current Resume</label>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="{{ Storage::url($currentResumePath) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-eye"></i> View Current Resume
                                </a>
                                <a href="{{ Storage::url($currentResumePath) }}"
                                   download
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                <span class="small text-muted">Upload new resume to replace current one</span>
                            </div>
                        </div>
                    @endif

                    <!-- Resume Upload -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">
                            {{ $applicationId ? 'Upload New Resume' : 'Resume Upload' }}
                            @if(!$applicationId) <span class="text-danger">*</span> @endif
                        </label>
                        <div class="position-relative">
                            <i class="fa-solid fa-file-arrow-up position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <input type="file"
                                   wire:model="resume"
                                   class="form-control ps-5 @error('resume') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.txt">
                        </div>
                        @error('resume')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror

                        @if ($resume)
                            <div class="mt-2 p-2 border rounded bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-file text-primary"></i>
                                    <div>
                                        <small class="fw-semibold">{{ $resume->getClientOriginalName() }}</small>
                                        <br>
                                        <small class="text-muted">{{ round($resume->getSize() / 1024, 2) }} KB</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, TXT (Max: 5MB)
                        </div>
                    </div>

                </div>

                <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        @if ($applicationId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
