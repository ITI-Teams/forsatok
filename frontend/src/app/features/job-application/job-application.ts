import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { SelectModule } from 'primeng/select';
import { FileUploadModule } from 'primeng/fileupload';
import { ButtonModule } from 'primeng/button';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { MessageModule } from 'primeng/message';
import { ApplicationService, JobPost } from '../../core/services/application.service';

interface ApplicationForm {
  job_post_id: number | null;
  cover_letter: string;
  resume_file: File | null;
}

@Component({
  selector: 'app-job-application',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    InputTextModule,
    TextareaModule,
    SelectModule,
    FileUploadModule,
    ButtonModule,
    ToastModule,
    MessageModule
  ],
  templateUrl: './job-application.html',
  styleUrls: ['./job-application.css']
})
export class JobApplication implements OnInit {
  application: ApplicationForm = {
    job_post_id: null,
    cover_letter: '',
    resume_file: null
  };

  availableJobs: JobPost[] = [];
  submitted = false;
  isLoading = false;
  jobIdFromRoute: number | null = null;

  constructor(
    private messageService: MessageService,
    private applicationService: ApplicationService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.jobIdFromRoute = params['id'] ? +params['id'] : null;
      if (this.jobIdFromRoute) {
        this.application.job_post_id = this.jobIdFromRoute;
      }
    });

    this.loadAvailableJobs();
  }

  loadAvailableJobs() {
    this.applicationService.getAvailableJobs().subscribe({
      next: (response) => {
        if (response.success) {
          this.availableJobs = response.data;

          if (this.jobIdFromRoute) {
            const jobExists = this.availableJobs.some(job => job.id === this.jobIdFromRoute);
            if (!jobExists) {
              this.messageService.add({
                severity: 'warn',
                summary: 'Warning',
                detail: 'The requested job is no longer available'
              });
              this.jobIdFromRoute = null;
              this.application.job_post_id = null;
            }
          }
        }
      },
      error: (error) => {
        this.applicationService.showError('Failed to load available jobs');
      }
    });
  }

  onFileSelect(event: any) {
    const file = event.files[0];
    if (file) {
      // Check file type
      const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      ];

      if (!allowedTypes.includes(file.type)) {
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Please upload PDF or Word documents only'
        });
        return;
      }

      // Check file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'File size should not exceed 5MB'
        });
        return;
      }

      this.application.resume_file = file;

      this.messageService.add({
        severity: 'success',
        summary: 'Success',
        detail: 'Resume uploaded successfully'
      });
    }
  }

  onSubmit() {
    this.submitted = true;

    if (!this.isFormValid()) {
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Please fill all required fields correctly'
      });
      return;
    }

    this.isLoading = true;

    const formData = new FormData();
    formData.append('job_post_id', this.application.job_post_id!.toString());
    formData.append('cover_letter', this.application.cover_letter);

    if (this.application.resume_file) {
      formData.append('resume', this.application.resume_file);
    }

    this.applicationService.submitApplication(formData).subscribe({
      next: (response) => {
        this.isLoading = false;

        if (response.success) {
          this.messageService.add({
            severity: 'success',
            summary: 'Success',
            detail: response.message || 'Application submitted successfully!'
          });

          setTimeout(() => {
            this.application = {
              job_post_id: null,
              cover_letter: '',
              resume_file: null
            };
            this.submitted = false;

            this.router.navigate(['/jobs']);
          }, 2000);
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Failed to submit application'
          });
        }
      },
      error: (error) => {
        this.isLoading = false;

        if (error.status === 403) {
          this.messageService.add({
            severity: 'warn',
            summary: 'Access Denied',
            detail: 'You are not allowed to see these jobs yet.'
          });
        } else if (error.status === 404) {
          this.messageService.add({
            severity: 'info',
            summary: 'No jobs found',
            detail: 'No available jobs at the moment.'
          });
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load available jobs'
          });
        }
      }
    });
  }

  isFormValid(): boolean {
    return !!(
      this.application.job_post_id &&
      this.application.cover_letter &&
      this.application.cover_letter.trim().length > 0 &&
      this.application.resume_file
    );
  }

  onClear() {
    this.application = {
      job_post_id: this.jobIdFromRoute,
      cover_letter: '',
      resume_file: null
    };
    this.submitted = false;
  }

  getSelectedJobTitle(): string {
    const selectedJob = this.availableJobs.find(job => job.id === this.application.job_post_id);
    return selectedJob ? selectedJob.title : '';
  }

  getFileSize(bytes: number): string {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
}
