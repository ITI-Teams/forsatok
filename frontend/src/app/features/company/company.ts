import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { EmployerProfileService, Employer, ContactMessage } from '../../core/services/employer-profile.service';

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

  employer: Employer = {
    id: 0,
    user_id: 0,
    company_name: '',
    industry: '',
    location: null,
    about: '',
    website: '',
    created_at: '',
    updated_at: ''
  };

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
    reviewer_name: '',
    reviewer_email: '',
    comment: ''
  };
  reviewStars = [1, 2, 3, 4, 5];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private employerService: EmployerProfileService
  ) {}

  ngOnInit() {
    // Get employer ID from route params
    this.route.params.subscribe(params => {
      this.employerId = +params['id'];
      if (this.employerId) {
        this.loadEmployerProfile();
      } else {
        console.error('No employer ID provided');
        this.errorMessage = 'No employer ID provided';
        this.isLoading = false;
      }
    });
  }

  loadEmployerProfile() {
    this.isLoading = true;
    this.errorMessage = '';

    this.employerService.getEmployer(this.employerId).subscribe({
      next: (data) => {
        this.employer = data;
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading employer profile:', err);
        this.errorMessage = err.error?.message || 'Failed to load employer profile. Please try again.';
        this.isLoading = false;
      }
    });
  }

  setRating(rating: number) {
    this.reviewModel.rating = rating;
  }

  submitContact() {
    if (!this.contactModel.full_name || !this.contactModel.email || !this.contactModel.message) {
      this.contactError = 'Please fill all required fields';
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
      contactable_id: this.employerId,
      contactable_type: 'App\\Domains\\Employers\\Models\\EmployerInfo'
    };

    this.employerService.sendContactMessage(contactData).subscribe({
      next: (response) => {
        console.log('Contact message sent:', response);
        this.contactSuccess = true;
        this.contactModel = { full_name: '', email: '', subject: '', message: '' };
        this.isSubmittingContact = false;

        // Hide success message after 3 seconds
        setTimeout(() => {
          this.contactSuccess = false;
        }, 3000);
      },
      error: (err) => {
        console.error('Error sending contact message:', err);
        this.contactError = err.error?.message || 'Failed to send message. Please try again.';
        this.isSubmittingContact = false;
      }
    });
  }

  submitReview() {
    if (!this.reviewModel.rating || !this.reviewModel.reviewer_name ||
        !this.reviewModel.reviewer_email || !this.reviewModel.comment) {
      alert('Please fill all review fields');
      return;
    }

    // TODO: Implement review submission via API
    console.log('Review submitted:', this.reviewModel);
    alert('Review submitted successfully!');

    // Reset form
    this.reviewModel = {
      rating: 0,
      reviewer_name: '',
      reviewer_email: '',
      comment: ''
    };
  }

  navigateToJob(jobId: number) {
    this.router.navigate(['/jobs', jobId]);
  }

  viewAllJobs() {
    this.router.navigate(['/jobs'], {
      queryParams: { employer_id: this.employerId }
    });
  }

  getLocationString(): string {
    if (!this.employer.location) return 'Location not specified';

    // Check if location has full_location string
    if (this.employer.location.full_location) {
      return this.employer.location.full_location;
    }

    // Build location string from parts
    const parts = [];
    if (this.employer.location.address) {
      parts.push(this.employer.location.address);
    }
    if (this.employer.location.city?.name) {
      parts.push(this.employer.location.city.name);
    }
    if (this.employer.location.country?.name) {
      parts.push(this.employer.location.country.name);
    }

    return parts.length > 0 ? parts.join(', ') : 'Location not specified';
  }

  getLogoUrl(): string {
    if (this.employer.logo_path) {
      return `http://localhost:8000/storage/${this.employer.logo_path}`;
    }
    return 'https://via.placeholder.com/128x128/e0f2fe/0284c7?text=' +
           encodeURIComponent(this.employer.company_name.charAt(0));
  }

  formatSalary(min: number, max: number): string {
    if (!min && !max) return 'Salary not specified';
    if (!max) return `$${min}+`;
    return `$${min} - $${max}`;
  }

  formatDate(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
}
