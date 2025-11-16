import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {environment} from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ContactService {

  private apiUrl = `${environment.apiUrl}/contact`;

  constructor(private http: HttpClient) {}

  sendMessage(data: any): Observable<any> {
    return this.http.post(this.apiUrl, {
      full_name: data.fullName,
      email: data.email,
      subject: data.subject,
      message: data.message
    });
  }

  getMessages(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

}
