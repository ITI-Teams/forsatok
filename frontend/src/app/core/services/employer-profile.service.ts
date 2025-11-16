import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders ,HttpParams} from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';

export interface Employer {
  id: number;
  user_id: number;
  company_name: string;
  industry: string;
  location: Location | null;
  about: string;
  website: string;
  logo_path?: string;
  created_at: string;
  updated_at: string;
  user?: {
    id: number;
    name: string;
    email: string;
  };
  jobs?: Job[];
  jobs_count?: number;
  average_rating?: number;
  total_reviews?: number;
}

export interface Location {
  country_id?: number;
  city_id?: number;
  address?: string;
  country?: {
    id: number;
    name: string;
    code?: string;
  };
  city?: {
    id: number;
    name: string;
  };
  full_location?: string;
}

export interface Job {
  id: number;
  title: string;
  experience: string;
  description: string;
  salary_min: number;
  salary_max: number;
  deadline: string;
  is_active: boolean;
  created_at: string;
  work_type?: string;
  work_place?: string;
  location?: Location;
  category?: {
    id: number;
    name: string;
  };
}

export interface ContactMessage {
  full_name: string;
  email: string;
  subject?: string;
  message: string;
  contactable_id?: number;
  contactable_type?: string;
  user_id?: number;
}

@Injectable({
  providedIn: 'root'
})
export class EmployerProfileService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(
    private http: HttpClient,
    private authService: AuthService
  ) {}

  private getHeaders(): HttpHeaders {
    const token = this.authService.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
  }

  // Get single employer by ID
  getEmployer(id: number): Observable<Employer> {
    return this.http.get<any>(`${this.apiUrl}/auth/employerinfo/${id}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success && response.data) {
          return response.data;
        }
        throw new Error(response.message || 'Failed to load employer');
      })
    );
  }

  // Get all employers with pagination
  // getEmployers(page: number = 1, perPage: number = 10): Observable<any> {
  //   return this.http.get<any>(`${this.apiUrl}/auth/employerinfo?page=${page}&per_page=${perPage}`, {
  //     headers: this.getHeaders()
  //   }).pipe(
  //     map(response => {
  //       if (response.success) {
  //         return {
  //           employers: response.data,
  //           meta: response.meta
  //         };
  //       }
  //       throw new Error(response.message || 'Failed to load employers');
  //     })
  //   );
  // }

// Get employer's jobs
  getEmployerJobs(employerId: number, page: number = 1, perPage: number = 10): Observable<any> {
    // Create params object
    let params = new HttpParams()
      .set('employer_id', employerId.toString())
      .set('page', page.toString())
      .set('per_page', perPage.toString());

    return this.http.get<any>(`${this.apiUrl}/jobs`, {
      headers: this.getHeaders(),
      params: params
    }).pipe(
      map(response => {
        if (response.status) {
          return {
            data: response.data.data || [],
            total: response.data.total || 0,
            current_page: response.data.current_page || 1,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || perPage
          };
        }
        throw new Error('Failed to load employer jobs');
      })
    );
  }

  // Send contact message to employer
    sendContactMessage(contactData: ContactMessage): Observable<any> {
    const payload = {
      ...contactData,
      contactable_type: 'App\\Domains\\Users\\Models\\User'
    };

    return this.http.post<any>(`${this.apiUrl}/contact`, payload, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success) {
          return response;
        }
        throw new Error(response.message || 'Failed to send message');
      })
    );
  }


    // Helper method to get full location string
  getLocationString(location: Location | null): string {
    if (!location) return 'Location not specified';

    if (location.full_location) {
      return location.full_location;
    }

    const parts = [];
    if (location.address) parts.push(location.address);
    if (location.city?.name) parts.push(location.city.name);
    if (location.country?.name) parts.push(location.country.name);

    return parts.length > 0 ? parts.join(', ') : 'Location not specified';
  }

}
