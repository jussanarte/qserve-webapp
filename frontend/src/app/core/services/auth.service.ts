import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { Router } from '@angular/router';
import { environment } from '../../../environments/environment';
import { User, UserRole, AuthResponse, LoginCredentials, RegisterData, ApiResponse } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly TOKEN_KEY = 'qserve_token';
  private readonly USER_KEY  = 'qserve_user';

  private currentUser$ = new BehaviorSubject<User | null>(this.loadUser());
  currentUser = this.currentUser$.asObservable();

  constructor(private http: HttpClient, private router: Router) {}

  login(credentials: LoginCredentials): Observable<ApiResponse<AuthResponse>> {
    return this.http
      .post<ApiResponse<AuthResponse>>(`${environment.apiUrl}/auth/login`, credentials)
      .pipe(tap(res => this.saveSession(res.data)));
  }

  register(data: RegisterData): Observable<ApiResponse<AuthResponse>> {
    return this.http
      .post<ApiResponse<AuthResponse>>(`${environment.apiUrl}/auth/register`, data)
      .pipe(tap(res => this.saveSession(res.data)));
  }

  logout(): void {
    sessionStorage.removeItem(this.TOKEN_KEY);
    sessionStorage.removeItem(this.USER_KEY);
    this.currentUser$.next(null);
    this.router.navigate(['/auth/login']);
  }

  getToken(): string | null {
    return sessionStorage.getItem(this.TOKEN_KEY);
  }

  getCurrentUser(): User | null {
    return this.currentUser$.value;
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  hasRole(role: UserRole): boolean {
    return this.getCurrentUser()?.role === role;
  }

  private saveSession(data: AuthResponse): void {
    sessionStorage.setItem(this.TOKEN_KEY, data.token);
    sessionStorage.setItem(this.USER_KEY, JSON.stringify(data.user));
    this.currentUser$.next(data.user);
  }

  private loadUser(): User | null {
    try {
      const raw = sessionStorage.getItem(this.USER_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch { return null; }
  }
}
