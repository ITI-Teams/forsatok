import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import {environment} from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class HomeService {
  private baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getHomeData(): Observable<{
    jobs: any[];
    top_cities: any[];
    candidates_carousel: any[];
  }> {
    return this.http.get<{ status: boolean; data: any }>(`${this.baseUrl}/home`).pipe(
      map((res) => res.data)
    );
  }

  saveJob(jobId: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/jobs/save`, { job_post_id: jobId });
  }

  getSavedJobs(): Observable<any> {
    return this.http.get(`${this.baseUrl}/jobs/saved`);
  }

  unsaveJob(jobId: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/jobs/unsave/${jobId}`);
  }

}
