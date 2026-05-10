import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({ providedIn: 'root' })
export class RoleGuard implements CanActivate {
  constructor(private auth: AuthService, private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot): boolean {
    const required: string[] = route.data['roles'] ?? [];
    const user = this.auth.getCurrentUser();
    if (user && required.includes(user.role)) return true;
    this.router.navigate(['/queue']);
    return false;
  }
}