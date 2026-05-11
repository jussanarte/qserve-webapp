import { Component, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { TranslateService } from '@ngx-translate/core';
import { NavbarComponent } from './layout/navbar/navbar.component';
import { AuthService } from './core/services/auth.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, CommonModule, NavbarComponent],
  template: `
    @if (auth.isAuthenticated()) {
      <app-navbar />
    }
    <router-outlet />
  `
})
export class App implements OnInit {
  constructor(public auth: AuthService, private translate: TranslateService) {}

  ngOnInit(): void {
    const saved = localStorage.getItem('qserve-lang') ?? 'pt';
    this.translate.use(saved);
  }
}