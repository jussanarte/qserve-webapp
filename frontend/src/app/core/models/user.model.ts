export type UserRole = 'admin' | 'attendant' | 'student';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  language: 'pt' | 'en';
  dark_mode: boolean;
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
  language?: string;
}