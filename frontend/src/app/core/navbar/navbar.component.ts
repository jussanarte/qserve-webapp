import { Component } from '@angular/core';
import { CommonModule, AsyncPipe } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../services/auth.service';
import { ThemeService } from '../services/theme.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent {
  user$  = this.auth.currentUser;
  theme$ = this.theme.getTheme$();

  constructor(
    private auth: AuthService,
    private theme: ThemeService,
    private translate: TranslateService
  ) {}

  toggleTheme(): void { this.theme.toggle(); }

  setLang(lang: string): void {
    this.translate.use(lang);
    localStorage.setItem('qserve-lang', lang);
  }

  logout(): void { this.auth.logout(); }
}