import { Component , Input} from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { HomeService } from '../../../core/services/home.service';
import { ToastService } from '../../../core/services/toast.service';


@Component({
  selector: 'app-featured-jobs',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './featured-jobs.html',
  styleUrl: './featured-jobs.css',
})
export class FeaturedJobs {
  @Input() jobs: Array<{
    id?: number;
    title?: string;
    company?: string;
    location?: string;
    salary_min?: number;
    salary_max?: number;
    work_type?: string;
    deadline?: string;
    saved?: boolean;
  }> = [];

  constructor(
    private router: Router,
    private homeService: HomeService,
    private toastService: ToastService
  ) {}


  viewJob(id: number | undefined) {
    if (id) {
      this.router.navigate(['/job', id]);
    } else {
      this.router.navigate(['/jobs']);
    }
  }

  toggleSaveJob(job: any, event: Event): void {
    event.stopPropagation();

    if (!job.id) {
      this.showToast('Error: Job ID is missing', 'error');
      return;
    }

    if (job.saved) {
      this.saveJob(job);
    } else {
      this.saveJob(job);
    }
  }

  private saveJob(job: any): void {
    this.homeService.saveJob(job.id).subscribe({
      next: (response: any) => {
        job.saved = response.saved !== undefined ? response.saved : true;
        const message = response.message || 'Job saved successfully';
        this.showToast(message, 'success');
      },
      error: (error) => {
        this.handleSaveError(error);
      }
    });
  }

  private unsaveJob(job: any): void {
    this.homeService.unsaveJob(job.id).subscribe({
      next: (response: any) => {
        job.saved = response.saved !== undefined ? response.saved : false;
        const message = response.message || 'Job unsaved successfully';
        this.showToast(message, 'success');
      },
      error: (error) => {
        this.handleSaveError(error);
      }
    });
  }

  private handleSaveError(error: any): void {
    if (error.status === 422) {
      this.showToast('Error: Invalid data sent to server', 'error');
    } else if (error.status === 401) {
      this.showToast('Please login to save jobs', 'warning');
    } else if (error.status === 500) {
      this.showToast('Server error. Please try again later.', 'error');
    } else {
      this.showToast('Error saving job. Please try again.', 'error');
    }
  }

  private showToast(message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info'): void {
    this.toastService.show({ message, type });
  }

  formatDate(dateString: string | undefined): string {
    if (!dateString) return '';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';

    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }

  isUrgent(job: any): boolean {
    if (!job.deadline) return false;
    const deadline = new Date(job.deadline);
    const now = new Date();
    const diffTime = deadline.getTime() - now.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays <= 3;
  }

  formatSalary(salary: number | undefined): string {
    if (!salary) return '';
    return `$${(salary / 1000).toFixed(0)}k`;
  }

  getCompanyLogo(company: string | undefined): string {
    if (!company) return 'building';
    return 'building';
  }

  showjobs(){
    this.router.navigate(['/jobs']);
  }

}
