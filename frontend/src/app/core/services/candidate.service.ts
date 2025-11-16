import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';
import {environment} from '../../environments/environment';

export interface CandidateInfo {
  resume?: string | File | null;
  job_title?: string;
  gender?: string;
  date_of_birth?: string;
  skills?: number[];
  skills_details?: { id: number; name: string }[];
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
    avatar?: string;
  };
  applications?: Application[];
  applications_count?: number;
  location?: {
    country_id: number | null;
    city_id: number | null;
    address: string | null;
    country?: { id: number; name: string };
    city?: { id: number; name: string };
  };
  category_id?: number;
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
  private apiUrl = `${environment.apiUrl}/auth/candidate/info`;
  private applicationsApiUrl = `${environment.apiUrl}/applications`;

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
   * Get candidate applications
   */
  getApplications(): Observable<Application[]> {
    return this.http.get<{ success: boolean; data: Application[] }>(this.applicationsApiUrl, {
      headers: this.getHeaders()
    }).pipe(
      map(response => response.data)
    );
  }

  updateProfile(data: FormData) {
    return this.http.post(`${environment.apiUrl}/auth/candidate/info`, data);
  }

  getSkills() {
    return this.http.get<{ data: { id: number; name: string }[] }>(
      `${environment.apiUrl}/skills`
    );
  }
  getCategories() {
    return this.http.get(`${environment.apiUrl}/categories`);
  }

  getSkillsByCategory(categoryId: number) {
    return this.http.get(`${environment.apiUrl}/skills`, {
      params: { category_id: categoryId }
    });
  }

  getCountries() {
    return this.http.get(`${environment.apiUrl}/locations/countries`);
  }

  getCities(countryId: number) {
    return this.http.get(`${environment.apiUrl}/locations/cities`, {
      params: { country_id: countryId }
    });
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
