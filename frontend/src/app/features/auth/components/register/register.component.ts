import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, AbstractControl } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, TranslateModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent {
  form: FormGroup;
  loading = false;
  error   = '';

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router,
    private translate: TranslateService
  ) {
    this.form = this.fb.group({
      name:            ['', [Validators.required, Validators.minLength(3)]],
      email:           ['', [Validators.required, Validators.email]],
      password:        ['', [Validators.required, Validators.minLength(6)]],
      passwordConfirm: ['', Validators.required],
      role:            ['student'],
      language:        ['pt'],
    }, { validators: this.passwordMatch });
  }

  passwordMatch(group: AbstractControl) {
    const pass    = group.get('password')?.value;
    const confirm = group.get('passwordConfirm')?.value;
    return pass === confirm ? null : { mismatch: true };
  }

  setLang(lang: string): void {
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

    const { passwordConfirm, ...data } = this.form.value;

    this.auth.register(data).subscribe({
      next: (res) => {
        const target = res.data.user.role === 'admin' ? '/admin' : '/queue';
        this.router.navigate([target]);
      },
      error: (err) => {
        this.error   = err.error?.message ?? 'Erro ao registar';
        this.loading = false;
      }
    });
  }
}
