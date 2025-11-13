import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CandidateService, Candidate, Education, Experience, Skill } from '../../core/services/candidate-profile.service';

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

  candidate: Candidate = {
    id: 0,
    name: '',
    title: '',
    location: '',
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

  // Contact form model (template-driven)
  contactModel = { name: '', email: '', message: '' };

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private candidateService: CandidateService
  ) {}

  ngOnInit() {
    // Get candidate ID from route params
    this.route.params.subscribe(params => {
      this.candidateId = +params['id']; // Convert string to number
      if (this.candidateId) {
        this.loadCandidateProfile();
      } else {
        console.error('No candidate ID provided');
        this.isLoading = false;
      }
    });
  }

  loadCandidateProfile() {
    this.isLoading = true;
    this.candidateService.getCandidate(this.candidateId).subscribe({
      next: (data) => {
        this.candidate = data;
        this.description.text = data.description || 'No description available.';
        this.education = data.education || [];
        this.experience = data.workExperience || [];
        this.skills = data.skills || [];
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading candidate profile:', err);
        this.isLoading = false;
        alert('Failed to load candidate profile. Please try again.');
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
    if (!this.contactModel.name || !this.contactModel.email || !this.contactModel.message) {
      alert('Please fill all fields');
      return;
    }

    console.log('Contact submit:', this.contactModel);
    // TODO: Implement API call to send message to candidate
    alert('Message sent successfully!');
    this.contactModel = { name: '', email: '', message: '' };
  }

  downloadCV() {
    // TODO: Implement CV download functionality
    console.log('Download CV for candidate:', this.candidateId);
    alert('CV download feature coming soon!');
  }

  shortlistCandidate() {
    // TODO: Implement shortlist functionality
    console.log('Shortlist candidate:', this.candidateId);
    alert('Candidate shortlisted!');
  }
}
