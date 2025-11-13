import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export interface Candidate {
  id: number;
  name: string;
  title: string;
  location: string;
  email: string;
  phone: string;
  image: string;
  salary: string;
  experience: string;
  languages: string[];
  description?: string;
  education?: Education[];
  workExperience?: Experience[];
  skills?: Skill[];
  age?: number;
  gender?: string;
  social?: {
    linkedin?: string;
    github?: string;
    twitter?: string;
    facebook?: string;
  };
}

export interface Education {
  university: string;
  years: string;
  degree: string;
  description: string;
}

export interface Experience {
  company: string;
  years: string;
  position: string;
  description: string;
}

export interface Skill {
  name: string;
  level: number;
}

@Injectable({
  providedIn: 'root'
})
export class CandidateService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  // Get all candidates
  getCandidates(page: number = 1, perPage: number = 10): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/candidatelist?page=${page}&per_page=${perPage}`).pipe(
      map(response => {
        return {
          candidates: response?.data?.map((candidate: any) => this.mapCandidate(candidate)) ?? [],
          meta: response?.meta ?? {}
        };
      })
    );
  }

  // Get single candidate
  getCandidate(id: number): Observable<Candidate> {
    return this.http.get<any>(`${this.apiUrl}/candidatelist/${id}`).pipe(
      map(response => {
        return this.mapCandidate(response?.data);
      })
    );
  }

  // Map API response to Candidate interface
  private mapCandidate(data: any): Candidate {
    return {
      id: data.id,
      name: data.user?.name ?? data.name ?? 'Unknown',
      title: data.job_title ?? data.title ?? 'N/A',
      location: data.location ?? 'N/A',
      email: data.user?.email ?? data.email ?? 'N/A',
      phone: data.phone ?? 'N/A',
      image: data.user?.profile_picture ?? data.image ?? 'https://i.pravatar.cc/300',
      salary: data.expected_salary ? `$${data.expected_salary}` : 'N/A',
      experience: data.years_of_experience ? `${data.years_of_experience} Years` : 'N/A',
      languages: data.languages ? JSON.parse(data.languages) : [],
      description: data.bio ?? data.description ?? '',
      education: data.education ? JSON.parse(data.education) : [],
      workExperience: data.work_experience ? JSON.parse(data.work_experience) : [],
      skills: data.skills ? JSON.parse(data.skills) : [],
      age: data.age ?? null,
      gender: data.gender ?? 'N/A',
      social: data.social_links ? JSON.parse(data.social_links) : {}
    };
  }
}
