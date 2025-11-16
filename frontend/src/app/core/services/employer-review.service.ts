import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';
import {environment} from '../../environments/environment';

export interface CompanyReview {
  id: number;
  company_id: number;
  candidate_id: number;
  rating: number;
  review: string;
  created_at: string;
  candidate?: {
    id: number;
    name: string;
    email: string;
  };
}

export interface ReviewSubmit {
  company_id: number;
  candidate_id: number;
  rating: number;
  review: string;
}

@Injectable({
  providedIn: 'root'
})
export class CompanyReviewService {
  private apiUrl = environment.apiUrl;

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

  // Get all reviews for a company
  getCompanyReviews(companyId: number): Observable<CompanyReview[]> {
    return this.http.get<any>(`${this.apiUrl}/company-reviews/company/${companyId}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.status === 'success') {
          return response.data;
        }
        throw new Error('Failed to load reviews');
      })
    );
  }

  // Submit a new review
  submitReview(reviewData: ReviewSubmit): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/company-reviews`, reviewData, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.status === 'success') {
          return response.data;
        }
        throw new Error(response.message || 'Failed to submit review');
      })
    );
  }

  // Update an existing review
  updateReview(reviewId: number, reviewData: Partial<ReviewSubmit>): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/company-reviews/${reviewId}`, reviewData, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.status === 'success') {
          return response.data;
        }
        throw new Error('Failed to update review');
      })
    );
  }

  // Delete a review
  deleteReview(reviewId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/company-reviews/${reviewId}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.status === 'success') {
          return response;
        }
        throw new Error('Failed to delete review');
      })
    );
  }

  // Calculate average rating from reviews
  calculateAverageRating(reviews: CompanyReview[]): number {
    if (!reviews || reviews.length === 0) return 0;
    const sum = reviews.reduce((acc, review) => acc + review.rating, 0);
    return Math.round((sum / reviews.length) * 10) / 10;
  }
}
