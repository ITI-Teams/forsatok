import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { EmployerProfileService, Employer, ContactMessage, Job } from '../../core/services/employer-profile.service';
import { CompanyReviewService, CompanyReview, ReviewSubmit } from '../../core/services/employer-review.service';
import { AuthService } from '../../core/services/auth.service';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-company',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './company.html',
  styleUrls: ['./company.css']
})
export class Company implements OnInit {
  employerId: number = 0;
  isLoading = true;
  errorMessage = '';
  isLoadingJobs = false;

  // employer
  employer: Employer = {
    id: 0,
    user_id: 0,
    company_name: '',
    industry: '',
    location: null,
    about: '',
    website: '',
    created_at: '',
    updated_at: '',
    jobs: [],
    jobs_count: 0
  };

  allJobs: Job[] = [];

  // Reviews
  reviews: CompanyReview[] = [];
  isLoadingReviews = false;
  currentUserId: number = 0;

  // Contact form model
  contactModel = {
    full_name: '',
    email: '',
    subject: '',
    message: ''
  };
  isSubmittingContact = false;
  contactSuccess = false;
  contactError = '';

  // Review form model
  reviewModel = {
    rating: 0,
    review: ''
  };
  reviewStars = [1, 2, 3, 4, 5];
  isSubmittingReview = false;
  reviewError = '';
  reviewSuccess = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private employerService: EmployerProfileService,
    private reviewService: CompanyReviewService,
    private authService: AuthService
  ) { }

  ngOnInit() {
    // Get current user ID
    const user = this.authService.getUser();
    if (user) {
      this.currentUserId = user.id;
    }

    // Get employer ID from route params
    this.route.params.subscribe(params => {
      this.employerId = +params['id'];
      if (this.employerId) {
        this.loadEmployerProfile();
        this.loadReviews();
      } else {
        console.error('No employer ID provided');
        this.errorMessage = 'No employer ID provided';
        this.isLoading = false;
      }
    });
  }

  // load the clicked company info
  loadEmployerProfile() {
    this.isLoading = true;
    this.errorMessage = '';

    this.employerService.getEmployer(this.employerId).subscribe({
      next: (data) => {
        this.employer = {
          ...data,
          jobs: data.jobs || [],
          jobs_count: data.jobs_count || 0
        };

        console.log('Employer loaded:', this.employer); // Debug log

        // CRITICAL: Load jobs using user_id after employer data is available
        if (!data.jobs || data.jobs.length === 0) {
          this.loadEmployerJobs();
        }

        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading employer profile:', err);
        this.errorMessage = err.error?.message || 'Failed to load employer profile. Please try again.';
        this.isLoading = false;
      }
    });
  }

  // Load jobs separately if not included in employer data
  loadEmployerJobs() {
    this.isLoadingJobs = true;

    // CRITICAL: Use user_id, not employer id
    const employerIdForJobs = this.employer.user_id;

    if (!employerIdForJobs) {
      console.error('No user_id available for employer');
      this.isLoadingJobs = false;
      return;
    }

    console.log('Loading jobs for user_id:', employerIdForJobs); // Debug log

    this.employerService.getEmployerJobs(employerIdForJobs, 1, 50).subscribe({
      next: (response) => {
        console.log('Jobs response:', response); // Debug log
        this.employer.jobs = response.data || [];
        this.employer.jobs_count = response.total || response.data?.length || 0;
        this.isLoadingJobs = false;
      },
      error: (err) => {
        console.error('Error loading employer jobs:', err);
        this.employer.jobs = [];
        this.employer.jobs_count = 0;
        this.isLoadingJobs = false;
      }
    });
  }

  // load the written reviews
  loadReviews() {
    this.isLoadingReviews = true;

    this.reviewService.getCompanyReviews(this.employerId).subscribe({
      next: (data) => {
        this.reviews = data;
        this.isLoadingReviews = false;
      },
      error: (err) => {
        console.error('Error loading reviews:', err);
        this.isLoadingReviews = false;
      }
    });
  }

  // set the rating
  setRating(rating: number) {
    this.reviewModel.rating = rating;
  }

  // submit the given review
  submitReview() {
    if (!this.reviewModel.rating || !this.reviewModel.review) {
      this.reviewError = 'Please provide a rating and write a review';
      return;
    }

    if (this.reviewModel.review.length < 10) {
      this.reviewError = 'Review must be at least 10 characters long';
      return;
    }

    this.isSubmittingReview = true;
    this.reviewSuccess = false;
    this.reviewError = '';

    const reviewData: ReviewSubmit = {
      company_id: this.employerId,
      candidate_id: this.currentUserId,
      rating: this.reviewModel.rating,
      review: this.reviewModel.review
    };

    this.reviewService.submitReview(reviewData).subscribe({
      next: (response) => {
        console.log('Review submitted:', response);
        this.reviewSuccess = true;
        this.reviewModel = { rating: 0, review: '' };
        this.isSubmittingReview = false;

        this.loadReviews();
        this.loadEmployerProfile();

        setTimeout(() => {
          this.reviewSuccess = false;
        }, 3000);
      },
      error: (err) => {
        console.error('Error submitting review:', err);

        // Handle different error types
        if (err.status === 409) {
          // Duplicate review error
          this.reviewError = 'You have already reviewed this company. Only one review per company is allowed.';
        } else if (err.status === 422 && err.error?.errors) {
          // Validation errors
          const errors = Object.values(err.error.errors).flat();
          this.reviewError = errors.join(', ');
        } else if (err.error?.message) {
          // Generic error message from backend
          this.reviewError = err.error.message;
        } else {
          // Fallback error message
          this.reviewError = 'Failed to submit review. Please try again.';
        }

        this.isSubmittingReview = false;
      }
    });
  }

  navigateToJob(jobId: number) {
    this.router.navigate(['/job', jobId]);
  }

  viewAllJobs() {
    // CRITICAL: Use user_id because job_posts.employer_id references users.id
    const employerIdForJobs = this.employer.user_id;

    if (!employerIdForJobs) {
      console.error('No user_id available for employer');
      return;
    }

    this.router.navigate(['/job'], {
      queryParams: { employer_id: employerIdForJobs }
    });
  }

  getLocationString(): string {
    return this.employerService.getLocationString(this.employer.location);
  }

  getLogoUrl(): string {
    if (this.employer.user?.avatar) {
      return this.employer.user.avatar;
    }
    return 'assets/images/default-avatar.svg'; // Or whatever fallback you prefer
  }

  formatSalary(min: number, max: number): string {
    if (!min && !max) return 'Salary not specified';
    if (!max) return `$${min}+`;
    return `$${min} - $${max}`;
  }

  formatDate(dateString: string): string {
    if (!dateString) return '';

    try {
      const date = new Date(dateString);

      // Check if date is valid
      if (isNaN(date.getTime())) {
        console.warn('Invalid date:', dateString);
        return 'Date not available';
      }

      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    } catch (error) {
      console.error('Error formatting date:', error, dateString);
      return 'Date not available';
    }
  }

  getJobLocationString(job: Job): string {
    if (job.location) {
      return this.employerService.getLocationString(job.location);
    }
    return this.getLocationString();
  }

  getAverageRating(): number {
    return this.reviewService.calculateAverageRating(this.reviews);
  }


  // Check if current user has already reviewed this company
  hasUserReviewed(): boolean {
    if (!this.currentUserId || !this.reviews || this.reviews.length === 0) {
      return false;
    }

    return this.reviews.some(review =>
      review.candidate_id === this.currentUserId
    );
  }

  // Submit contact form
  submitContact() {
    // Validate form
    if (!this.contactModel.full_name || !this.contactModel.email || !this.contactModel.message) {
      this.contactError = 'Please fill in all required fields';
      return;
    }

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(this.contactModel.email)) {
      this.contactError = 'Please enter a valid email address';
      return;
    }

    this.isSubmittingContact = true;
    this.contactSuccess = false;
    this.contactError = '';

    const contactData: ContactMessage = {
      full_name: this.contactModel.full_name,
      email: this.contactModel.email,
      subject: this.contactModel.subject,
      message: this.contactModel.message,
      contactable_id: this.employer.user_id,
      contactable_type: 'App\\Domains\\Users\\Models\\User',
      user_id: this.currentUserId || undefined
    };

    this.employerService.sendContactMessage(contactData).subscribe({
      next: (response) => {
        console.log('Contact message sent:', response);
        this.contactSuccess = true;

        // Reset form
        this.contactModel = {
          full_name: '',
          email: '',
          subject: '',
          message: ''
        };

        this.isSubmittingContact = false;

        // Hide success message after 5 seconds
        setTimeout(() => {
          this.contactSuccess = false;
        }, 5000);
      },
      error: (err) => {
        console.error('Error sending contact message:', err);

        if (err.status === 422 && err.error?.errors) {
          // Validation errors
          const errors = Object.values(err.error.errors).flat();
          this.contactError = errors.join(', ');
        } else if (err.error?.message) {
          this.contactError = err.error.message;
        } else {
          this.contactError = 'Failed to send message. Please try again.';
        }

        this.isSubmittingContact = false;
      }
    });
  }
}
