import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CandidateService, CandidateInfo, Application, GRADIENT_PRESETS } from '../../core/services/candidate.service';
import { AuthService } from '../../core/services/auth.service';
import {environment} from '../../environments/environment';

interface EditCandidateData {
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
  skills: number[];
  resume?: string | File | null;
  cover_gradient?: string;
  country_id?: number;
  city_id?: number;
  address?: string;
  category_id?: number;

}

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
  countries: { id: number; name: string }[] = [];
  cities: { id: number; name: string; country_id: number }[] = [];
  categories: { id: number; name: string }[] = [];
  filteredSkills: { id: number; name: string }[] = [];

  editCandidate: EditCandidateData = {
    skills: []
  };

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
    this.loadSkills();
    this.loadCategories();
    this.loadCountries();
    this.loadProfile();
    this.loadApplications();
  }

  loadCountries(): void {
    this.candidateService.getCountries().subscribe({
      next: (res: any) => {
        this.countries = res.data;
      },
      error: (err) => {
        console.error('Error loading countries:', err);
      }
    });
  }

  onCountryChange(): void {
    if (!this.editCandidate.country_id) {
      this.cities = [];
      this.editCandidate.city_id = undefined;
      return;
    }

    this.candidateService.getCities(this.editCandidate.country_id).subscribe({
      next: (res: any) => {
        this.cities = res.data;
        if (this.editCandidate.city_id && !this.cities.find(c => c.id === this.editCandidate.city_id)) {
          this.editCandidate.city_id = undefined;
        }
      },
      error: (err) => {
        console.error('Error loading cities:', err);
        this.cities = [];
        this.editCandidate.city_id = undefined;
      }
    });
  }

  loadSkills(): void {
    this.candidateService.getSkills().subscribe({
      next: (res: any) => {
        this.skillsList = res.data;
        this.filteredSkills = this.skillsList;
        this.updateCandidateSkillsDetails();
      },
      error: (err) => {
        console.error('Error loading skills:', err);
      }
    });
  }

  private updateCandidateSkillsDetails(): void {
    if (this.candidate?.skills?.length && this.skillsList.length) {
      (this.candidate as any).skills_details = this.skillsList.filter(
        skill => this.candidate!.skills!.includes(skill.id)
      );
    }
  }

  loadProfile(): void {
    this.loading = true;
    this.candidateService.getProfile().subscribe({
      next: (data) => {
        this.candidate = data;

        this.updateCandidateSkillsDetails();

        if (this.candidate?.location?.country_id) {
          this.loadCitiesForCountry(this.candidate.location.country_id);
        }

        // Get gradient from local storage or use default
        const savedGradient = localStorage.getItem(`candidate_${data.user_id}_gradient`);
        this.selectedGradient = savedGradient || 'from-purple-600 via-blue-600 to-indigo-600';

        this.loading = false;
        this.error = null;
      },
      error: (err) => {
        console.error('Error loading profile:', err);
        this.handleProfileError(err);
        this.loading = false;
      }
    });
  }

  private loadCitiesForCountry(countryId: number): void {
    this.candidateService.getCities(countryId).subscribe({
      next: (res: any) => {
        this.cities = res.data;
      },
      error: (err) => {
        console.error('Error loading cities:', err);
      }
    });
  }

  private handleProfileError(err: any): void {
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
    if (!this.candidate) return;

    this.editCandidate = {
      name: this.candidate.user?.name || '',
      email: this.candidate.user?.email || '',
      password: '',
      phone: this.candidate.phone || '',
      bio: this.candidate.bio || '',
      education: this.candidate.education || '',
      experience: this.candidate.experience || '',
      job_title: this.candidate.job_title || '',
      gender: this.candidate.gender || '',
      date_of_birth: this.candidate.date_of_birth || '',
      resume: null,
      cover_gradient: this.candidate.cover_gradient || this.selectedGradient,
      skills: this.candidate.skills_details?.map(s => s.id) || [],
      country_id: this.candidate.location?.country_id || undefined,
      city_id: this.candidate.location?.city_id || undefined,
      category_id: this.candidate.category_id || undefined,
      address: this.candidate.location?.address || ''
    };

    if (this.editCandidate.country_id) {
      this.onCountryChange();
    }


    if (this.editCandidate.country_id) {
      this.loadCitiesForCurrentCountry();
    } else {
      this.cities = [];
    }

    this.filteredSkills = this.skillsList;

    const savedGradient = localStorage.getItem(`candidate_${this.candidate.user_id}_gradient`);
    this.selectedGradient = savedGradient || this.selectedGradient;

    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
    this.showGradientPicker = false;
    this.selectedProfileImage = null;
    this.profileImagePreview = null;
  }

  openGradientPicker(): void {
    this.showGradientPicker = !this.showGradientPicker;
  }

  selectGradient(gradient: string): void {
    this.selectedGradient = gradient;
    this.editCandidate.cover_gradient = gradient;
    this.showGradientPicker = false;

    // Save to local storage
    if (this.candidate?.user_id) {
      localStorage.setItem(`candidate_${this.candidate.user_id}_gradient`, gradient);
    }
  }

  onProfileImageSelected(event: any): void {
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
    const file = event.target.files[0];
    if (file) {
      this.editCandidate.resume = file;
    }
  }

  saveChanges(): void {
    const form = new FormData();

    for (const [key, value] of Object.entries(this.editCandidate)) {
      if (value === null || value === undefined) continue;

      if (value instanceof File) {
        form.append(key, value);
      } else if (Array.isArray(value)) {
        value.forEach((v, i) => form.append(`${key}[${i}]`, String(v)));
      } else {
        form.append(key, String(value));
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
      },
      error: (err) => {
        console.error('Error updating profile:', err);
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
    if (this.candidate?.user?.avatar) {
      if (this.candidate.user.avatar.startsWith('http')) {
        return this.candidate.user.avatar;
      } else {
        return `${environment.imageUrl}/storage/${this.candidate.user.avatar}`;
      }
    }
    if (this.candidate?.profile_image) {
      if (this.candidate.profile_image.startsWith('http')) {
        return this.candidate.profile_image;
      } else {
        return `${environment.imageUrl}/storage/${this.candidate.profile_image}`;
      }
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

  toggleSkill(skillId: number, event: any): void {
    const isChecked = event.target.checked;

    if (isChecked) {
      if (!this.editCandidate.skills.includes(skillId)) {
        this.editCandidate.skills.push(skillId);
      }
    } else {
      this.editCandidate.skills = this.editCandidate.skills.filter(id => id !== skillId);
    }
  }

  isSkillSelected(skillId: number): boolean {
    return this.editCandidate.skills.includes(skillId);
  }

  getCountryName(countryId: number | undefined): string {
    if (!countryId) return '';
    const country = this.countries.find(c => c.id === countryId);
    return country ? country.name : '';
  }

  getCityName(cityId: number | undefined): string {
    if (!cityId) return '';
    const city = this.cities.find(c => c.id === cityId);
    return city ? city.name : '';
  }

  loadCitiesForCurrentCountry(): void {
    if (this.editCandidate.country_id) {
      this.candidateService.getCities(this.editCandidate.country_id).subscribe({
        next: (res: any) => {
          this.cities = res.data;
          // تأكد من أن city_id الحالي موجود في قائمة المدن المحملة
          if (this.editCandidate.city_id && !this.cities.find(c => c.id === this.editCandidate.city_id)) {
            this.editCandidate.city_id = undefined;
          }
        },
        error: (err) => {
          console.error('Error loading cities:', err);
          this.cities = [];
        }
      });
    } else {
      this.cities = [];
    }
  }

  loadCategories(): void {
    this.candidateService.getCategories().subscribe({
      next: (res: any) => {
        this.categories = res.data;
      },
      error: (err) => {
        console.error('Error loading categories:', err);
      }
    });
  }

  onCategoryChange(): void {
    console.log('Selected category:', this.editCandidate.category_id);

    if (this.editCandidate.category_id) {
      this.candidateService.getSkillsByCategory(this.editCandidate.category_id).subscribe({
        next: (res: any) => {
          console.log('Filtered skills:', res.data);
          this.filteredSkills = res.data;
        },
        error: (err) => {
          console.error('Error loading skills by category:', err);
          this.filteredSkills = [];
        }
      });
    } else {
      console.log('No category selected, showing all skills');
      this.filteredSkills = this.skillsList;
    }
  }

  getAddress(): string {
    return this.candidate?.location?.address || '';
  }

  hasLocation(): boolean {
    return !!(this.candidate?.location?.country_id || this.candidate?.location?.city_id);
  }

  getMyCountryName(): string {
    return this.candidate?.location?.country?.name || '';
  }

  getMyCityName(): string {
    return this.candidate?.location?.city?.name || '';
  }
}
