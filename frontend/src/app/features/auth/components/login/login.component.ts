import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  form: FormGroup;
  loading = false;
  error = '';

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router
  ) {
    this.form = this.fb.group({
      email:    ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;
    this.error = '';

    this.auth.login(this.form.value).subscribe({
      next: (res) => {
        const role = res.data.user.role;
        const routes: Record<string, string> = {
          admin:     '/admin',
          attendant: '/attendant',
          student:   '/queue',
        };
        this.router.navigate([routes[role] ?? '/queue']);
      },
      error: (err) => {
        this.error = err.error?.message ?? 'Erro ao entrar';
        this.loading = false;
      }
    });
  }
}