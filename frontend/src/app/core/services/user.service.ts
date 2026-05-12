import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ApiResponse, StaffFormData, User } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class UserService {
  private readonly api = `${environment.apiUrl}/admin/attendants`;

  constructor(private http: HttpClient) {}

  attendants(): Observable<User[]> {
    return this.http.get<ApiResponse<User[]>>(this.api).pipe(map(res => res.data));
  }

  createAttendant(data: StaffFormData): Observable<User> {
    return this.http.post<ApiResponse<User>>(this.api, data).pipe(map(res => res.data));
  }

  updateAttendant(id: number, data: StaffFormData): Observable<User> {
    return this.http.put<ApiResponse<User>>(`${this.api}/${id}`, data).pipe(map(res => res.data));
  }

  deleteAttendant(id: number): Observable<null> {
    return this.http.delete<ApiResponse<null>>(`${this.api}/${id}`).pipe(map(res => res.data));
  }
}
