import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface CompanySearchResult {
  id: number;
  user_id?: number;
  company_name: string;
  website?: string;
  industry?: string;
  about?: string;
  logo_url?: string;
  user?: {
    id: number;
    name: string;
    email: string;
    avatar: string;
  };
  jobs_count?: number;
  average_rating?: number;
  total_reviews?: number;
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

export interface CompanySearchFilters {
  search?: string;
  country_id?: number;
  city_id?: number;
  industry?: string;
  page?: number;
  per_page?: number;
}

export interface CompanySearchResponse {
  success: boolean;
  data: CompanySearchResult[];
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
export interface Industry {
  value: string;
  name: string;
  count: number;
}

export interface Country {
  id: number;
  name: string;
  code?: string;
  companies_count?: number;
}

export interface City {
  id: number;
  name: string;
  country_id: number;
  country?: {
    id: number;
    name: string;
    code?: string;
  };
  companies_count?: number;
}

export interface CompanyFilterOptions {
  industries: Industry[];
  countries: Country[];
  cities: City[];
}

export interface CompanyFilterOptionsResponse {
  status: boolean;
  data: CompanyFilterOptions;
}

@Injectable({
  providedIn: 'root'
})
export class CompanySearchService {
  private searchApiUrl = 'http://127.0.0.1:8000/api/companies/search';
  private filterOptionsApiUrl = 'http://127.0.0.1:8000/api/companies/filter-options';

  constructor(private http: HttpClient) {}

  /**
   * Search companies with filters
   */
  searchCompanies(filters?: CompanySearchFilters): Observable<CompanySearchResponse> {
    let params = new HttpParams();

    if (filters) {
      if (filters.search) params = params.set('search', filters.search);
      if (filters.country_id !== undefined && filters.country_id !== null) {
        params = params.set('country_id', filters.country_id.toString());
      }
      if (filters.city_id !== undefined && filters.city_id !== null) {
        params = params.set('city_id', filters.city_id.toString());
      }
      if (filters.industry) params = params.set('industry', filters.industry);
      if (filters.page) params = params.set('page', filters.page.toString());
      if (filters.per_page) params = params.set('per_page', filters.per_page.toString());
    }

    return this.http.get<CompanySearchResponse>(this.searchApiUrl, { params });
  }

  /**
   * Get filter options (industries, countries, cities)
   * @param filters Optional object with selected filters to calculate counts based on them
   */
  getFilterOptions(filters?: {
    country_id?: number;
    city_id?: number;
    industry?: string;
  }): Observable<CompanyFilterOptions> {
    let params = new HttpParams();
    if (filters) {
      if (filters.country_id !== undefined && filters.country_id !== null) {
        params = params.set('selected_country_id', filters.country_id.toString());
      }
      if (filters.city_id !== undefined && filters.city_id !== null) {
        params = params.set('selected_city_id', filters.city_id.toString());
      }
      if (filters.industry) {
        params = params.set('selected_industry', filters.industry);
      }
    }
    return this.http.get<CompanyFilterOptionsResponse>(this.filterOptionsApiUrl, { params }).pipe(
      map(response => response.data)
    );
  }
}

