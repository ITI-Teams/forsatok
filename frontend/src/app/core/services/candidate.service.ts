import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';

export interface CandidateInfo {
  id?: number;
  user_id?: number;
  phone?: string;
  education?: string;
  experience?: string;
  bio?: string;
  resume_url?: string;
  cover_gradient?: string;
  profile_image?: string;
  created_at?: string;
  updated_at?: string;
  user?: {
    id: number;
    name: string;
    email: string;
  };
  applications?: Application[];
  applications_count?: number;
}

export interface Application {
  id: number;
  job_id?: number;
  status: string;
  applied_at?: string;
  applied_date?: string;
  job?: {
    id: number;
    title: string;
    employer?: {
      id?: number;
      name?: string;
      company?: string;
    };
  };
  job_post?: {
    id: number;
    title: string;
    employer?: {
      id?: number;
      name?: string;
      company?: string;
    };
  };
}

export interface CandidateInfoResponse {
  success: boolean;
  data: CandidateInfo;
  message?: string;
}

export interface UpdateCandidateInfoRequest {
  name?: string;
  email?: string;
  password?: string;
  phone?: string;
  education?: string;
  experience?: string;
  bio?: string;
  resume?: File;
  cover_gradient?: string;
  profile_image?: File;
}

@Injectable({
  providedIn: 'root'
})
export class CandidateService {
  private apiUrl = 'http://localhost:8000/api/auth/candidate/info';
  private applicationsApiUrl = 'http://localhost:8000/api/applications';

  constructor(
    private http: HttpClient,
    private authService: AuthService
  ) {}

  private getHeaders(): HttpHeaders {
    const token = this.authService.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  /**
   * Get current candidate profile
   */
  getProfile(): Observable<CandidateInfo> {
    const headers = this.getHeaders();

    // If no token, return error observable
    if (!this.authService.getToken()) {
      return new Observable(observer => {
        observer.error({ status: 401, error: { message: 'No authentication token found' } });
      });
    }

    return this.http.get<CandidateInfoResponse>(this.apiUrl, { headers }).pipe(
      map(response => {
        if (response.success && response.data) {
          return response.data;
        }
        throw new Error(response.message || 'Failed to load profile');
      })
    );
  }

  /**
   * Update current candidate profile
   */
  updateProfile(data: UpdateCandidateInfoRequest): Observable<CandidateInfo> {
    const formData = new FormData();

    if (data.name) formData.append('name', data.name);
    if (data.email) formData.append('email', data.email);
    if (data.password) formData.append('password', data.password);
    if (data.phone) formData.append('phone', data.phone);
    if (data.education) formData.append('education', data.education);
    if (data.experience) formData.append('experience', data.experience);
    if (data.bio) formData.append('bio', data.bio);
    if (data.cover_gradient) formData.append('cover_gradient', data.cover_gradient);
    if (data.profile_image) formData.append('profile_image', data.profile_image);
    if (data.resume) formData.append('resume', data.resume);

    formData.append('_method', 'POST');

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.authService.getToken()}`
    });

    return this.http.post<CandidateInfoResponse>(this.apiUrl, formData, { headers }).pipe(
      map(response => {
        if (response.success && response.data) {
          return response.data;
        }
        throw new Error(response.message || 'Failed to update profile');
      })
    );
  }

  /**
   * Get candidate applications
   */
  getApplications(): Observable<Application[]> {
    return this.http.get<{ success: boolean; data: Application[] }>(this.applicationsApiUrl, {
      headers: this.getHeaders()
    }).pipe(
      map(response => response.data)
    );
  }
}

// Gradient presets
export const GRADIENT_PRESETS = [
  { name: 'Purple Blue', value: 'from-purple-600 via-blue-600 to-indigo-600' },
  { name: 'Pink Orange', value: 'from-pink-500 via-rose-500 to-orange-500' },
  { name: 'Green Teal', value: 'from-green-500 via-emerald-500 to-teal-500' },
  { name: 'Blue Cyan', value: 'from-blue-500 via-cyan-500 to-sky-500' },
  { name: 'Purple Pink', value: 'from-purple-500 via-pink-500 to-rose-500' },
  { name: 'Orange Red', value: 'from-orange-500 via-red-500 to-pink-500' },
  { name: 'Indigo Purple', value: 'from-indigo-600 via-purple-600 to-pink-600' },
  { name: 'Teal Blue', value: 'from-teal-500 via-blue-500 to-indigo-500' },
  { name: 'Yellow Orange', value: 'from-yellow-400 via-orange-500 to-red-500' },
  { name: 'Violet Purple', value: 'from-violet-500 via-purple-500 to-fuchsia-500' },
];
