import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class HomeService {
  private baseUrl = 'http://127.0.0.1:8000/api';

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
}
