import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { JobService, Job } from '../../core/services/job.service';

type JobDetail = Job & {
  id?: number | null;
  requirements?: string | null;
  responsibilities?: string | null;
  qualification?: string | null;
  benefits?: string | null;
};

@Component({
  selector: 'app-job-details',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './job-details.html',
  styleUrls: ['./job-details.css'],
})
export class JobDetails implements OnInit {
  job: JobDetail | null = null;
  similarJobs: JobDetail[] = [];
  loading = true;
  error: string | null = null;
  safeMapUrl: SafeResourceUrl | null = null;

  constructor(
    private route: ActivatedRoute,
    public router: Router,
    private jobService: JobService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.route.paramMap.subscribe((params) => {
      const idParam = params.get('id');
      if (idParam) {
        const jobId = parseInt(idParam, 10);
        // reset state when navigating within the same component
        this.job = null;
        this.similarJobs = [];
        this.safeMapUrl = null;
        this.error = null;
        this.loadJobDetails(jobId);
      } else {
        this.error = 'Job ID not found';
        this.loading = false;
      }
    });
  }

  loadJobDetails(id: number) {
    this.loading = true;
    this.jobService.getJobDetails(id).subscribe({
      next: (response) => {
        this.job = response.data;
        this.loading = false;

        // Generate map URL from location
        if (this.job?.locationable?.city) {
          const city = this.job.locationable.city;
          const country = city.country || this.job.locationable.country;
          const location = `${city.name}, ${country?.name || ''}`;
          const mapUrl = this.generateMapUrl(location);
          this.safeMapUrl = this.sanitizer.bypassSecurityTrustResourceUrl(mapUrl);
        } else if (this.job?.location) {
          const mapUrl = this.generateMapUrl(this.job.location);
          this.safeMapUrl = this.sanitizer.bypassSecurityTrustResourceUrl(mapUrl);
        }

        // Load similar jobs after job is loaded
        this.loadSimilarJobs(id);
      },
      error: (err) => {
        console.error('Error loading job details:', err);
        this.error = 'Failed to load job details';
        this.loading = false;
      },
    });
  }

  loadSimilarJobs(currentJobId: number) {
    // Wait for job to load first, then load similar jobs
    if (this.job?.category?.id) {
      this.jobService.getJobs({
        category_id: this.job.category.id,
        work_type: this.job.work_type,
        per_page: 4
      }).subscribe({
        next: (response) => {
          this.similarJobs = response.data.data
            .filter(job => job.id !== currentJobId)
            .slice(0, 3);
        },
        error: (err) => {
          console.error('Error loading similar jobs:', err);
        },
      });
    }
  }

  generateMapUrl(location: string): string {
    const encodedLocation = encodeURIComponent(location);
    // return `https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dS6FG4Q0iDWJ8&q=${encodedLocation}`;
    return `https://www.google.com/maps/embed/v1/place?key=AIzaSyD5Vv7LJxdp4O7w-S0oA9jFY1iIb2JxW8s&q=${encodedLocation}`;
  }

  formatSalary(): string {
    if (!this.job) return 'Not specified';
    return this.jobService.formatSalary(this.job);
  }

  formatLocation(): string {
    if (!this.job) return 'Not specified';
    return this.jobService.formatJobLocation(this.job);
  }

  getJobType(): string {
    if (!this.job) return 'Not specified';
    return this.jobService.getWorkType(this.job);
  }

  getWorkPlace(): string {
    if (!this.job) return 'Not specified';
    return this.jobService.getWorkPlace(this.job);
  }

  applyForJob() {
    if (this.job) {
      this.router.navigate(['/apply', this.job.id]);
    }
  }

  viewJob(id: number) {
    this.router.navigate(['/job', id]);
  }

  formatLocationForJob(job: Job): string {
    return this.jobService.formatJobLocation(job);
  }

  formatSalaryForJob(job: Job): string {
    return this.jobService.formatSalary(job);
  }

  getJobTypeForJob(job: Job): string {
    return this.jobService.getWorkType(job);
  }

  getWorkPlaceForJob(job: Job): string {
    return this.jobService.getWorkPlace(job);
  }

  splitLines(value?: string | null): string[] {
    if (!value) {
      return [];
    }
    return value
      .split(/\r?\n/)
      .map((line) => line.trim())
      .filter((line) => line.length > 0);
  }

  getExcerpt(text?: string | null, maxLength = 140): string {
    if (!text) {
      return '';
    }

    const cleanText = text.replace(/\s+/g, ' ').trim();
    if (cleanText.length <= maxLength) {
      return cleanText;
    }

    return cleanText.substring(0, maxLength).trimEnd() + '…';
  }
}
