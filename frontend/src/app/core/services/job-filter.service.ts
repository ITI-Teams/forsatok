import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface JobType {
  value: string;
  name: string;
  count: number;
  selected?: boolean;
}

export interface ExperienceLevel {
  value: string;
  name: string;
  count: number;
  selected?: boolean;
}

export interface SalaryRange {
  min: number;
  max: number;
}

export interface FilterOptions {
  types: JobType[];
  experience_levels: ExperienceLevel[];
  work_places: JobType[];
  salary_range: SalaryRange;
}

export interface FilterOptionsResponse {
  status: boolean;
  data: FilterOptions;
}

@Injectable({
  providedIn: 'root'
})
export class JobFilterService {
  private apiUrl = 'http://127.0.0.1:8000/api/jobs/filter-options';

  constructor(private http: HttpClient) {}

  /**
   * Get all available filter options
   */
  getFilterOptions(): Observable<FilterOptions> {
    return this.http.get<FilterOptionsResponse>(this.apiUrl).pipe(
      map(response => response.data)
    );
  }
}
