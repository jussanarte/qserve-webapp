import { Component, inject } from '@angular/core';
import { CommonModule, AsyncPipe } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../core/services/auth.service';
import { ThemeService } from '../../core/services/theme.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent {
  private auth = inject(AuthService);
  private theme = inject(ThemeService);
  private translate = inject(TranslateService);

  user$  = this.auth.currentUser;
  theme$ = this.theme.getTheme$();

  toggleTheme(): void { this.theme.toggle(); }

  setLang(lang: string): void {
    this.translate.use(lang);
    localStorage.setItem('qserve-lang', lang);
  }

  logout(): void { this.auth.logout(); }
}
