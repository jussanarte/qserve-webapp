export type UserRole = 'admin' | 'attendant' | 'student';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  language: 'pt' | 'en';
  dark_mode: boolean;
  is_active: boolean;
  created_at?: string;
}

export interface AuthResponse {
  user: User;
  token: string;
  message: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  role?: 'student' | 'admin';
  language?: string;
}

export interface StaffFormData {
  name: string;
  email: string;
  password?: string;
  language: 'pt' | 'en';
  is_active?: boolean;
}

export interface ForgotPasswordResponse {
  reset_token: string;
  expires_at: string;
}

export interface ResetPasswordData {
  token: string;
  password: string;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}
