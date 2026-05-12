import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, TranslateModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  form: FormGroup;
  loading = false;
  error   = '';
  currentLang = localStorage.getItem('qserve-lang') ?? 'pt';

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router,
    private translate: TranslateService
  ) {
    this.form = this.fb.group({
      email:    ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  setLang(lang: string): void {
    this.currentLang = lang;
    this.translate.use(lang);
    localStorage.setItem('qserve-lang', lang);
  }

  setLangFromEvent(event: Event): void {
    const select = event.target as HTMLSelectElement;
    this.setLang(select.value);
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;
    this.error   = '';

    this.auth.login(this.form.value).subscribe({
      next: (res) => {
        const redirects: Record<string, string> = {
          admin:     '/admin',
          attendant: '/attendant',
          student:   '/queue',
        };
        this.router.navigate([redirects[res.data.user.role] ?? '/queue']);
      },
      error: (err) => {
        this.error   = err.error?.message ?? 'Erro ao entrar';
        this.loading = false;
      }
    });
  }
}
