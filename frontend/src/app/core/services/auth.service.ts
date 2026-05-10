import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { Router } from '@angular/router';
import { environment } from '../../../environment/environment';
import { User, AuthResponse, LoginCredentials, RegisterData, UserRole } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly TOKEN_KEY = 'qserve_token';
  private currentUserSubject = new BehaviorSubject<User | null>(this.loadUser());
  currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient, private router: Router) {}

  login(credentials: LoginCredentials): Observable<{ success: boolean; data: AuthResponse }> {
    return this.http.post<{ success: boolean; data: AuthResponse }>(
      `${environment.apiUrl}/auth/login`, credentials
    ).pipe(
      tap(res => this.saveSession(res.data))
    );
  }

  register(data: RegisterData): Observable<{ success: boolean; data: AuthResponse }> {
    return this.http.post<{ success: boolean; data: AuthResponse }>(
      `${environment.apiUrl}/auth/register`, data
    ).pipe(
      tap(res => this.saveSession(res.data))
    );
  }

  logout(): void {
    sessionStorage.removeItem(this.TOKEN_KEY);
    sessionStorage.removeItem('qserve_user');
    this.currentUserSubject.next(null);
    this.router.navigate(['/auth/login']);
  }

  getToken(): string | null {
    return sessionStorage.getItem(this.TOKEN_KEY);
  }

  getCurrentUser(): User | null {
    return this.currentUserSubject.value;
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  hasRole(role: UserRole): boolean {
    return this.getCurrentUser()?.role === role;
  }

  private saveSession(data: AuthResponse): void {
    sessionStorage.setItem(this.TOKEN_KEY, data.token);
    sessionStorage.setItem('qserve_user', JSON.stringify(data.user));
    this.currentUserSubject.next(data.user);
  }

  private loadUser(): User | null {
    try {
      const raw = sessionStorage.getItem('qserve_user');
      return raw ? JSON.parse(raw) : null;
    } catch { return null; }
  }
}