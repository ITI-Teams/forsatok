import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';

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
  bio?: string;
  education?: Education[];
  workExperience?: Experience[];
  skills?: Skill[];
  resume?: string;
}

export interface Education {
  university: string;
  degree: string;
  years: string;
  description: string;
}

export interface Experience {
  company: string;
  position: string;
  years: string;
  description: string;
}

export interface Skill {
  name: string;
  level: number;
}

export interface ContactMessage {
  name: string;
  email: string;
  message: string;
  contactable_id?: number;
  contactable_type?: string;
}


@Injectable({
  providedIn: 'root'
})
export class CandidateProfileService {
  private apiUrl = 'http://localhost:8000/api';

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

  // Get single candidate by ID
  getCandidate(id: number): Observable<Candidate> {
    return this.http.get<any>(`${this.apiUrl}/auth/candidatelist/${id}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success && response.data) {
          const data = response.data;

          // Transform API data to match frontend interface
          return {
            id: data.id,
            name: data.user?.name || 'Unknown',
            email: data.user?.email || '',
            phone: data.phone || '',
            title: 'Candidate', // Default title, can be updated later
            location: 'Location TBD', // Add when available in migration
            image: data.profile_image || 'https://i.pravatar.cc/300',
            salary: 'Negotiable', // Add when available in migration
            experience: data.experience || 'Not specified',
            languages: [], // Add when available in migration
            description: data.bio || '',
            bio: data.bio || '',
            resume: data.resume,
            education: this.parseEducation(data.education),
            workExperience: this.parseExperience(data.experience),
            skills: [] // Add when available in migration
          };
        }
        throw new Error(response.message || 'Failed to load candidate');
      })
    );
  }

  // Get all candidates with pagination
  getCandidates(page: number = 1, perPage: number = 10): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/auth/candidatelist?page=${page}&per_page=${perPage}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success) {
          return {
            candidates: response.data.map((data: any) => ({
              id: data.id,
              name: data.user?.name || 'Unknown',
              email: data.user?.email || '',
              phone: data.phone || '',
              title: 'Candidate',
              location: 'Location TBD',
              image: data.profile_image || 'https://i.pravatar.cc/300',
              experience: data.experience || 'Not specified',
              bio: data.bio || ''
            })),
            meta: response.meta
          };
        }
        throw new Error(response.message || 'Failed to load candidates');
      })
    );
  }

  // Send contact message to candidate
  sendContactMessage(contactData: ContactMessage): Observable<any> {
  return this.http.post<any>(`${this.apiUrl}/contact`, contactData, {
    headers: this.getHeaders()
  }).pipe(
    map(response => {
      if (response.success) {
        return response;
      }
      throw new Error(response.message || 'Failed to send message');
    })
  );
}

  // Parse education string to Education array
  //  Format expected: "University Name - Degree (Years); ..."
  private parseEducation(educationStr: string | null): Education[] {
    if (!educationStr) return [];

    try {
      // Try parsing as JSON first
      const parsed = JSON.parse(educationStr);
      if (Array.isArray(parsed)) return parsed;
    } catch (e) {
      // If not JSON, parse as string format
      return educationStr.split(';').map(item => {
        const trimmed = item.trim();
        const match = trimmed.match(/(.+?)\s*-\s*(.+?)\s*\((.+?)\)/);
        if (match) {
          return {
            university: match[1].trim(),
            degree: match[2].trim(),
            years: match[3].trim(),
            description: ''
          };
        }
        return {
          university: trimmed,
          degree: '',
          years: '',
          description: ''
        };
      }).filter(e => e.university);
    }

    return [];
  }

  // Parse experience string to Experience array
  //  Format expected: "Company Name - Position (Years); ..."
  private parseExperience(experienceStr: string | null): Experience[] {
    if (!experienceStr) return [];

    try {
      // Try parsing as JSON first
      const parsed = JSON.parse(experienceStr);
      if (Array.isArray(parsed)) return parsed;
    } catch (e) {
      // If not JSON, parse as string format
      return experienceStr.split(';').map(item => {
        const trimmed = item.trim();
        const match = trimmed.match(/(.+?)\s*-\s*(.+?)\s*\((.+?)\)/);
        if (match) {
          return {
            company: match[1].trim(),
            position: match[2].trim(),
            years: match[3].trim(),
            description: ''
          };
        }
        return {
          company: trimmed,
          position: '',
          years: '',
          description: ''
        };
      }).filter(e => e.company);
    }

    return [];
  }
}
