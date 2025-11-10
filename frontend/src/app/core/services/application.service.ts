// services/application.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { MessageService } from 'primeng/api';

export interface JobApplication {
  id?: number;
  candidate_id: number;
  job_post_id: number;
  cover_letter: string;
  resume_path: string;
  status: 'pending' | 'accepted' | 'rejected';
  created_at?: string;
  updated_at?: string;
}

export interface JobPost {
  id: number;
  title: string;
  company: string;
  location: string;
  salary_range: string;
  description: string;
  deadline: string;
  is_active: boolean;
}

export interface ApplicationsResponse {
  success: boolean;
  data: JobApplication[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  message: string;
}

export interface ApplicationStats {
  total: number;
  pending: number;
  accepted: number;
  rejected: number;
}

export interface StatsResponse {
  success: boolean;
  data: ApplicationStats;
  message: string;
}

export interface AvailableJobsResponse {
  success: boolean;
  data: JobPost[];
  message: string;
}

@Injectable({
  providedIn: 'root'
})
export class ApplicationService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(
    private http: HttpClient,
    private messageService: MessageService
  ) {}

  getApplications(page: number = 1, perPage: number = 10): Observable<ApplicationsResponse> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('per_page', perPage.toString());

    return this.http.get<ApplicationsResponse>(`${this.apiUrl}/applications`, { params });
  }

  getApplication(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/applications/${id}`);
  }

  getStats(): Observable<StatsResponse> {
    return this.http.get<StatsResponse>(`${this.apiUrl}/applications/stats`);
  }

  getAvailableJobs(): Observable<AvailableJobsResponse> {
    return this.http.get<AvailableJobsResponse>(`${this.apiUrl}/applications/available-jobs`);
  }

  submitApplication(applicationData: FormData): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/applications`, applicationData);
  }

  showSuccess(message: string) {
    this.messageService.add({
      severity: 'success',
      summary: 'Success',
      detail: message
    });
  }

  showError(message: string) {
    this.messageService.add({
      severity: 'error',
      summary: 'Error',
      detail: message
    });
  }
}
