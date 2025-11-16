import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import {environment} from '../../environments/environment';

export interface Category {
  id: number;
  name: string;
  slug?: string;
  jobs_count?: number;
  created_at?: string;
  updated_at?: string;
}

export interface CategoriesResponse {
  status: boolean;
  data: Category[];
}

@Injectable({
  providedIn: 'root'
})
export class CategoryService {
  private apiUrl = `${environment.apiUrl}/categories`;

  constructor(private http: HttpClient) {}

  /**
   * Get all categories with job counts
   */
  getCategories(): Observable<Category[]> {
    return this.http.get<CategoriesResponse>(this.apiUrl).pipe(
      map(response => response.data)
    );
  }
}
