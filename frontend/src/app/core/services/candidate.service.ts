import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export interface Candidate {
  id: number;
  userId: number;
  name: string;
  email: string;
  jobTitle?: string;
  phone?: string;
  education?: string;
  experience?: string;
  bio?: string;
  gender?: string;
  dateOfBirth?: string;
  resume?: string | File | null;
  skills?: {
    id: number;
    name: string;
    slug: string;
    category_id: number;
  }[];
  applications?: {
    id: number;
    job_id: number;
    status: string;
    applied_at: string;
  }[];
  applicationsCount?: number;
}

@Injectable({
  providedIn: 'root',
})
export class CandidateService {
  private baseUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  // 1️⃣ جلب كل المرشحين (اختياري)
  getCandidates(page: number = 1): Observable<Candidate[]> {
    return this.http.get<any>(`${this.baseUrl}/candidatelist?page=${page}`).pipe(
      map(response => {
        return response?.data?.data?.map((item: any) => ({
          id: item.id,
          userId: item.user_id,
          name: item.user?.name ?? 'Unknown',
          email: item.user?.email ?? 'No Email',
          jobTitle: item.job_title ?? '',
          phone: item.phone ?? '',
          education: item.education ?? '',
          experience: item.experience ?? '',
          bio: item.bio ?? '',
          gender: item.gender ?? '',
          dateOfBirth: item.date_of_birth ?? '',
          resume: item.resume_url ? `http://127.0.0.1:8000${item.resume_url}` : null,
          skills: item.skills ?? [],
          applications: item.applications ?? [],
          applicationsCount: item.applications_count ?? 0
        })) ?? [];
      })
    );
  }

  // 2️⃣ جلب مرشح واحد بالتفصيل
  getCandidateById(id: number): Observable<Candidate> {
    return this.http.get<any>(`${this.baseUrl}/candidatelist/${id}`).pipe(
      map(response => {
        const item = response?.data;
        return {
          id: item.id,
          userId: item.user_id,
          name: item.user?.name ?? 'Unknown',
          email: item.user?.email ?? 'No Email',
          jobTitle: item.job_title ?? '',
          phone: item.phone ?? '',
          education: item.education ?? '',
          experience: item.experience ?? '',
          bio: item.bio ?? '',
          gender: item.gender ?? '',
          dateOfBirth: item.date_of_birth ?? '',
          resume: item.resume_url ? `http://127.0.0.1:8000${item.resume_url}` : null,
          skills: item.skills ?? [],
          applications: item.applications ?? [],
          applicationsCount: item.applications_count ?? 0
        };
      })
    );
  }

  // 3️⃣ جلب البروفايل الحالي للمرشح
  getMyProfile(): Observable<Candidate> {
    return this.http.get<any>(`${this.baseUrl}/candidate/info`).pipe(
      map(response => {
        const item = response?.data;
        return {
          id: item.id,
          userId: item.user_id,
          name: item.user?.name ?? 'Unknown',
          email: item.user?.email ?? 'No Email',
          jobTitle: item.job_title ?? '',
          phone: item.phone ?? '',
          education: item.education ?? '',
          experience: item.experience ?? '',
          bio: item.bio ?? '',
          gender: item.gender ?? '',
          dateOfBirth: item.date_of_birth ?? '',
          resume: item.resume_url ? `http://127.0.0.1:8000${item.resume_url}` : null,
          skills: item.skills ?? [],
          applications: item.applications ?? [],
          applicationsCount: item.applications_count ?? 0
        };
      })
    );
  }

  // 4️⃣ تحديث البروفايل الحالي للمرشح
  updateProfile(formData: FormData): Observable<any> {
    return this.http.post(`${this.baseUrl}/candidate/info`, formData);
  }

  // 5️⃣ جلب كل المهارات (اختياري، لو عندك endpoint skills)
  getAllSkills(): Observable<{id:number, name:string}[]> {
  return this.http.get<any>(`${this.baseUrl}/skills`).pipe(
    map(response => response.data ?? [])
  );
}
}
