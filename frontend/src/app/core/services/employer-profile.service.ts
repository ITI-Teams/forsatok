import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';

export interface Employer {
  id: number;
  user_id: number;
  company_name: string;
  industry: string;
  location: any;
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
}

export interface ContactMessage {
  full_name: string;
  email: string;
  subject?: string;
  message: string;
  contactable_id?: number;
  contactable_type?: string;
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
  getEmployers(page: number = 1, perPage: number = 10): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/auth/employerinfo?page=${page}&per_page=${perPage}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success) {
          return {
            employers: response.data,
            meta: response.meta
          };
        }
        throw new Error(response.message || 'Failed to load employers');
      })
    );
  }

  // Send contact message to employer
  sendContactMessage(contactData: ContactMessage): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/contact`, contactData, {
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
}
