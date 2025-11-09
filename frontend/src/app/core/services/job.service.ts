import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export interface Job {
  id: number;
  title: string;
  company?: string;
  type: string;
  category?: string;
  level?: string;
  salary: number;
  location: string;
  description: string;
}

@Injectable({
  providedIn: 'root'
})
export class JobService {
  private apiUrl = 'http://127.0.0.1:8000/api/jobs';

  constructor(private http: HttpClient) {}

  getJobs(): Observable<Job[]> {
    return this.http.get<any>(this.apiUrl).pipe(
      map(response => {
        return response?.data?.data?.map((job: any) => ({
          id: job.id,
          title: job.title,
          company: job.employer?.name ?? 'Unknown Company',
          type: job.type,
          category: job.category?.name ?? 'Uncategorized',
          level: job.experince ?? 'N/A',
          salary: job.salary_max ?? job.salary_min ?? 0,
          location: job.location,
          description: job.description
        })) ?? [];
      })
    );
  }

  getJob(id: number): Observable<Job> {
    return this.http.get<any>(`${this.apiUrl}/${id}`).pipe(
      map(response => {
        const job = response?.data;
        return {
          id: job.id,
          title: job.title,
          company: job.employer?.name ?? 'Unknown Company',
          type: job.type,
          category: job.category?.name ?? 'Uncategorized',
          level: job.experince ?? 'N/A',
          salary: job.salary_max ?? job.salary_min ?? 0,
          location: job.location,
          description: job.description
        };
      })
    );
  }
}
