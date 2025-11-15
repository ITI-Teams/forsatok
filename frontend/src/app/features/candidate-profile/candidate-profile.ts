import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CandidateProfileService, Candidate, Education, Experience, Skill , ContactMessage } from '../../core/services/candidate-profile.service';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-candidate-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './candidate-profile.html',
  styleUrls: ['./candidate-profile.css']
})
export class CandidateProfile implements OnInit {
  activeTab = 'description';
  candidateId: number = 0;
  isLoading = true;
  errorMessage = '';

  candidate: Candidate = {
    id: 0,
    name: '',
    title: '',
    location: null,
    email: '',
    phone: '',
    image: 'https://i.pravatar.cc/300',
    salary: '',
    experience: '',
    languages: [],
  };

  tabs = [
    { id: 'description', label: 'Description' },
    { id: 'education', label: 'Education' },
    { id: 'experience', label: 'Experience' },
    { id: 'skills', label: 'Skills' },
  ];

  description = {
    text: ''
  };

  education: Education[] = [];
  experience: Experience[] = [];
  skills: Skill[] = [];

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
  currentUserId: number = 0;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private candidateService: CandidateProfileService,
    private authService: AuthService
  ) {}

  ngOnInit() {

    // Get current user ID
    const user = this.authService.getUser();
    if (user) {
      this.currentUserId = user.id;
    }
    // Get candidate ID from route params
    this.route.params.subscribe(params => {
      this.candidateId = +params['id'];
      if (this.candidateId) {
        this.loadCandidateProfile();
      } else {
        console.error('No candidate ID provided');
        this.errorMessage = 'No candidate ID provided';
        this.isLoading = false;
      }
    });
  }

  loadCandidateProfile() {
    this.isLoading = true;
    this.errorMessage = '';

    this.candidateService.getCandidate(this.candidateId).subscribe({
      next: (data) => {
        this.candidate = data;
        this.description.text = data.bio || data.description || 'No description available yet.';
        this.education = data.education || [];
        this.experience = data.workExperience || [];
        this.skills = data.skills || [];
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading candidate profile:', err);
        this.errorMessage = err.error?.message || 'Failed to load candidate profile. Please try again.';
        this.isLoading = false;
      }
    });
  }

  setActive(tabId: string) {
    this.activeTab = tabId;
    // Scroll to top of content if needed
    const el = document.querySelector('#tab-content');
    if (el) (el as HTMLElement).scrollTop = 0;
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
        contactable_id: this.candidateId,
        contactable_type: 'App\\Domains\\Employers\\Models\\EmployerInfo',
         user_id: this.currentUserId
      };

      this.candidateService.sendContactMessage(contactData).subscribe({
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


  downloadCV() {
    if (this.candidate.resume) {
      // Assuming resume is stored in Laravel public storage
      const resumeUrl = `http://localhost:8000/storage/${this.candidate.resume}`;
      window.open(resumeUrl, '_blank');
    } else {
      alert('No resume available for this candidate.');
    }
  }


  // Get location string
    getLocationString(): string {
    return this.candidateService.getLocationString(this.candidate.location);
  }
}
