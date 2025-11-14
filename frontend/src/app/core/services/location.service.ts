import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Country {
  id: number;
  name: string;
  code?: string;
  created_at?: string;
  updated_at?: string;
}

export interface City {
  id: number;
  name: string;
  country_id: number;
  country?: Country;
  created_at?: string;
  updated_at?: string;
}

export interface LocationResponse {
  status: boolean;
  data: Country[] | City[];
}

@Injectable({
  providedIn: 'root'
})
export class LocationService {
  private apiUrl = 'http://127.0.0.1:8000/api/locations';

  constructor(private http: HttpClient) {}

  /**
   * Get all countries
   */
  getCountries(): Observable<Country[]> {
    return this.http.get<LocationResponse>(`${this.apiUrl}/countries`).pipe(
      map(response => response.data as Country[])
    );
  }

  /**
   * Get all cities (optionally filtered by country)
   */
  getCities(countryId?: number): Observable<City[]> {
    let params = new HttpParams();
    if (countryId) {
      params = params.set('country_id', countryId.toString());
    }

    return this.http.get<LocationResponse>(`${this.apiUrl}/cities`, { params }).pipe(
      map(response => response.data as City[])
    );
  }
}
