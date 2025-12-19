import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import {environment} from '../../environments/environment';

export interface JobLocation {
  city?: {
    id: number;
    name: string;
    country_id: number;
    country?: {
      id: number;
      name: string;
      code?: string;
    };
  };
  country?: {
    id: number;
    name: string;
    code?: string;
  };
  address?: string;
}

export interface Job {
  id: number;
  title: string;
  description?: string;
  requirements?: string;
  responsibilities?: string;
  qualification?: string;
  benefits?: string;
  location?: string;
  locationable?: JobLocation;
  salary_min?: number;
  salary_max?: number;
  work_type?: string;
  work_place?: string;
  experience?: string;
  deadline?: string;
  is_active?: boolean;
  category?: {
    id: number;
    name: string;
  };
  employer?: {
    id: number;
    name: string;
    email: string;
  };
  skills?: {
    id: number;
    name: string;
    slug: string;
  }[];
  created_at?: string;
  updated_at?: string;
}

export interface JobFilterOptions {
  types: { value: string; name: string; count: number }[];
  experience_levels: { value: string; name: string; count: number }[];
  work_places: { value: string; name: string; count: number }[];
  skills: { id: number; name: string; slug: string; jobs_count: number }[];
  salary_range: { min: number; max: number };
}

export interface JobFilters {
  search?: string;
  location?: string;
  city_id?: number;
  country_id?: number;
  category_id?: number;
  type?: string;
  work_type?: string;
  work_place?: string;
  experience?: string;
  min_salary?: number;
  max_salary?: number;
  page?: number;
  per_page?: number;
}

export interface JobsResponse {
  status: boolean;
  data: {
    data: Job[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
}

export interface JobDetailResponse {
  status: boolean;
  data: Job;
}

export interface SaveJobResponse {
  message: string;
  saved: boolean;
}

export interface SavedJob {
  id: number;
  job_post_id: number;
  candidate_id: number;
  job?: Job;
  created_at?: string;
}

@Injectable({
  providedIn: 'root'
})
export class JobService {
  private apiUrl = `${environment.apiUrl}/jobs`;

  constructor(private http: HttpClient) {}

  /**
   * Get all jobs with optional filters and pagination
   */
  getJobs(filters?: JobFilters): Observable<JobsResponse> {
    let params = new HttpParams();

    if (filters) {
      // Only add params that have actual values (not null/undefined/empty)
      if (filters.search) params = params.set('search', filters.search);
      if (filters.location) params = params.set('location', filters.location);
      if (filters.city_id !== undefined && filters.city_id !== null) {
        params = params.set('city_id', filters.city_id.toString());
      }
      if (filters.country_id !== undefined && filters.country_id !== null) {
        params = params.set('country_id', filters.country_id.toString());
      }
      if (filters.category_id !== undefined && filters.category_id !== null) {
        params = params.set('category_id', filters.category_id.toString());
      }
      if (filters.work_type) {
        params = params.set('work_type', filters.work_type);
      } else if (filters.type) {
        params = params.set('type', filters.type);
      }
      if (filters.work_place) {
        params = params.set('work_place', filters.work_place);
      }
      if (filters.experience) params = params.set('experience', filters.experience);
      if (filters.min_salary !== undefined && filters.min_salary !== null) {
        params = params.set('min_salary', filters.min_salary.toString());
      }
      if (filters.max_salary !== undefined && filters.max_salary !== null) {
        params = params.set('max_salary', filters.max_salary.toString());
      }
      if (filters.page) params = params.set('page', filters.page.toString());
      if (filters.per_page) params = params.set('per_page', filters.per_page.toString());
    }

    return this.http.get<JobsResponse>(this.apiUrl, { params });
  }

  /**
   * Get a single job by ID
   */
  getJob(id: number): Observable<Job> {
    return this.http.get<JobDetailResponse>(`${this.apiUrl}/${id}`).pipe(
      map(response => response.data)
    );
  }

  /**
   * Get job details with full information
   */
  getJobDetails(id: number): Observable<JobDetailResponse> {
    return this.http.get<JobDetailResponse>(`${this.apiUrl}/${id}`);
  }

  /**
   * Save or unsave a job (toggle)
   */
  saveJob(jobPostId: number): Observable<SaveJobResponse> {
    return this.http.post<SaveJobResponse>(`${this.apiUrl}/save`, {
      job_post_id: jobPostId
    });
  }

  /**
   * Get all saved jobs for the authenticated user
   */
  getSavedJobs(): Observable<{ data: SavedJob[] }> {
    return this.http.get<{ data: SavedJob[] }>(`${this.apiUrl}/saved`);
  }

  /**
   * Get all available filter options for jobs
   */
  getFilterOptions(): Observable<{ status: boolean; data: JobFilterOptions }> {
    return this.http.get<{ status: boolean; data: JobFilterOptions }>(`${this.apiUrl}/filters/options`);
  }

  /**
   * Remove a job from saved list
   */
  unsaveJob(savedJobId: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/unsave/${savedJobId}`);
  }

  /**
   * Check if a job is saved (helper method)
   */
  isJobSaved(jobId: number, savedJobs: SavedJob[]): boolean {
    return savedJobs.some(saved => saved.job_post_id === jobId);
  }

  /**
   * Format salary range for display
   */
  formatSalary(job: Job): string {
    if (job.salary_min && job.salary_max) {
      return `$${job.salary_min.toLocaleString()} - $${job.salary_max.toLocaleString()}`;
    } else if (job.salary_min) {
      return `$${job.salary_min.toLocaleString()}+`;
    } else if (job.salary_max) {
      return `Up to $${job.salary_max.toLocaleString()}`;
    }
    return 'Salary not specified';
  }

  /**
   * Get formatted job type
   */
  getJobType(job: Job): string {
    return this.getWorkType(job);
  }

  getWorkType(job: Job): string {
    const types: { [key: string]: string } = {
      'full-time': 'Full Time',
      'part-time': 'Part Time',
      'contract': 'Contract',
      'freelance': 'Freelance',
      'internship': 'Internship',
      'remote': 'Remote'
    };
    const value = job.work_type || (job as any).type;
    return types[value || ''] || value || 'Not specified';
  }

  getWorkPlace(job: Job): string {
    const workPlaces: { [key: string]: string } = {
      'on-site': 'On-site',
      'remote': 'Remote',
      'hybrid': 'Hybrid'
    };
    return workPlaces[job.work_place || ''] || job.work_place || 'Not specified';
  }

  /**
   * Format job location for display
   */
  formatJobLocation(job: Job): string {
    const location = job.locationable || (job as any).location;

    if (location?.city) {
      const city = location.city;
      const country = city.country || location.country;
      if (country) {
        return `${city.name}, ${country.name}`;
      }
      return city.name;
    }
    if (location?.country) {
      return location.country.name;
    }
    if (job.location) {
      return job.location;
    }
    return 'Location not specified';
  }
}
