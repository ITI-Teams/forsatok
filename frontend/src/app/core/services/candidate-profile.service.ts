import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthService } from './auth.service';

export interface Candidate {
  id: number;
  name: string;
  title: string;
  location: Location | null;
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
  category_id?: number;
  user_id?: number;
}

export interface Location {
  country_id?: number;
  city_id?: number;
  address?: string;
  country?: {
    id: number;
    name: string;
    code?: string;
  };
  city?: {
    id: number;
    name: string;
  };
  full_location?: string;
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
  id: number;
  name: string;
  level?: number;
}

export interface ContactMessage {
  full_name: string;
  email: string;
  subject?: string;
  message: string;
  contactable_id?: number;
  contactable_type?: string;
  user_id?: number;
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


             // Handle image URL
        let imageUrl = 'https://i.pravatar.cc/300'; // default
        if (data.user?.avatar) {
          // If avatar is a full URL
          if (data.user.avatar.startsWith('http')) {
            imageUrl = data.user.avatar;
          } else {
            // If avatar is a relative path, prepend your storage URL
            imageUrl = `http://localhost:8000/storage/${data.user.avatar}`;
          }
        }

          // Transform API data to match frontend interface
          return {
            id: data.id,
            user_id: data.user_id,
            name: data.user?.name || 'Unknown',
            email: data.user?.email || '',
            phone: data.phone || '',
            title: data.job_title || 'Candidate',
            location: data.location || null,
            image:imageUrl,
            salary: 'Negotiable',
            experience: data.experience || 'Not specified',
            languages: [],
            description: data.bio || '',
            bio: data.bio || '',
            resume: data.resume,
            education: this.parseEducation(data.education),
            workExperience: this.parseExperience(data.experience),
            skills: data.skills_details || [],
            category_id: data.category_id
          };
        }
        throw new Error(response.message || 'Failed to load candidate');
      })
    );
  }

  // Get all candidates with pagination
  // getCandidates(page: number = 1, perPage: number = 10): Observable<any> {
  //   return this.http.get<any>(`${this.apiUrl}/auth/candidatelist?page=${page}&per_page=${perPage}`, {
  //     headers: this.getHeaders()
  //   }).pipe(
  //     map(response => {
  //       if (response.success) {
  //         return {
  //           candidates: response.data.map((data: any) => ({
  //             id: data.id,
  //             name: data.user?.name || 'Unknown',
  //             email: data.user?.email || '',
  //             phone: data.phone || '',
  //             title: data.job_title || 'Candidate',
  //             location: data.location || null,
  //             image: data.user?.avatar || 'https://i.pravatar.cc/300',
  //             experience: data.experience || 'Not specified',
  //             bio: data.bio || '',
  //             skills: data.skills_details || []
  //           })),
  //           meta: response.meta
  //         };
  //       }
  //       throw new Error(response.message || 'Failed to load candidates');
  //     })
  //   );
  // }


   // Get candidate's skills
  getCandidateSkills(candidateId: number): Observable<Skill[]> {
    return this.http.get<any>(`${this.apiUrl}/auth/candidatelist/${candidateId}`, {
      headers: this.getHeaders()
    }).pipe(
      map(response => {
        if (response.success && response.data) {
          return response.data.skills_details || [];
        }
        return [];
      })
    );
  }

  // Send contact message to candidate
  sendContactMessage(contactData: ContactMessage): Observable<any> {
    const payload = {
      ...contactData,
      contactable_type: 'App\\Domains\\Users\\Models\\User'
    };

    return this.http.post<any>(`${this.apiUrl}/contact`, payload, {
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

  // Helper method to get full location string
  getLocationString(location: Location | null): string {
    if (!location) return 'Location not specified';

    if (location.full_location) {
      return location.full_location;
    }

    const parts = [];
    if (location.address) parts.push(location.address);
    if (location.city?.name) parts.push(location.city.name);
    if (location.country?.name) parts.push(location.country.name);

    return parts.length > 0 ? parts.join(', ') : 'Location not specified';
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
