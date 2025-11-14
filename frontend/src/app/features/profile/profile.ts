import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CandidateService, CandidateInfo, Application, GRADIENT_PRESETS } from '../../core/services/candidate.service';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './profile.html',
  styleUrls: ['./profile.css'],
})
export class Profile implements OnInit {
  candidate: CandidateInfo | null = null;
  applications: Application[] = [];
  loading = true;
  error: string | null = null;
  skillsList: { id: number; name: string }[] = [];

  editCandidate: {
    name?: string;
    email?: string;
    password?: string;

    phone?: string;
    bio?: string;
    education?: string;
    experience?: string;
    job_title?: string;
    gender?: string;
    date_of_birth?: string;

    skills?: number[];
    resume?: string | File | null;

    cover_gradient?: string;
  } = {};
  showModal = false;
  showGradientPicker = false;
  selectedGradient = 'from-purple-600 via-blue-600 to-indigo-600';
  gradients = GRADIENT_PRESETS;
  selectedProfileImage: File | null = null;
  profileImagePreview: string | null = null;

  constructor(
    private candidateService: CandidateService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadProfile();
    this.loadApplications();
    this.loadSkills();
  }

  loadSkills() {
    this.candidateService.getSkills().subscribe((res: any) => {
      this.skillsList = res.data;
    });

  }

  loadProfile(): void {
    this.loading = true;
    this.candidateService.getProfile().subscribe({
      next: (data) => {
        this.candidate = data;

        if (this.candidate?.skills && this.skillsList?.length) {
          (this.candidate as any).skills_details = this.skillsList.filter(
            skill => this.candidate!.skills!.includes(skill.id)
          );
        }
        // Get gradient from local storage or use default (frontend only)
        const savedGradient = localStorage.getItem(`candidate_${data.user_id}_gradient`);
        this.selectedGradient = savedGradient || 'from-purple-600 via-blue-600 to-indigo-600';

        this.loading = false;
        this.error = null;
      },
      error: (err) => {
        console.error('Error loading profile:', err);

        // Better error messages
        if (err.status === 404) {
          this.error = 'Profile not found. Please create your profile first.';
        } else if (err.status === 403) {
          this.error = 'Unauthorized. Please login as a candidate.';
        } else if (err.status === 401) {
          this.error = 'Please login to view your profile.';
        } else if (err.status === 0) {
          this.error = 'Cannot connect to server. Please check your connection.';
        } else {
          this.error = err.error?.message || 'Failed to load profile. Please try again.';
        }

        this.loading = false;
      }
    });
  }

  loadApplications(): void {
    this.candidateService.getApplications().subscribe({
      next: (data) => {
        this.applications = data.map((app: any) => ({
          ...app,
          job: app.job_post || app.job,
          applied_at: app.applied_at || app.applied_date
        }));
      },
      error: (err) => {
        console.error('Error loading applications:', err);
      }
    });
  }

  openEditModal(): void {
    if (this.candidate) {
      this.editCandidate = {
        ...this.candidate,
        name: this.candidate.user?.name,
        email: this.candidate.user?.email,
        password: ''
      };
      // Get gradient from local storage or candidate data
      const savedGradient = localStorage.getItem(`candidate_${this.candidate.user_id}_gradient`);
      this.selectedGradient = savedGradient || 'from-purple-600 via-blue-600 to-indigo-600';
      this.showModal = true;
    }
  }

  closeModal(): void {
    this.showModal = false;
    this.showGradientPicker = false;
    this.selectedProfileImage = null;
    this.profileImagePreview = null;
    if (this.candidate) {
      this.editCandidate = { ...this.candidate };
    }
  }

  openGradientPicker(): void {
    this.showGradientPicker = !this.showGradientPicker;
  }

  selectGradient(gradient: string): void {
    this.selectedGradient = gradient;
    this.editCandidate.cover_gradient = gradient;
    this.showGradientPicker = false;

    // Save to local storage (frontend only for now)
    if (this.candidate?.user_id) {
      localStorage.setItem(`candidate_${this.candidate.user_id}_gradient`, gradient);
    }
  }

  onProfileImageSelected(event: any): void {
    // Frontend only - no logic for now
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
      this.selectedProfileImage = file;
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.profileImagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  onResumeSelected(event: any): void {
    // Frontend only - no logic for now
    const file = event.target.files[0];
    if (file) {
      this.editCandidate.resume = file;
    }
  }

  saveChanges() {
    const form = new FormData();

    for (const [key, value] of Object.entries(this.editCandidate)) {
      if (value === null || value === undefined) continue;

      if (value instanceof File) {
        form.append(key, value);
      } else if (Array.isArray(value)) {
        value.forEach((v, i) => form.append(`${key}[${i}]`, String(v)));
      } else {
        form.append(key, String(value)); // force convert to string
      }
    }

    if (this.selectedProfileImage) {
      form.append('avatar', this.selectedProfileImage);
    }

    if (this.editCandidate.resume instanceof File) {
      form.append('resume', this.editCandidate.resume);
    }

    this.candidateService.updateProfile(form).subscribe({
      next: () => {
        this.loadProfile();
        this.closeModal();
      }
    });
  }

  getInitials(): string {
    if (!this.candidate?.user?.name) return 'U';
    const names = this.candidate.user.name.split(' ');
    if (names.length >= 2) {
      return (names[0][0] + names[1][0]).toUpperCase();
    }
    return this.candidate.user.name.substring(0, 2).toUpperCase();
  }

  getCoverGradient(): string {
    return this.candidate?.cover_gradient || this.selectedGradient || 'from-purple-600 via-blue-600 to-indigo-600';
  }

  getProfileImageUrl(): string | null {
    if (this.profileImagePreview) {
      return this.profileImagePreview;
    }
    if (this.candidate?.profile_image) {
      return this.candidate.profile_image;
    }
    return null;
  }

  viewResume(): void {
    if (this.candidate?.resume_url) {
      window.open(this.candidate.resume_url, '_blank');
    }
  }

  getResumeName(): string {
    if (this.candidate?.resume_url) {
      return this.candidate.resume_url.split('/').pop() || 'Resume.pdf';
    }
    return 'No Resume Uploaded';
  }

  getStatusColor(status: string): string {
    switch (status.toLowerCase()) {
      case 'accepted':
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
      case 'rejected':
        return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
  }

  getResumeFileName(): string {
    if (this.editCandidate.resume && this.editCandidate.resume instanceof File) {
      return this.editCandidate.resume.name;
    }
    return 'Resume.pdf';
  }

  getAge(dateString: string | undefined): number | null {
    if (!dateString) return null;

    const birth = new Date(dateString);
    const today = new Date();

    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--;
    }

    return age;
  }

}
