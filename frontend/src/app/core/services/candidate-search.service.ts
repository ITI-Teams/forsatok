import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import {environment} from '../../environments/environment';

export interface CandidateSearchResult {
  id: number;
  user_id?: number;
  job_title?: string;
  phone?: string;
  education?: string;
  experience?: string;
  bio?: string;
  user?: {
    id: number;
    name: string;
    email: string;
    avatar?: string;
  };
  skills?: number[]; // Array of skill IDs
  skills_details?: Array<{
    id: number;
    name: string;
    slug?: string;
    category_id?: number;
  }>;
  location?: {
    city?: {
      id: number;
      name: string;
      country_id: number;
    };
    country?: {
      id: number;
      name: string;
      code?: string;
    };
  };
}

export interface CandidateSearchFilters {
  search?: string;
  country_id?: number;
  city_id?: number;
  skill_ids?: number | number[] | string;
  education?: string;
  experience?: string | string[];
  min_experience?: number;
  max_experience?: number;
  page?: number;
  per_page?: number;
}

export interface CandidateSearchResponse {
  success: boolean;
  data: CandidateSearchResult[];
  meta?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  message?: string;
}

// Filter Options Interfaces
export interface Skill {
  id: number;
  name: string;
  slug?: string;
  count: number;
  selected?: boolean;
}

export interface EducationLevel {
  value: string;
  name: string;
  count: number;
}

export interface ExperienceLevel {
  value: string;
  name: string;
  count: number;
  min: number;
  max: number;
}

export interface Country {
  id: number;
  name: string;
  code?: string;
  candidates_count?: number;
}

export interface City {
  id: number;
  name: string;
  country_id: number;
  country?: Country;
  candidates_count?: number;
}

export interface CandidateFilterOptions {
  skills: Skill[];
  education_levels: EducationLevel[];
  experience_levels: ExperienceLevel[];
  countries: Country[];
  cities: City[];
}

export interface CandidateFilterOptionsResponse {
  status: boolean;
  data: CandidateFilterOptions;
}

@Injectable({
  providedIn: 'root'
})
export class CandidateSearchService {
  private searchApiUrl = `${environment.apiUrl}/candidates/search`;
  private filterOptionsApiUrl = `${environment.apiUrl}/candidates/filter-options`;

  constructor(private http: HttpClient) {}

  /**
   * Search candidates with filters
   */
  searchCandidates(filters?: CandidateSearchFilters): Observable<CandidateSearchResponse> {
    let params = new HttpParams();

    if (filters) {
      if (filters.search) params = params.set('search', filters.search);
      if (filters.country_id !== undefined && filters.country_id !== null) {
        params = params.set('country_id', filters.country_id.toString());
      }
      if (filters.city_id !== undefined && filters.city_id !== null) {
        params = params.set('city_id', filters.city_id.toString());
      }
      if (filters.skill_ids) {
        if (Array.isArray(filters.skill_ids)) {
          params = params.set('skill_ids', filters.skill_ids.join(','));
        } else {
          params = params.set('skill_ids', filters.skill_ids.toString());
        }
      }
      if (filters.education) params = params.set('education', filters.education);
      if (filters.experience) {
        if (Array.isArray(filters.experience)) {
          params = params.set('experience', filters.experience.join(','));
        } else {
          params = params.set('experience', filters.experience);
        }
      }
      if (filters.min_experience !== undefined && filters.min_experience !== null) {
        params = params.set('min_experience', filters.min_experience.toString());
      }
      if (filters.max_experience !== undefined && filters.max_experience !== null) {
        params = params.set('max_experience', filters.max_experience.toString());
      }
      if (filters.page) params = params.set('page', filters.page.toString());
      if (filters.per_page) params = params.set('per_page', filters.per_page.toString());
    }

    return this.http.get<CandidateSearchResponse>(this.searchApiUrl, { params });
  }

  /**
   * Get filter options (skills, education levels, experience range)
   * @param filters Optional object with selected filters to calculate counts based on them
   */
  getFilterOptions(filters?: {
    skill_ids?: number[];
    country_id?: number;
    city_id?: number;
    education?: string;
    experience?: string;
  }): Observable<CandidateFilterOptions> {
    let params = new HttpParams();
    if (filters) {
      if (filters.skill_ids && filters.skill_ids.length > 0) {
        params = params.set('selected_skill_ids', filters.skill_ids.join(','));
      }
      if (filters.country_id !== undefined && filters.country_id !== null) {
        params = params.set('selected_country_id', filters.country_id.toString());
      }
      if (filters.city_id !== undefined && filters.city_id !== null) {
        params = params.set('selected_city_id', filters.city_id.toString());
      }
      if (filters.education) {
        params = params.set('selected_education', filters.education);
      }
      if (filters.experience) {
        if (Array.isArray(filters.experience)) {
          params = params.set('selected_experience', filters.experience.join(','));
        } else {
          params = params.set('selected_experience', filters.experience);
        }
      }
    }
    return this.http.get<CandidateFilterOptionsResponse>(this.filterOptionsApiUrl, { params }).pipe(
      map(response => response.data)
    );
  }
}
